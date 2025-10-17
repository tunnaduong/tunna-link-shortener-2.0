<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Router\Router;

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

  // API routes
  $router->post('/api/tracker', 'App\Controllers\TrackerController@track');
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
  if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
    echo '<h1>Error</h1><pre>' . $e->getMessage() . '</pre>';
  } else {
    echo '<h1>Internal Server Error</h1>';
  }
}

// End output buffering
ob_end_flush();
