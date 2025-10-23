<?php

namespace App\Controllers;

use App\Services\LinkService;
use App\Services\TrackerService;
use App\Services\ViewRenderer;
use App\Services\FileUploadService;
use App\Services\OpenGraphService;
use App\Repositories\LinkRepository;
use App\Repositories\TrackerRepository;
use App\Database\DatabaseConnection;
use App\Config\DatabaseConfig;
use App\Config\AppConfig;

class AdminController
{
  private $linkService;
  private $trackerService;
  private $viewRenderer;
  private $fileUploadService;
  private $openGraphService;

  public function __construct()
  {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    // Load environment variables
    new AppConfig();

    // Initialize database connection
    $dbConfig = new DatabaseConfig();
    $dbConnection = DatabaseConnection::getInstance($dbConfig);

    // Initialize repositories
    $linkRepository = new LinkRepository($dbConnection);
    $trackerRepository = new TrackerRepository($dbConnection);

    // Initialize utility services
    $userAgentParser = new \App\Utils\UserAgentParser();
    $ipGeolocation = new \App\Utils\IpGeolocation();

    // Initialize services
    $this->trackerService = new TrackerService($trackerRepository, $userAgentParser, $ipGeolocation);
    $this->linkService = new LinkService($linkRepository, $this->trackerService);
    $this->viewRenderer = new ViewRenderer();
    $this->fileUploadService = new FileUploadService();
    $this->openGraphService = new OpenGraphService();
  }

  public function dashboard()
  {
    // Check authentication
    error_log("Dashboard accessed. Session data: " . print_r($_SESSION, true));
    if (!$this->isAuthenticated()) {
      error_log("Not authenticated, showing login");
      $this->showLogin();
      return;
    }

    error_log("Authenticated, showing dashboard");

    try {
      // Get statistics
      $stats = $this->getStatistics();

      // Get recent links
      $recentLinks = $this->getRecentLinks(10);

      // Get recent visits
      $recentVisits = $this->getRecentVisits(10);

      $this->viewRenderer->render('admin/dashboard', [
        'stats' => $stats,
        'recentLinks' => $recentLinks,
        'recentVisits' => $recentVisits
      ]);
    } catch (\Exception $e) {
      $this->viewRenderer->render('admin/error', [
        'error' => 'Failed to load dashboard: ' . $e->getMessage()
      ]);
    }
  }

  public function links()
  {
    if (!$this->isAuthenticated()) {
      $this->showLogin();
      return;
    }

    try {
      $page = $_GET['page'] ?? 1;
      $searchQuery = $_GET['search'] ?? '';
      $limit = 20;
      $offset = ($page - 1) * $limit;

      $links = $this->getAllLinks($limit, $offset, $searchQuery);
      $totalLinks = $this->getTotalLinksCount($searchQuery);
      $totalPages = ceil($totalLinks / $limit);

      $this->viewRenderer->render('admin/links', [
        'links' => $links,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'totalLinks' => $totalLinks,
        'searchQuery' => $searchQuery
      ]);
    } catch (\Exception $e) {
      $this->viewRenderer->render('admin/error', [
        'error' => 'Failed to load links: ' . $e->getMessage()
      ]);
    }
  }

  public function analytics()
  {
    if (!$this->isAuthenticated()) {
      $this->showLogin();
      return;
    }

    try {
      $code = $_GET['code'] ?? null;

      if ($code) {
        // Show analytics for specific link
        $link = $this->linkService->getLinkByCode($code);
        if (!$link) {
          $this->viewRenderer->render('admin/error', [
            'error' => 'Link not found'
          ]);
          return;
        }

        $visits = $this->getVisitsByCode($code);
        $visitStats = $this->getVisitStatsByCode($code);

        $this->viewRenderer->render('admin/link_analytics', [
          'link' => $link,
          'visits' => $visits,
          'visitStats' => $visitStats
        ]);
      } else {
        // Show overall analytics
        $overallStats = $this->getOverallAnalytics();
        $topLinks = $this->getTopLinksByVisits(10);

        $this->viewRenderer->render('admin/analytics', [
          'overallStats' => $overallStats,
          'topLinks' => $topLinks
        ]);
      }
    } catch (\Exception $e) {
      $this->viewRenderer->render('admin/error', [
        'error' => 'Failed to load analytics: ' . $e->getMessage()
      ]);
    }
  }

