<?php

namespace App\Controllers;

use App\Services\LinkService;
use App\Services\TrackerService;
use App\Services\ViewRenderer;
use App\Services\FileUploadService;
use App\Repositories\LinkRepository;
use App\Repositories\TrackerRepository;
use App\Database\DatabaseConnection;
use App\Config\DatabaseConfig;
use App\Config\AppConfig;

class AdminController
{
  private LinkService $linkService;
  private TrackerService $trackerService;
  private ViewRenderer $viewRenderer;
  private FileUploadService $fileUploadService;

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
      $limit = 20;
      $offset = ($page - 1) * $limit;

      $links = $this->getAllLinks($limit, $offset);
      $totalLinks = $this->getTotalLinksCount();
      $totalPages = ceil($totalLinks / $limit);

      $this->viewRenderer->render('admin/links', [
        'links' => $links,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'totalLinks' => $totalLinks
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
        $linkTitle = $_POST['link_title'] ?? '';
        $linkExcerpt = $_POST['link_excerpt'] ?? '';
        $linkPreviewUrl = $_POST['link_preview_url'] ?? '';
        $password = $_POST['password'] ?? '';
        $redirectType = (int) ($_POST['redirect_type'] ?? 0);
        $waitSeconds = (int) ($_POST['wait_seconds'] ?? 10);
        $tag = $_POST['tag'] ?? '';
        $adsImgUrl = $_POST['ads_img_url'] ?? '';
        $adsClickUrl = $_POST['ads_click_url'] ?? '';
        $adsPromotedBy = $_POST['ads_promoted_by'] ?? '';

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

    return [
      'totalLinks' => $totalLinks,
      'totalVisits' => $totalVisits,
      'linksToday' => $linksToday,
      'visitsToday' => $visitsToday
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

  private function getAllLinks(int $limit, int $offset): array
  {
    $dbConfig = new DatabaseConfig();
    $dbConnection = DatabaseConnection::getInstance($dbConfig);
    $pdo = $dbConnection->getConnection();

    $stmt = $pdo->prepare("
            SELECT l.*, 
                   (SELECT COUNT(*) FROM tracker t WHERE t.ref_code = l.code) as visit_count
            FROM links l 
            ORDER BY l.created_at DESC 
            LIMIT :limit OFFSET :offset
        ");
    $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  private function getTotalLinksCount(): int
  {
    $dbConfig = new DatabaseConfig();
    $dbConnection = DatabaseConnection::getInstance($dbConfig);
    $pdo = $dbConnection->getConnection();

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM links");
    return $stmt->fetch()['total'];
  }

  private function getVisitsByCode(string $code): array
  {
    $dbConfig = new DatabaseConfig();
    $dbConnection = DatabaseConnection::getInstance($dbConfig);
    $pdo = $dbConnection->getConnection();

    $stmt = $pdo->prepare("
            SELECT * FROM tracker 
            WHERE ref_code = :code 
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
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tracker WHERE ref_code = :code");
    $stmt->bindParam(':code', $code);
    $stmt->execute();
    $totalVisits = $stmt->fetch()['total'];

    // Visits by browser
    $stmt = $pdo->prepare("
            SELECT browser, COUNT(*) as count 
            FROM tracker 
            WHERE ref_code = :code AND browser IS NOT NULL
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
            WHERE ref_code = :code AND location IS NOT NULL
            GROUP BY location 
            ORDER BY count DESC
        ");
    $stmt->bindParam(':code', $code);
    $stmt->execute();
    $visitsByLocation = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    return [
      'totalVisits' => $totalVisits,
      'visitsByBrowser' => $visitsByBrowser,
      'visitsByLocation' => $visitsByLocation
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

    return [
      'visitsByDay' => $visitsByDay,
      'topBrowsers' => $topBrowsers,
      'topLocations' => $topLocations
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
        $linkTitle = $_POST['link_title'] ?? '';
        $linkExcerpt = $_POST['link_excerpt'] ?? '';
        $linkPreviewUrl = $_POST['link_preview_url'] ?? '';
        $password = $_POST['password'] ?? '';
        $redirectType = (int) ($_POST['redirect_type'] ?? 0);
        $waitSeconds = (int) ($_POST['wait_seconds'] ?? 10);
        $tag = $_POST['tag'] ?? '';
        $adsImgUrl = $_POST['ads_img_url'] ?? '';
        $adsClickUrl = $_POST['ads_click_url'] ?? '';
        $adsPromotedBy = $_POST['ads_promoted_by'] ?? '';

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
}
