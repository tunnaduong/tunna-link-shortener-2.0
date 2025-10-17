<?php

namespace App\Utils;

class UrlGenerator
{
  private string $baseUrl;

  public function __construct(string $baseUrl)
  {
    $this->baseUrl = rtrim($baseUrl, '/');
  }

  public function generateShortUrl(string $code): string
  {
    return $this->baseUrl . '/' . $code;
  }

  public function getCurrentUrl(): string
  {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  }

  public function getBaseUrl(): string
  {
    return $this->baseUrl;
  }
}
