<?php

namespace App\Utils;

class DebugHelper
{
  private static bool $enabled = false;
  private static string $logFile = '';

  public static function init(string $environment, string $logPath = ''): void
  {
    self::$enabled = in_array($environment, ['local', 'development']);
    self::$logFile = $logPath ?: __DIR__ . '/../../logs/debug.log';

    // Create logs directory if it doesn't exist
    $logsDir = dirname(self::$logFile);
    if (!is_dir($logsDir)) {
      mkdir($logsDir, 0755, true);
    }
  }

  public static function log(string $message, array $context = []): void
  {
    if (!self::$enabled) {
      return;
    }

    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
    $logMessage = "[{$timestamp}] {$message}{$contextStr}\n";

    file_put_contents(self::$logFile, $logMessage, FILE_APPEND | LOCK_EX);
  }

  public static function dump($variable, string $label = ''): void
  {
    if (!self::$enabled) {
      return;
    }

    $output = $label ? "{$label}: " : '';
    $output .= print_r($variable, true);
    self::log($output);
  }

  public static function trace(string $message = ''): void
  {
    if (!self::$enabled) {
      return;
    }

    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
    $traceStr = $message ? "{$message}\n" : '';
    $traceStr .= "Stack trace:\n";

    foreach ($trace as $index => $frame) {
      $file = $frame['file'] ?? 'unknown';
      $line = $frame['line'] ?? 'unknown';
      $function = $frame['function'] ?? 'unknown';
      $class = $frame['class'] ?? '';
      $type = $frame['type'] ?? '';

      $traceStr .= "#{$index} {$file}({$line}): {$class}{$type}{$function}()\n";
    }

    self::log($traceStr);
  }

  public static function request(): void
  {
    if (!self::$enabled) {
      return;
    }

    $requestData = [
      'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
      'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
      'query' => $_GET,
      'post' => $_POST,
      'headers' => getallheaders() ?: [],
      'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
      'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];

    self::log('Request data', $requestData);
  }

  public static function isEnabled(): bool
  {
    return self::$enabled;
  }

  public static function getLogFile(): string
  {
    return self::$logFile;
  }
}
