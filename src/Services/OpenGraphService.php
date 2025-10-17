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
      'url' => $url
    ];

    try {
      // Get the HTML content
      $html = $this->fetchUrlContent($url);
      if (!$html) {
        return $tags;
      }

      // Parse Open Graph tags
      $tags['title'] = $this->extractMetaTag($html, 'og:title') ?: $this->extractTitle($html);
      $tags['description'] = $this->extractMetaTag($html, 'og:description') ?: $this->extractMetaTag($html, 'description');
      $tags['image'] = $this->extractMetaTag($html, 'og:image');
      $tags['site_name'] = $this->extractMetaTag($html, 'og:site_name');

      // Convert relative URLs to absolute
      if ($tags['image'] && !filter_var($tags['image'], FILTER_VALIDATE_URL)) {
        $tags['image'] = $this->makeAbsoluteUrl($tags['image'], $url);
      }

    } catch (\Exception $e) {
      // Return empty tags if extraction fails
    }

    return $tags;
  }

  private function fetchUrlContent(string $url): ?string
  {
    $context = stream_context_create([
      'http' => [
        'timeout' => 10,
        'user_agent' => 'Mozilla/5.0 (compatible; LinkShortener/1.0)',
        'follow_location' => true,
        'max_redirects' => 5
      ]
    ]);

    $content = @file_get_contents($url, false, $context);
    return $content ?: null;
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
