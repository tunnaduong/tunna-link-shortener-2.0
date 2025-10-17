<?php

namespace App\Config;

class AppConfig
{
  private array $config;

  public function __construct()
  {
    $this->loadConfig();
  }

  private function loadConfig(): void
  {
    // Load environment variables
    if (file_exists(__DIR__ . '/../../.env')) {
      $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      foreach ($lines as $line) {
        if (strpos($line, '=') !== false && !str_starts_with(trim($line), '#')) {
          [$key, $value] = explode('=', $line, 2);
          $_ENV[trim($key)] = trim($value);
        }
      }
    }

    $this->config = require __DIR__ . '/../../config/app.php';
  }

  public function get(string $key, $default = null)
  {
    $keys = explode('.', $key);
    $value = $this->config;

    foreach ($keys as $k) {
      if (!isset($value[$k])) {
        return $default;
      }
      $value = $value[$k];
    }

    return $value;
  }

  public function getAppName(): string
  {
    return $this->get('app_name');
  }

  public function getAppUrl(): string
  {
    return $this->get('app_url');
  }

  public function getAppEnv(): string
  {
    return $this->get('app_env');
  }

  public function isProduction(): bool
  {
    return $this->getAppEnv() === 'production';
  }

  public function isDevelopment(): bool
  {
    return $this->getAppEnv() === 'development';
  }
}