  public function createLink()
  {
    if (!$this->isAuthenticated()) {
      $this->showLogin();
      return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      try {
        $nextUrl = $_POST['next_url'] ?? '';
        $customCode = $_POST['custom_code'] ?? '';
        $linkTitle = !empty($_POST['link_title']) ? $_POST['link_title'] : null;
        $linkExcerpt = !empty($_POST['link_excerpt']) ? $_POST['link_excerpt'] : null;
        $linkPreviewUrl = !empty($_POST['link_preview_url']) ? $_POST['link_preview_url'] : null;
        $password = $_POST['password'] ?? '';
        $redirectType = (int) ($_POST['redirect_type'] ?? 0);
        $waitSeconds = (int) ($_POST['wait_seconds'] ?? 10);
        $tag = $_POST['tag'] ?? '';
        $adsImgUrl = !empty($_POST['ads_img_url']) ? $_POST['ads_img_url'] : null;
        $adsClickUrl = !empty($_POST['ads_click_url']) ? $_POST['ads_click_url'] : null;
        $adsPromotedBy = !empty($_POST['ads_promoted_by']) ? $_POST['ads_promoted_by'] : null;

        // Handle file uploads
        try {
          // Handle preview image upload
          if (isset($_FILES['preview_image_file']) && $_FILES['preview_image_file']['error'] === UPLOAD_ERR_OK) {
            $linkPreviewUrl = $this->fileUploadService->uploadImage($_FILES['preview_image_file'], 'preview');
          } elseif (!empty($linkPreviewUrl) && strpos($linkPreviewUrl, 'data:image') === 0) {
            // Handle base64 data
            $linkPreviewUrl = $this->fileUploadService->uploadFromBase64($linkPreviewUrl, 'preview');
          }

          // Handle advertisement image upload
          if (isset($_FILES['ads_image_file']) && $_FILES['ads_image_file']['error'] === UPLOAD_ERR_OK) {
            $adsImgUrl = $this->fileUploadService->uploadImage($_FILES['ads_image_file'], 'ads');
          } elseif (!empty($adsImgUrl) && strpos($adsImgUrl, 'data:image') === 0) {
            // Handle base64 data
            $adsImgUrl = $this->fileUploadService->uploadFromBase64($adsImgUrl, 'ads');
          }
        } catch (\Exception $e) {
          $this->viewRenderer->render('admin/create_link', [
            'error' => 'File upload failed: ' . $e->getMessage()
          ]);
          return;
        }

        if (empty($nextUrl)) {
          $this->viewRenderer->render('admin/create_link', [
            'error' => 'URL is required'
          ]);
          return;
        }

        // Generate code if not provided
        $code = $customCode;
        if (empty($code)) {
          $code = $this->generateRandomCode();
        }

        // Check if code already exists
        if ($this->linkService->getLinkByCode($code)) {
          $this->viewRenderer->render('admin/create_link', [
            'error' => 'Code already exists. Please choose a different one.'
          ]);
          return;
        }

        // Create the link
        $linkData = [
          'code' => $code,
          'next_url' => $nextUrl,
          'link_title' => $linkTitle,
          'link_excerpt' => $linkExcerpt,
          'link_preview_url' => $linkPreviewUrl,
          'password' => $password,
          'redirect_type' => $redirectType,
          'wait_seconds' => $waitSeconds,
          'tag' => $tag,
          'ads_img_url' => $adsImgUrl,
          'ads_click_url' => $adsClickUrl,
          'ads_promoted_by' => $adsPromotedBy
        ];

        $link = $this->linkService->createLink($linkData);

        $this->viewRenderer->render('admin/create_link_success', [
          'link' => $link,
          'success' => 'Link created successfully!'
        ]);
        return;

      } catch (\Exception $e) {
        $this->viewRenderer->render('admin/create_link', [
          'error' => 'Failed to create link: ' . $e->getMessage()
        ]);
        return;
      }
    }

    $this->viewRenderer->render('admin/create_link');
  }

