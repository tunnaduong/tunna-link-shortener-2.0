<?php

namespace App\Controllers;

use App\Services\LinkService;
use App\Services\RecaptchaService;
use App\Services\ViewRenderer;
use App\Services\OpenGraphService;
use App\Utils\UrlGenerator;

class LinkController
{
  private LinkService $linkService;
  private RecaptchaService $recaptchaService;
  private ViewRenderer $viewRenderer;
  private UrlGenerator $urlGenerator;
  private OpenGraphService $openGraphService;

  public function __construct(
    LinkService $linkService,
    RecaptchaService $recaptchaService,
    ViewRenderer $viewRenderer,
    UrlGenerator $urlGenerator
  ) {
    $this->linkService = $linkService;
    $this->recaptchaService = $recaptchaService;
    $this->viewRenderer = $viewRenderer;
    $this->urlGenerator = $urlGenerator;
    $this->openGraphService = new OpenGraphService();
  }

  public function showLink(string $code): void
  {
    $result = $this->linkService->handleLinkRedirect($code);

    if (!$result['success']) {
      $this->viewRenderer->render('404', [
        'title' => 'Link Not Found',
        'message' => 'The requested link was not found.'
      ]);
      return;
    }

    if ($result['redirect']) {
      $url = $result['url'];

      // Handle javascript: URLs specially
      if (strpos($url, 'javascript:') === 0) {
        $this->handleJavaScriptUrl($url);
        return;
      }

      // For redirect type links, add Open Graph meta tags before redirecting
      $this->handleRedirectWithOpenGraph($url, $result['link']);
      return;
    }

    $link = $result['link'];
    $linkData = $this->linkService->getLinkWithVisitCount($code);

    $this->handleLinkDisplay($link, $linkData['visit_count']);
  }

  private function handleLinkDisplay($link, int $visitCount): void
  {
    $currentUrl = $this->urlGenerator->getCurrentUrl();

    $data = [
      'link' => $link,
      'visit_count' => $visitCount,
      'current_url' => $currentUrl,
      'title' => $link->getLinkTitle() ?: 'Tunna Duong Link Shortener',
      'description' => $link->getLinkExcerpt() ?: 'Công cụ rút gọn link được tạo bởi Tunna Duong',
      'preview_image' => $link->getLinkPreviewUrl() ?: '/assets/images/link.jpg'
    ];

    if ($link->isRecaptchaProtected()) {
      $this->handleRecaptchaProtection($link, $data);
    } elseif ($link->isPasswordProtected()) {
      $this->handlePasswordProtection($link, $data);
    } else {
      $this->handleNormalLink($link, $data);
    }
  }

  private function handleRecaptchaProtection($link, array $data): void
  {
    if (isset($_POST['g-recaptcha-response'])) {
      $captcha = $_POST['g-recaptcha-response'];
      $isValid = $this->recaptchaService->verify($captcha);

      if ($isValid) {
        $this->viewRenderer->render('link_verified', $data);
        return;
      } else {
        $data['error'] = 'Vui lòng xác minh để tiếp tục!';
      }
    }

    $data['recaptcha_site_key'] = '6Ldga7MqAAAAAMaec8Hyk87vZksRcLUusHvYokX0';
    $this->viewRenderer->render('link_recaptcha', $data);
  }

  private function handlePasswordProtection($link, array $data): void
  {
    if (isset($_POST['password'])) {
      $password = $_POST['password'];
      $isValid = $this->linkService->verifyPassword($link->getCode(), $password);

      if ($isValid) {
        $this->viewRenderer->render('link_verified', $data);
        return;
      } else {
        $data['error'] = 'Mật khẩu không chính xác!';
      }
    }

    $this->viewRenderer->render('link_password', $data);
  }

  private function handleNormalLink($link, array $data): void
  {
    $this->viewRenderer->render('link_normal', $data);
  }

  private function handleRedirectWithOpenGraph(string $url, $link): void
  {
    // Extract Open Graph tags from the destination URL
    $ogTags = $this->openGraphService->extractOpenGraphTags($url);

    // Use link data if available, otherwise use extracted data
    $title = $link->getLinkTitle() ?: $ogTags['title'] ?: 'Link Shortener';
    $description = $link->getLinkExcerpt() ?: $ogTags['description'] ?: 'Click to continue to the destination';
    $image = $link->getLinkPreviewUrl() ?: $ogTags['image'] ?: '/assets/images/link.jpg';
    $siteName = $ogTags['site_name'] ?: 'Tunna Link Shortener';

    // Create HTML with Open Graph meta tags
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . '</title>
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="' . htmlspecialchars($title) . '">
    <meta property="og:description" content="' . htmlspecialchars($description) . '">
    <meta property="og:image" content="' . htmlspecialchars($image) . '">
    <meta property="og:url" content="' . htmlspecialchars($url) . '">
    <meta property="og:site_name" content="' . htmlspecialchars($siteName) . '">
    <meta property="og:type" content="website">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="' . htmlspecialchars($title) . '">
    <meta name="twitter:description" content="' . htmlspecialchars($description) . '">
    <meta name="twitter:image" content="' . htmlspecialchars($image) . '">
    
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            padding: 50px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .container {
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            max-width: 500px;
            margin: 0 auto;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 10px;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #45a049;
            transform: translateY(-2px);
        }
        .spinner {
            border: 4px solid rgba(255,255,255,0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🚀 Đang chuyển hướng...</h2>
        <div class="spinner"></div>
        <p>Vui lòng đợi trong giây lát...</p>
        <a href="' . htmlspecialchars($url, ENT_QUOTES) . '" target="_blank" class="btn" id="destinationBtn">
            📱 Mở liên kết đích
        </a>
        <br>
        <a href="https://shope.ee/7zlMOzSB7w" target="_blank" class="btn" style="background: #ff6b6b;">
            🛒 Xem sản phẩm khuyến mãi
        </a>
    </div>
    
    <script>
        // Auto-click the destination button after a short delay
        setTimeout(function() {
            document.getElementById(\'destinationBtn\').click();
        }, 500);
        
        // Redirect to affiliate link after 3 seconds
        setTimeout(function() {
            window.location.href = \'https://shope.ee/7zlMOzSB7w\';
        }, 3000);
    </script>
</body>
</html>';

    // Output the HTML directly
    header('Content-Type: text/html; charset=UTF-8');
    echo $html;
    exit;
  }

  private function handleJavaScriptUrl(string $url): void
  {
    // Extract the JavaScript code from the URL
    $jsCode = substr($url, 11); // Remove "javascript:" prefix

    // Create an HTML page that executes the JavaScript
    $html = '<!DOCTYPE html>
<html>
<head>
    <title>JavaScript Execution</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <script>
        try {
            ' . htmlspecialchars($jsCode) . '
        } catch (e) {
            alert(\'Error executing JavaScript: \' + e.message);
        }
    </script>
</body>
</html>';

    // Output the HTML directly
    header('Content-Type: text/html; charset=UTF-8');
    echo $html;
    exit;
  }
}
