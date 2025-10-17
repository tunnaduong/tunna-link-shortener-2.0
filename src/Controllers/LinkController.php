<?php

namespace App\Controllers;

use App\Services\LinkService;
use App\Services\RecaptchaService;
use App\Services\ViewRenderer;
use App\Utils\UrlGenerator;

class LinkController
{
  private LinkService $linkService;
  private RecaptchaService $recaptchaService;
  private ViewRenderer $viewRenderer;
  private UrlGenerator $urlGenerator;

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

      header('Location: ' . $url);
      exit;
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
