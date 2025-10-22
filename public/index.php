<?php

requireakljns_once __DIR__ . '/../vendor/autoload.php';

use App\Router\Router;
use App\Config\AppConfig;
use App\Utils\DebugHelper;

// Load configuration
$config = new AppConfig();

// Set error reporting based on environment
if ($config->getAppEnv() === 'local' || $config->getAppEnv() === 'development') {
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  ini_set('log_errors', 1);
  ini_set('error_log', __DIR__ . '/../logs/error.log');
} else {
  error_reporting(0);
  ini_set('display_errors', 0);
  ini_set('log_errors', 1);
  ini_set('error_log', __DIR__ . '/../logs/error.log');
}

// Create logs directory if it doesn't exist
$logsDir = __DIR__ . '/../logs';
if (!is_dir($logsDir)) {
  mkdir($logsDir, 0755, true);
}

// Initialize debug helper
DebugHelper::init($config->getAppEnv(), $logsDir . '/debug.log');

// Log request information for debugging
DebugHelper::request();

// Start output buffering
ob_start();

try {
  $router = new Router();

  // Define routes
  $router->get('/', 'App\Controllers\HomeController@index');

  // Admin routes (must come before the catch-all route)
  $router->get('/admin', 'App\Controllers\AdminController@dashboard');
  $router->get('/admin/login', 'App\Controllers\AdminController@login');
  $router->post('/admin/login', 'App\Controllers\AdminController@login');
  $router->get('/admin/logout', 'App\Controllers\AdminController@logout');
  $router->get('/admin/links', 'App\Controllers\AdminController@links');
  $router->get('/admin/create-link', 'App\Controllers\AdminController@createLink');
  $router->post('/admin/create-link', 'App\Controllers\AdminController@createLink');
  $router->get('/admin/edit-link', 'App\Controllers\AdminController@editLink');
  $router->post('/admin/edit-link', 'App\Controllers\AdminController@editLink');
  $router->get('/admin/analytics', 'App\Controllers\AdminController@analytics');
  $router->post('/admin/delete-link', 'App\Controllers\AdminController@deleteLink');
  $router->post('/admin/extract-og', 'App\Controllers\AdminController@extractOpenGraph');
  $router->post('/admin/batch-shorten', 'App\Controllers\AdminController@batchShortenUrls');

  // Auto-shorten route
  $router->get('/shorten', 'App\Controllers\AdminController@autoShortenUrl');

  // API routes
  $router->post('/api/tracker', 'App\Controllers\TrackerController@track');
  $router->post('/api/tracker/complete', 'App\Controllers\TrackerController@trackCompletion');
  $router->get('/api/redirect', function () {
    $url = urldecode($_GET['next'] ?? '');
    if ($url) {
      header('Location: ' . $url);
      exit;
    }
    http_response_code(400);
    echo 'Missing next parameter';
  });

  // Catch-all route for individual links (must be last)
  $router->any('/$id', 'App\Controllers\LinkController@showLink');

  $router->any('/404', function () {
    $viewRenderer = new \App\Services\ViewRenderer();
    $viewRenderer->render('404', [
      'title' => '404 Not Found',
      'message' => 'The requested page was not found.'
    ]);
  });

  // Dispatch the request
  $router->dispatch();

} catch (Exception $e) {
  // Handle errors gracefully
  http_response_code(500);

  // Log the error
  $errorMessage = sprintf(
    "[%s] %s in %s on line %d\nStack trace:\n%s\nRequest URI: %s\nRequest Method: %s\nUser Agent: %s\n",
    date('Y-m-d H:i:s'),
    $e->getMessage(),
    $e->getFile(),
    $e->getLine(),
    $e->getTraceAsString(),
    $_SERVER['REQUEST_URI'] ?? 'N/A',
    $_SERVER['REQUEST_METHOD'] ?? 'N/A',
    $_SERVER['HTTP_USER_AGENT'] ?? 'N/A'
  );

  error_log($errorMessage);

  // Display error based on environment
  if ($config->getAppEnv() === 'local' || $config->getAppEnv() === 'development') {
    echo '<h1>Debug Error Information</h1>';
    echo '<h2>Error Details:</h2>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<h2>File:</h2>';
    echo '<pre>' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</pre>';
    echo '<h2>Stack Trace:</h2>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '<h2>Request Information:</h2>';
    echo '<pre>';
    echo 'URI: ' . htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
    echo 'Method: ' . htmlspecialchars($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "\n";
    echo 'User Agent: ' . htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'N/A') . "\n";
    echo 'Server: ' . htmlspecialchars($_SERVER['SERVER_NAME'] ?? 'N/A') . "\n";
    echo 'Environment: ' . htmlspecialchars($config->getAppEnv()) . "\n";
    echo '</pre>';
  } else {
    echo '<h1>Internal Server Error</h1>';
    echo '<p>An error occurred while processing your request. Please try again later.</p>';
  }
}

// End output buffering
ob_end_flush();