  public function deleteLink()
  {
    if (!$this->isAuthenticated()) {
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => 'Unauthorized']);
      return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'message' => 'Method not allowed']);
      return;
    }

    try {
      $code = $_POST['code'] ?? '';
      if (empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Code is required']);
        return;
      }

      $success = $this->linkService->deleteLink($code);

      if ($success) {
        echo json_encode(['success' => true, 'message' => 'Link deleted successfully']);
      } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete link']);
      }
    } catch (\Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
  }

  public function login()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = $_POST['username'] ?? '';
      $password = $_POST['password'] ?? '';

      // Debug: Log the attempt
      error_log("Login attempt: username='$username', password='$password'");

      // Simple authentication - in production, use proper password hashing
      $adminUsername = $_ENV['ADMIN_USERNAME'] ?? 'admin';
      $adminPassword = $_ENV['ADMIN_PASSWORD'] ?? 'admin123';

      if ($username === $adminUsername && $password === $adminPassword) {
        $_SESSION['admin_authenticated'] = true;
        error_log("Login successful, redirecting to /admin");

        // Use absolute URL for redirect
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $redirectUrl = $protocol . '://' . $host . '/admin';

        header('Location: ' . $redirectUrl);
        exit;
      } else {
        error_log("Login failed: Invalid credentials");
        $this->viewRenderer->render('admin/login', [
          'error' => 'Invalid credentials'
        ]);
        return;
      }
    }

    $this->showLogin();
  }

  public function logout()
  {
    session_destroy();

    // Use absolute URL for redirect
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $redirectUrl = $protocol . '://' . $host . '/admin/login';

    header('Location: ' . $redirectUrl);
    exit;
  }

  private function isAuthenticated(): bool
  {
    return isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;
  }

  private function showLogin()
  {
    $this->viewRenderer->render('admin/login');
  }

  private function generateRandomCode(int $length = 6): string
  {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';

    for ($i = 0; $i < $length; $i++) {
      $code .= $characters[rand(0, strlen($characters) - 1)];
    }

    // Check if code already exists, if so generate a new one
    if ($this->linkService->getLinkByCode($code)) {
      return $this->generateRandomCode($length);
    }

    return $code;
  }

  private function getStatistics(): array
  {
    $dbConfig = new DatabaseConfig();
    $dbConnection = DatabaseConnection::getInstance($dbConfig);
    $pdo = $dbConnection->getConnection();

    // Total links
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM links");
    $totalLinks = $stmt->fetch()['total'];

    // Total visits
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tracker");
    $totalVisits = $stmt->fetch()['total'];

    // Links created today
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM links WHERE DATE(created_at) = CURDATE()");
    $linksToday = $stmt->fetch()['total'];

    // Visits today
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tracker WHERE DATE(time_of_visit) = CURDATE()");
    $visitsToday = $stmt->fetch()['total'];

    // Average visits in last 7 days (excluding today)
    $stmt = $pdo->query("SELECT AVG(daily_visits) as avg_visits FROM (
        SELECT DATE(time_of_visit) as visit_date, COUNT(*) as daily_visits 
        FROM tracker 
        WHERE time_of_visit >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
        AND time_of_visit < CURDATE()
        GROUP BY DATE(time_of_visit)
    ) as daily_stats");
    $avgVisitsLast7Days = round($stmt->fetch()['avg_visits'] ?? 0);

    // Average redirects completed in last 7 days (excluding today)
    $stmt = $pdo->query("SELECT AVG(daily_completed) as avg_completed FROM (
        SELECT DATE(time_of_visit) as visit_date, COUNT(*) as daily_completed 
        FROM tracker 
        WHERE redirect_completed = 1 
        AND time_of_visit >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
        AND time_of_visit < CURDATE()
        GROUP BY DATE(time_of_visit)
    ) as daily_stats");
    $avgCompletedLast7Days = round($stmt->fetch()['avg_completed'] ?? 0);

    // Redirects completed
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tracker WHERE redirect_completed = 1");
    $redirectsCompleted = $stmt->fetch()['total'];

    // Redirects completed today
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tracker WHERE redirect_completed = 1 AND DATE(time_of_visit) = CURDATE()");
    $redirectsCompletedToday = $stmt->fetch()['total'];

    // Calculate completion percentage for today only
    $completionPercentageToday = $visitsToday > 0 ? round(($redirectsCompletedToday / $visitsToday) * 100, 1) : 0;

    // Calculate completion rate for last 7 days average
    $avgCompletionLast7Days = $avgVisitsLast7Days > 0 ? round(($avgCompletedLast7Days / $avgVisitsLast7Days) * 100, 1) : 0;

    // For Total Visits, show trend of visits in last 7 days vs previous 7 days
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tracker WHERE time_of_visit >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
    $visitsLast7Days = $stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tracker WHERE time_of_visit >= DATE_SUB(CURDATE(), INTERVAL 14 DAY) AND time_of_visit < DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
    $visitsPrevious7Days = $stmt->fetch()['total'];

    // If previous period has too few visits, use a more reasonable comparison
    if ($visitsPrevious7Days < 5) {
      // Compare with average of last 30 days
      $stmt = $pdo->query("SELECT AVG(daily_visits) as avg_daily FROM (
            SELECT DATE(time_of_visit) as visit_date, COUNT(*) as daily_visits 
            FROM tracker 
            WHERE time_of_visit >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
            AND time_of_visit < CURDATE()
            GROUP BY DATE(time_of_visit)
        ) as daily_stats");
      $avgDailyVisits30Days = round($stmt->fetch()['avg_daily'] ?? 0);
      $totalVisitsTrend = $this->calculateTrend($visitsLast7Days, $avgDailyVisits30Days * 7);
    } else {
      $totalVisitsTrend = $this->calculateTrend($visitsLast7Days, $visitsPrevious7Days);
    }
    $visitsTrend = $this->calculateTrend($visitsToday, $avgVisitsLast7Days);
    $redirectsTrend = $this->calculateTrend($redirectsCompletedToday, $avgCompletedLast7Days);
    $completionTrend = $this->calculateCompletionTrend($visitsToday, $avgVisitsLast7Days, $redirectsCompletedToday, $avgCompletedLast7Days);

    return [
      'totalLinks' => $totalLinks,
      'totalVisits' => $totalVisits,
      'linksToday' => $linksToday,
      'visitsToday' => $visitsToday,
      'redirectsCompleted' => $redirectsCompleted,
      'redirectsCompletedToday' => $redirectsCompletedToday,
      'completionPercentageToday' => $completionPercentageToday,
      'totalVisitsTrend' => $totalVisitsTrend,
      'visitsTrend' => $visitsTrend,
      'redirectsTrend' => $redirectsTrend,
      'completionTrend' => $completionTrend
    ];
  }

  private function getRecentLinks(int $limit): array
  {
    $dbConfig = new DatabaseConfig();
    $dbConnection = DatabaseConnection::getInstance($dbConfig);
    $pdo = $dbConnection->getConnection();

    $stmt = $pdo->prepare("
            SELECT l.*, 
                   (SELECT COUNT(*) FROM tracker t WHERE t.ref_code = l.code) as visit_count
            FROM links l 
            ORDER BY l.created_at DESC 
            LIMIT :limit
        ");
    $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  private function getRecentVisits(int $limit): array
  {
    $dbConfig = new DatabaseConfig();
    $dbConnection = DatabaseConnection::getInstance($dbConfig);
    $pdo = $dbConnection->getConnection();

    $stmt = $pdo->prepare("
            SELECT t.*, l.next_url, l.link_title
            FROM tracker t
            LEFT JOIN links l ON l.code = t.ref_code
            ORDER BY t.time_of_visit DESC 
            LIMIT :limit
        ");
    $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  private function getAllLinks(int $limit, int $offset, string $searchQuery = ''): array
  {
    $dbConfig = new DatabaseConfig();
    $dbConnection = DatabaseConnection::getInstance($dbConfig);
    $pdo = $dbConnection->getConnection();

    $whereClause = '';
    $params = [':limit' => $limit, ':offset' => $offset];

    if (!empty($searchQuery)) {
      $whereClause = "WHERE l.code LIKE :search_code OR l.next_url LIKE :search_url OR l.link_title LIKE :search_title";
      $params[':search_code'] = '%' . $searchQuery . '%';
      $params[':search_url'] = '%' . $searchQuery . '%';
      $params[':search_title'] = '%' . $searchQuery . '%';
    }

    $stmt = $pdo->prepare("
            SELECT l.*, 
                   (SELECT COUNT(*) FROM tracker t WHERE t.ref_code = l.code) as visit_count
            FROM links l 
            {$whereClause}
            ORDER BY l.created_at DESC 
            LIMIT :limit OFFSET :offset
        ");

    foreach ($params as $key => $value) {
      if ($key === ':limit' || $key === ':offset') {
        $stmt->bindValue($key, $value, \PDO::PARAM_INT);
      } else {
        $stmt->bindValue($key, $value, \PDO::PARAM_STR);
      }
    }

    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  private function getTotalLinksCount(string $searchQuery = ''): int
  {
    $dbConfig = new DatabaseConfig();
    $dbConnection = DatabaseConnection::getInstance($dbConfig);
    $pdo = $dbConnection->getConnection();

    $whereClause = '';
    $params = [];

    if (!empty($searchQuery)) {
      $whereClause = "WHERE code LIKE :search_code OR next_url LIKE :search_url OR link_title LIKE :search_title";
      $params[':search_code'] = '%' . $searchQuery . '%';
      $params[':search_url'] = '%' . $searchQuery . '%';
      $params[':search_title'] = '%' . $searchQuery . '%';
    }

    $sql = "SELECT COUNT(*) as total FROM links {$whereClause}";
    $stmt = $pdo->prepare($sql);

    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value, \PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetch()['total'];
  }

  private function getVisitsByCode(string $code): array
  {
    $dbConfig = new DatabaseConfig();
    $dbConnection = DatabaseConnection::getInstance($dbConfig);
    $pdo = $dbConnection->getConnection();

    $stmt = $pdo->prepare("
            SELECT * FROM tracker 
            WHERE BINARY ref_code = :code 
            ORDER BY time_of_visit DESC
        ");
    $stmt->bindParam(':code', $code);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  private function getVisitStatsByCode(string $code): array
  {
    $dbConfig = new DatabaseConfig();
    $dbConnection = DatabaseConnection::getInstance($dbConfig);
    $pdo = $dbConnection->getConnection();

    // Total visits
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tracker WHERE BINARY ref_code = :code");
    $stmt->bindParam(':code', $code);
    $stmt->execute();
    $totalVisits = $stmt->fetch()['total'];

    // Completed redirects
    $stmt = $pdo->prepare("SELECT COUNT(*) as completed FROM tracker WHERE BINARY ref_code = :code AND redirect_completed = 1");
    $stmt->bindParam(':code', $code);
    $stmt->execute();
    $completedRedirects = $stmt->fetch()['completed'];

    // Completion rate
    $completionRate = $totalVisits > 0 ? round(($completedRedirects / $totalVisits) * 100, 2) : 0;

    // Visits by browser
    $stmt = $pdo->prepare("
            SELECT browser, COUNT(*) as count 
            FROM tracker 
            WHERE BINARY ref_code = :code AND browser IS NOT NULL
            GROUP BY browser 
            ORDER BY count DESC
        ");
    $stmt->bindParam(':code', $code);
    $stmt->execute();
    $visitsByBrowser = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // Visits by location
    $stmt = $pdo->prepare("
            SELECT location, COUNT(*) as count 
            FROM tracker 
            WHERE BINARY ref_code = :code AND location IS NOT NULL
            GROUP BY location 
            ORDER BY count DESC
        ");
    $stmt->bindParam(':code', $code);
    $stmt->execute();
    $visitsByLocation = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // Visits by referrer
    $stmt = $pdo->prepare("
            SELECT ref_url, COUNT(*) as count 
            FROM tracker 
            WHERE BINARY ref_code = :code AND ref_url IS NOT NULL AND ref_url != 'Unknown' AND ref_url != 'Direct visit'
            GROUP BY ref_url 
            ORDER BY count DESC
        ");
    $stmt->bindParam(':code', $code);
    $stmt->execute();
    $visitsByReferrer = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // Visits by ISP
    $stmt = $pdo->prepare("
            SELECT isp, COUNT(*) as count 
            FROM tracker 
            WHERE BINARY ref_code = :code AND isp IS NOT NULL AND isp != ''
            GROUP BY isp 
            ORDER BY count DESC
        ");
    $stmt->bindParam(':code', $code);
    $stmt->execute();
    $visitsByIsp = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    return [
      'totalVisits' => $totalVisits,
      'completedRedirects' => $completedRedirects,
      'completionRate' => $completionRate,
      'visitsByBrowser' => $visitsByBrowser,
      'visitsByLocation' => $visitsByLocation,
      'visitsByReferrer' => $visitsByReferrer,
      'visitsByIsp' => $visitsByIsp
    ];
  }

  private function getOverallAnalytics(): array
  {
    $dbConfig = new DatabaseConfig();
    $dbConnection = DatabaseConnection::getInstance($dbConfig);
    $pdo = $dbConnection->getConnection();

    // Visits by day (last 30 days)
    $stmt = $pdo->query("
            SELECT DATE(time_of_visit) as date, COUNT(*) as visits
            FROM tracker 
            WHERE time_of_visit >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(time_of_visit)
            ORDER BY date DESC
        ");
    $visitsByDay = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // Top browsers
    $stmt = $pdo->query("
            SELECT browser, COUNT(*) as count 
            FROM tracker 
            WHERE browser IS NOT NULL
            GROUP BY browser 
            ORDER BY count DESC 
            LIMIT 10
        ");
    $topBrowsers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // Top locations
    $stmt = $pdo->query("
            SELECT location, COUNT(*) as count 
            FROM tracker 
            WHERE location IS NOT NULL
            GROUP BY location 
            ORDER BY count DESC 
            LIMIT 10
        ");
    $topLocations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // Top referrers
    $stmt = $pdo->query("
            SELECT ref_url, COUNT(*) as count 
            FROM tracker 
            WHERE ref_url IS NOT NULL AND ref_url != 'Unknown' AND ref_url != 'Direct visit'
            GROUP BY ref_url 
            ORDER BY count DESC 
            LIMIT 10
        ");
    $topReferrers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    return [
      'visitsByDay' => $visitsByDay,
      'topBrowsers' => $topBrowsers,
      'topLocations' => $topLocations,
      'topReferrers' => $topReferrers
    ];
  }

  private function getTopLinksByVisits(int $limit): array
  {
    $dbConfig = new DatabaseConfig();
    $dbConnection = DatabaseConnection::getInstance($dbConfig);
    $pdo = $dbConnection->getConnection();

    $stmt = $pdo->prepare("
            SELECT l.*, COUNT(t.id) as visit_count
            FROM links l
            LEFT JOIN tracker t ON t.ref_code = l.code
            GROUP BY l.id
            ORDER BY visit_count DESC
            LIMIT :limit
        ");
    $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function editLink()
  {
    // Check authentication
    if (!isset($_SESSION['admin_authenticated']) || !$_SESSION['admin_authenticated']) {
      header('Location: /admin/login');
      exit;
    }

    $code = $_GET['code'] ?? '';
    if (empty($code)) {
      $this->viewRenderer->render('admin/error', ['message' => 'Link code is required']);
      return;
    }

    $link = $this->linkService->getLinkByCode($code);
    if (!$link) {
      $this->viewRenderer->render('admin/error', ['message' => 'Link not found']);
      return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      try {
        $nextUrl = $_POST['next_url'] ?? '';
        $linkTitle = !empty($_POST['link_title']) ? $_POST['link_title'] : null;
        $linkExcerpt = !empty($_POST['link_excerpt']) ? $_POST['link_excerpt'] : null;
        $linkPreviewUrl = !empty($_POST['link_preview_url']) ? $_POST['link_preview_url'] : null;
        $password = $_POST['password'] ?? '';
        $redirectType = (int) ($_POST['redirect_type'] ?? 0);
        $waitSeconds = (int) ($_POST['wait_seconds'] ?? 10);
        $tag = $_POST['tag'] ?? '';
        $adsImgUrl = !empty($_POST['ads_img_url']) ? $_POST['ads_img_url'] : null;
        $adsClickUrl = !empty($_POST['ads_click_url']) ? $_POST['ads_click_url'] : null;
        $adsPromotedBy = !empty($_POST['ads_promoted_by']) ? $_POST['ads_promoted_by'] : null;

        // Handle file uploads
        try {
          // Handle preview image upload
          if (isset($_FILES['preview_image_file']) && $_FILES['preview_image_file']['error'] === UPLOAD_ERR_OK) {
            $linkPreviewUrl = $this->fileUploadService->uploadImage($_FILES['preview_image_file'], 'preview');
          } elseif (!empty($linkPreviewUrl) && strpos($linkPreviewUrl, 'data:image') === 0) {
            $linkPreviewUrl = $this->fileUploadService->uploadFromBase64($linkPreviewUrl, 'preview');
          }

          // Handle advertisement image upload
          if (isset($_FILES['ads_image_file']) && $_FILES['ads_image_file']['error'] === UPLOAD_ERR_OK) {
            $adsImgUrl = $this->fileUploadService->uploadImage($_FILES['ads_image_file'], 'ads');
          } elseif (!empty($adsImgUrl) && strpos($adsImgUrl, 'data:image') === 0) {
            $adsImgUrl = $this->fileUploadService->uploadFromBase64($adsImgUrl, 'ads');
          }
        } catch (\Exception $e) {
          $this->viewRenderer->render('admin/edit_link', [
            'link' => $link,
            'error' => 'File upload failed: ' . $e->getMessage()
          ]);
          return;
        }

        if (empty($nextUrl)) {
          $this->viewRenderer->render('admin/edit_link', [
            'link' => $link,
            'error' => 'URL is required'
          ]);
          return;
        }

        // Update link data
        $link->setNextUrl($nextUrl);
        $link->setLinkTitle($linkTitle);
        $link->setLinkExcerpt($linkExcerpt);
        $link->setLinkPreviewUrl($linkPreviewUrl);
        $link->setPassword($password);
        $link->setRedirectType($redirectType);
        $link->setWaitSeconds($waitSeconds);
        $link->setTag($tag);
        $link->setAdsImgUrl($adsImgUrl);
        $link->setAdsClickUrl($adsClickUrl);
        $link->setAdsPromotedBy($adsPromotedBy);

        $this->linkService->updateLink($link);

        $this->viewRenderer->render('admin/edit_link_success', [
          'link' => $link,
          'success' => 'Link updated successfully!'
        ]);
        return;

      } catch (\Exception $e) {
        $this->viewRenderer->render('admin/edit_link', [
          'link' => $link,
          'error' => 'Failed to update link: ' . $e->getMessage()
        ]);
        return;
      }
    }

    $this->viewRenderer->render('admin/edit_link', ['link' => $link]);
  }

  public function extractOpenGraph()
  {
    // Set server timeout to prevent hanging
    set_time_limit(20); // 20 second timeout

    // Check authentication
    if (!isset($_SESSION['admin_authenticated']) || !$_SESSION['admin_authenticated']) {
      http_response_code(401);
      echo json_encode(['error' => 'Unauthorized']);
      return;
    }

    $url = $_POST['url'] ?? '';
    if (empty($url)) {
      http_response_code(400);
      echo json_encode(['error' => 'URL is required']);
      return;
    }

    // Validate URL
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid URL']);
      return;
    }

    try {
      $ogTags = $this->openGraphService->extractOpenGraphTags($url);

      // Check if we got any meaningful data
      $hasData = false;
      foreach ($ogTags as $key => $value) {
        if ($value && $key !== 'url' && strlen(trim($value)) > 0) {
          $hasData = true;
          break;
        }
      }

      if (!$hasData) {
        // Provide fallback data
        $ogTags['title'] = parse_url($url, PHP_URL_HOST) ?: 'Website';
        $ogTags['description'] = 'No Open Graph data found for this URL';
        $ogTags['site_name'] = parse_url($url, PHP_URL_HOST) ?: 'Website';
      }

      echo json_encode(['success' => true, 'data' => $ogTags]);
    } catch (\Exception $e) {
      // Log the error for debugging
      error_log("AdminController extractOpenGraph error: " . $e->getMessage());

      // Provide fallback data even on error
      $fallbackData = [
        'title' => parse_url($url, PHP_URL_HOST) ?: 'Website',
        'description' => 'Unable to extract Open Graph data',
        'site_name' => parse_url($url, PHP_URL_HOST) ?: 'Website',
        'url' => $url
      ];

      echo json_encode(['success' => true, 'data' => $fallbackData, 'warning' => 'Limited data extracted due to: ' . $e->getMessage()]);
    }
  }

  public function autoShortenUrl()
  {
    $url = $_GET['url'] ?? '';
    if (empty($url)) {
      http_response_code(400);
      echo json_encode(['error' => 'URL parameter is required']);
      return;
    }

    // Validate URL
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid URL']);
      return;
    }

    try {
      // Check if link already exists
      $existingLink = $this->linkService->getLinkByNextUrl($url);
      if ($existingLink) {
        // Redirect to existing short link
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $redirectUrl = $protocol . '://' . $host . '/' . $existingLink->getCode();
        header('Location: ' . $redirectUrl);
        exit;
      }

      // Parse additional parameters
      $redirectType = (int) ($_GET['type'] ?? 0);
      $waitSeconds = (int) ($_GET['wait'] ?? 10);
      $linkTitle = !empty($_GET['title']) ? $_GET['title'] : null;
      $linkExcerpt = !empty($_GET['excerpt']) ? $_GET['excerpt'] : null;
      $linkPreviewUrl = !empty($_GET['preview_url']) ? $_GET['preview_url'] : null;
      $password = $_GET['password'] ?? '';
      $tag = $_GET['tag'] ?? '';
      $adsImgUrl = !empty($_GET['ads_img_url']) ? $_GET['ads_img_url'] : null;
      $adsClickUrl = !empty($_GET['ads_click_url']) ? $_GET['ads_click_url'] : null;
      $adsPromotedBy = !empty($_GET['ads_promoted_by']) ? $_GET['ads_promoted_by'] : null;

      // Generate random code
      $code = $this->generateRandomCode();

      // Create the link
      $linkData = [
        'code' => $code,
        'next_url' => $url,
        'link_title' => $linkTitle,
        'link_excerpt' => $linkExcerpt,
        'link_preview_url' => $linkPreviewUrl,
        'password' => $password,
        'redirect_type' => $redirectType,
        'wait_seconds' => $waitSeconds,
        'tag' => $tag,
        'ads_img_url' => $adsImgUrl,
        'ads_click_url' => $adsClickUrl,
        'ads_promoted_by' => $adsPromotedBy
      ];

      $link = $this->linkService->createLink($linkData);

      // Redirect to new short link
      $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
      $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
      $redirectUrl = $protocol . '://' . $host . '/' . $link->getCode();
      header('Location: ' . $redirectUrl);
      exit;

    } catch (\Exception $e) {
      http_response_code(500);
      echo json_encode(['error' => 'Failed to create short link: ' . $e->getMessage()]);
    }
  }

  public function batchShortenUrls()
  {
    if (!$this->isAuthenticated()) {
      $this->showLogin();
      return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      try {
        $urlsText = $_POST['urls'] ?? '';
        $defaultRedirectType = (int) ($_POST['default_redirect_type'] ?? 0);
        $defaultWaitSeconds = (int) ($_POST['default_wait_seconds'] ?? 10);

        // Get ads settings from batch form
        $batchAdsImgUrl = !empty($_POST['batch_ads_img_url']) ? $_POST['batch_ads_img_url'] : null;
        $batchAdsClickUrl = !empty($_POST['batch_ads_click_url']) ? $_POST['batch_ads_click_url'] : null;
        $batchAdsPromotedBy = !empty($_POST['batch_ads_promoted_by']) ? $_POST['batch_ads_promoted_by'] : null;

        // Handle batch ads image upload
        try {
          if (isset($_FILES['batch_ads_image_file']) && $_FILES['batch_ads_image_file']['error'] === UPLOAD_ERR_OK) {
            $batchAdsImgUrl = $this->fileUploadService->uploadImage($_FILES['batch_ads_image_file'], 'ads');
          } elseif (!empty($batchAdsImgUrl) && strpos($batchAdsImgUrl, 'data:image') === 0) {
            // Handle base64 data
            $batchAdsImgUrl = $this->fileUploadService->uploadFromBase64($batchAdsImgUrl, 'ads');
          }
        } catch (\Exception $e) {
          $this->viewRenderer->render('admin/error', [
            'error' => 'Batch ads image upload failed: ' . $e->getMessage()
          ]);
          return;
        }

        if (empty($urlsText)) {
          $this->viewRenderer->render('admin/error', [
            'error' => 'URLs are required'
          ]);
          return;
        }

        $urls = array_filter(array_map('trim', explode("\n", $urlsText)));
        $results = [
          'successful' => [],
          'duplicates' => [],
          'errors' => []
        ];

        foreach ($urls as $index => $urlLine) {
          if (empty($urlLine))
            continue;

          try {
            // Parse URL line - support simple, advanced, and custom code format
            $parts = explode('|', $urlLine);
            $url = trim($parts[0]);

            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
              $results['errors'][] = [
                'line' => $index + 1,
                'url' => $url,
                'error' => 'Invalid URL format'
              ];
              continue;
            }

            // Determine format and parse parameters
            $customCode = '';
            $redirectType = $defaultRedirectType;
            $waitSeconds = $defaultWaitSeconds;
            $password = '';
            $tag = '';

            if (count($parts) >= 2) {
              // Check if second parameter is a number (old format) or custom code (new format)
              if (is_numeric($parts[1])) {
                // Old format: url|type|wait|password|tag
                $redirectType = isset($parts[1]) ? (int) $parts[1] : $defaultRedirectType;
                $waitSeconds = isset($parts[2]) ? (int) $parts[2] : $defaultWaitSeconds;
                $password = isset($parts[3]) ? trim($parts[3]) : '';
                $tag = isset($parts[4]) ? trim($parts[4]) : '';
              } else {
                // New format: url|customcode|type|wait|password|tag
                $customCode = trim($parts[1]);
                $redirectType = isset($parts[2]) ? (int) $parts[2] : $defaultRedirectType;
                $waitSeconds = isset($parts[3]) ? (int) $parts[3] : $defaultWaitSeconds;
                $password = isset($parts[4]) ? trim($parts[4]) : '';
                $tag = isset($parts[5]) ? trim($parts[5]) : '';
              }
            }

            // Validate custom code if provided
            if (!empty($customCode)) {
              // Check if custom code already exists
              $existingLinkByCode = $this->linkService->getLinkByCode($customCode);
              if ($existingLinkByCode) {
                $results['errors'][] = [
                  'line' => $index + 1,
                  'url' => $url,
                  'error' => 'Custom code "' . $customCode . '" already exists'
                ];
                continue;
              }
            }

            // Check if link already exists by URL
            $existingLink = $this->linkService->getLinkByNextUrl($url);
            if ($existingLink) {
              $results['duplicates'][] = [
                'line' => $index + 1,
                'url' => $url,
                'code' => $existingLink->getCode(),
                'short_url' => ($_ENV['APP_URL'] ?? 'https://tunn.ad') . '/' . $existingLink->getCode()
              ];
              continue;
            }

            // Generate code (custom or random)
            $code = !empty($customCode) ? $customCode : $this->generateRandomCode();

            // Create the link
            $linkData = [
              'code' => $code,
              'next_url' => $url,
              'link_title' => null, // Default null for batch processing
              'link_excerpt' => null, // Default null
              'link_preview_url' => null, // Default null
              'redirect_type' => $redirectType,
              'wait_seconds' => $waitSeconds,
              'password' => $password,
              'tag' => $tag,
              'ads_img_url' => $batchAdsImgUrl, // Use batch ads settings
              'ads_click_url' => $batchAdsClickUrl, // Use batch ads settings
              'ads_promoted_by' => $batchAdsPromotedBy // Use batch ads settings
            ];

            $link = $this->linkService->createLink($linkData);

            $results['successful'][] = [
              'line' => $index + 1,
              'url' => $url,
              'code' => $link->getCode(),
              'short_url' => ($_ENV['APP_URL'] ?? 'https://tunn.ad') . '/' . $link->getCode()
            ];

          } catch (\Exception $e) {
            $results['errors'][] = [
              'line' => $index + 1,
              'url' => $url,
              'error' => $e->getMessage()
            ];
          }
        }

        $this->viewRenderer->render('admin/batch_shorten_success', [
          'results' => $results,
          'total' => count($urls),
          'successful_count' => count($results['successful']),
          'duplicates_count' => count($results['duplicates']),
          'errors_count' => count($results['errors'])
        ]);

      } catch (\Exception $e) {
        $this->viewRenderer->render('admin/error', [
          'error' => 'Failed to process batch URLs: ' . $e->getMessage()
        ]);
      }
    } else {
      $this->viewRenderer->render('admin/error', [
        'error' => 'Method not allowed'
      ]);
    }
  }

  private function calculateTrend($current, $previous): array
  {
    // If both are 0, no change
    if ($current == 0 && $previous == 0) {
      return ['direction' => 'neutral', 'percentage' => 0, 'class' => 'trend-neutral'];
    }

    // If previous is 0 but current is not, show as new data
    if ($previous == 0 && $current > 0) {
      return ['direction' => 'up', 'percentage' => 100, 'class' => 'trend-up'];
    }

    // If current is 0 but previous was not, show as decline
    if ($current == 0 && $previous > 0) {
      return ['direction' => 'down', 'percentage' => 100, 'class' => 'trend-down'];
    }

    $change = (($current - $previous) / $previous) * 100;
    $percentage = round(abs($change), 1);

    // Cap the percentage at 999% to avoid extremely large numbers
    if ($percentage > 999) {
      $percentage = 999;
    }

    if ($change > 0) {
      return ['direction' => 'up', 'percentage' => $percentage, 'class' => 'trend-up'];
    } elseif ($change < 0) {
      return ['direction' => 'down', 'percentage' => $percentage, 'class' => 'trend-down'];
    } else {
      return ['direction' => 'neutral', 'percentage' => 0, 'class' => 'trend-neutral'];
    }
  }

  private function calculateCompletionTrend($visitsToday, $avgVisitsLast7Days, $completedToday, $avgCompletedLast7Days): array
  {
    $completionToday = $visitsToday > 0 ? ($completedToday / $visitsToday) * 100 : 0;
    $completionLast7Days = $avgVisitsLast7Days > 0 ? ($avgCompletedLast7Days / $avgVisitsLast7Days) * 100 : 0;

    if ($completionLast7Days == 0) {
      if ($completionToday > 0) {
        return ['direction' => 'up', 'percentage' => 100, 'class' => 'trend-up'];
      }
      return ['direction' => 'neutral', 'percentage' => 0, 'class' => 'trend-neutral'];
    }

    $change = $completionToday - $completionLast7Days;
    $percentage = round(abs($change), 1);

    // Cap the percentage at 999% to avoid extremely large numbers
    if ($percentage > 999) {
      $percentage = 999;
    }

    if ($change > 0) {
      return ['direction' => 'up', 'percentage' => $percentage, 'class' => 'trend-up'];
    } elseif ($change < 0) {
      return ['direction' => 'down', 'percentage' => $percentage, 'class' => 'trend-down'];
    } else {
      return ['direction' => 'neutral', 'percentage' => 0, 'class' => 'trend-neutral'];
    }
  }

  public function mapAnalytics()
  {
    if (!$this->isAuthenticated()) {
      $this->showLogin();
      return;
    }

    try {
      // Get location data for map visualization
      $locationData = $this->getLocationDataForMap();

      $this->viewRenderer->render('admin/map_analytics', [
        'locationData' => $locationData
      ]);
    } catch (\Exception $e) {
      $this->viewRenderer->render('admin/error', [
        'error' => 'Failed to load map analytics: ' . $e->getMessage()
      ]);
    }
  }

  private function getLocationDataForMap()
  {
    try {
      $dbConfig = new DatabaseConfig();
      $dbConnection = DatabaseConnection::getInstance($dbConfig);
      $pdo = $dbConnection->getConnection();

      // Get all unique locations with visit counts and coordinates
      $sql = "SELECT 
                location, 
                COUNT(*) as visit_count,
                GROUP_CONCAT(DISTINCT ip_address) as ip_addresses,
                MIN(time_of_visit) as first_visit,
                MAX(time_of_visit) as last_visit,
                (SELECT coordinates FROM tracker t2 WHERE t2.location = tracker.location AND t2.coordinates IS NOT NULL LIMIT 1) as coordinates
              FROM tracker 
              WHERE location IS NOT NULL 
                AND location != 'Unknown' 
                AND location != ''
              GROUP BY location 
              ORDER BY visit_count DESC";

      $stmt = $pdo->query($sql);
      $locations = [];

      while ($row = $stmt->fetch()) {
        // Parse coordinates from stored JSON data
        $coordinates = null;
        if (!empty($row['coordinates'])) {
          $coordinates = json_decode($row['coordinates'], true);
        }


        $locations[] = [
          'location' => $row['location'],
          'visit_count' => (int) $row['visit_count'],
          'ip_addresses' => explode(',', $row['ip_addresses']),
          'first_visit' => $row['first_visit'],
          'last_visit' => $row['last_visit'],
          'coordinates' => $coordinates
        ];
      }

      return $locations;
    } catch (\Exception $e) {
      error_log("Error getting location data: " . $e->getMessage());
      return [];
    }
  }

  private function getCoordinatesFromLocation($location)
  {
    // Try to get coordinates from location string
    // This is a simplified approach - in production, you might want to use a proper geocoding service
    try {
      // For now, we'll use a simple approach with ip-api.com for coordinates
      // In a real implementation, you'd want to cache these results
      $ipAddresses = $this->getIpAddressesForLocation($location);

      if (!empty($ipAddresses)) {
        $firstIp = $ipAddresses[0];
        $details = json_decode(file_get_contents("http://ip-api.com/json/{$firstIp}"));

        if (isset($details->lat) && isset($details->lon)) {
          return [
            'lat' => (float) $details->lat,
            'lng' => (float) $details->lon
          ];
        }
      }
    } catch (\Exception $e) {
      error_log("Error getting coordinates for location {$location}: " . $e->getMessage());
    }

    return null;
  }

  private function getIpAddressesForLocation($location)
  {
    try {
      $dbConfig = new DatabaseConfig();
      $dbConnection = DatabaseConnection::getInstance($dbConfig);
      $pdo = $dbConnection->getConnection();

      $sql = "SELECT DISTINCT ip_address FROM tracker WHERE location = ? LIMIT 1";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$location]);

      $ipAddresses = [];
      while ($row = $stmt->fetch()) {
        $ipAddresses[] = $row['ip_address'];
      }

      return $ipAddresses;
    } catch (\Exception $e) {
      error_log("Error getting IP addresses for location: " . $e->getMessage());
      return [];
    }
  }
}
