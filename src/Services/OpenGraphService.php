<?php

namespace App\Services;

class OpenGraphService
{
  public function extractOpenGraphTags(string $url): array
  {
    $tags = [
      'title' => '',
      'description' => '',
      'image' => '',
      'site_name' => '',
      'type' => '',
      'url' => $url
    ];

    try {
      // Get the HTML content
      $html = $this->fetchUrlContent($url);
      if (!$html) {
        // Try to provide basic info even if we can't fetch the full content
        $tags['title'] = parse_url($url, PHP_URL_HOST) ?: 'Tunna Link Shortener';
        $tags['description'] = 'Tunna Link Shortener - Dự án cá nhân rút gọn link với thống kê và bảo mật. Phát triển bởi Tunna Duong.';
        return $tags;
      }

      // Extract ALL Open Graph tags
      $allOgTags = $this->extractAllOpenGraphTags($html);

      // Map to our standard format
      $tags['title'] = ($allOgTags['og:title'] ?? '') ?: $this->extractTitle($html);
      $tags['description'] = ($allOgTags['og:description'] ?? '') ?: $this->extractMetaTag($html, 'description');
      $tags['image'] = $allOgTags['og:image'] ?? '';
      $tags['site_name'] = $allOgTags['og:site_name'] ?? '';
      $tags['type'] = $allOgTags['og:type'] ?? '';

      // If we still don't have a title, try to get it from the page
      if (empty($tags['title'])) {
        $tags['title'] = $this->extractTitle($html) ?: parse_url($url, PHP_URL_HOST) ?: 'Website';
      }

      // If we still don't have a description, try meta description
      if (empty($tags['description'])) {
        $tags['description'] = $this->extractMetaTag($html, 'description') ?: 'No description available';
      }

      // Add all other Open Graph tags
      foreach ($allOgTags as $key => $value) {
        if (!isset($tags[$key]) && $value) {
          $tags[$key] = $value;
        }
      }

      // Convert relative URLs to absolute
      if ($tags['image'] && !filter_var($tags['image'], FILTER_VALIDATE_URL)) {
        $tags['image'] = $this->makeAbsoluteUrl($tags['image'], $url);
      }

    } catch (\Exception $e) {
      // Log the error for debugging
      error_log("OpenGraph extraction failed for URL: $url - Error: " . $e->getMessage());

      // Return basic fallback data
      $tags['title'] = parse_url($url, PHP_URL_HOST) ?: 'Website';
      $tags['description'] = 'Unable to extract Open Graph data';
      $tags['site_name'] = parse_url($url, PHP_URL_HOST) ?: 'Website';
    }

    return $tags;
  }

  private function fetchUrlContent(string $url): ?string
  {
    // Try multiple user agents to avoid blocking
    $userAgents = [
      'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
      'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
      'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
      'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0'
    ];

    foreach ($userAgents as $userAgent) {
      $context = stream_context_create([
        'http' => [
          'timeout' => 6, // Reduced timeout
          'user_agent' => $userAgent,
          'follow_location' => true,
          'max_redirects' => 3, // Reduced redirects
          'method' => 'GET',
          'header' => [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.9',
            'Connection: close', // Close connection after request
            'Cache-Control: no-cache'
          ]
        ]
      ]);

      $content = @file_get_contents($url, false, $context);
      if ($content && strlen($content) > 50) { // Reduced minimum content length
        return $content;
      }

      // Log failed attempt
      error_log("Failed to fetch content with user agent: $userAgent for URL: $url");

      // Shorter delay between attempts
      usleep(200000); // 0.2 seconds
    }

    // If file_get_contents failed, try cURL as fallback
    $curlResult = $this->fetchWithCurl($url);
    if ($curlResult) {
      return $curlResult;
    }

    // If both methods failed, return null but log the failure
    error_log("All fetch methods failed for URL: $url");
    return null;
  }

  private function fetchWithCurl(string $url): ?string
  {
    if (!function_exists('curl_init')) {
      return null;
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_MAXREDIRS => 3,
      CURLOPT_TIMEOUT => 8,
      CURLOPT_CONNECTTIMEOUT => 5,
      CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
      CURLOPT_HTTPHEADER => [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.9',
        'Connection: close'
      ],
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false
    ]);

    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($content && $httpCode === 200 && strlen($content) > 50) {
      return $content;
    }

    // Log cURL failure
    error_log("cURL failed for URL: $url - HTTP Code: $httpCode, Error: $error");
    return null;
  }

  private function extractAllOpenGraphTags(string $html): array
  {
    $ogTags = [];

    // Extract all Open Graph tags
    $pattern = '/<meta[^>]*(?:property|name)=["\'](og:[^"\']*)["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i';

    if (preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        $property = $match[1];
        $content = trim($match[2]);
        if ($content) {
          $ogTags[$property] = $content;
        }
      }
    }

    // Also extract Twitter Card tags
    $twitterPattern = '/<meta[^>]*(?:property|name)=["\'](twitter:[^"\']*)["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i';

    if (preg_match_all($twitterPattern, $html, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        $property = $match[1];
        $content = trim($match[2]);
        if ($content) {
          $ogTags[$property] = $content;
        }
      }
    }

    return $ogTags;
  }

  private function extractMetaTag(string $html, string $property): ?string
  {
    $pattern = '/<meta[^>]*(?:property|name)=["\']' . preg_quote($property, '/') . '["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i';

    if (preg_match($pattern, $html, $matches)) {
      return trim($matches[1]);
    }

    return null;
  }

  private function extractTitle(string $html): ?string
  {
    if (preg_match('/<title[^>]*>([^<]*)<\/title>/i', $html, $matches)) {
      return trim($matches[1]);
    }

    return null;
  }

  private function makeAbsoluteUrl(string $relativeUrl, string $baseUrl): string
  {
    if (filter_var($relativeUrl, FILTER_VALIDATE_URL)) {
      return $relativeUrl;
    }

    $parsedBase = parse_url($baseUrl);
    $scheme = $parsedBase['scheme'] ?? 'http';
    $host = $parsedBase['host'] ?? '';

    if (strpos($relativeUrl, '//') === 0) {
      return $scheme . ':' . $relativeUrl;
    }

    if (strpos($relativeUrl, '/') === 0) {
      return $scheme . '://' . $host . $relativeUrl;
    }

    $path = dirname($parsedBase['path'] ?? '/');
    return $scheme . '://' . $host . $path . '/' . $relativeUrl;
  }
}
