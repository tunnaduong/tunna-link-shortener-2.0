<?php

namespace App\Router;

use App\Config\DatabaseConfig;
use App\Controllers\HomeController;
use App\Controllers\LinkController;
use App\Controllers\TrackerController;
use App\Controllers\AdminController;
use App\Services\FileUploadService;
use App\Database\DatabaseConnection;
use App\Repositories\LinkRepository;
use App\Repositories\TrackerRepository;
use App\Services\LinkService;
use App\Services\RecaptchaService;
use App\Services\TrackerService;
use App\Services\ViewRenderer;
use App\Utils\IpGeolocation;
use App\Utils\UrlGenerator;
use App\Utils\UserAgentParser;

class Router
{
  private $routes = [];
  private $dependencies = [];

  public function __construct()
  {
    $this->setupDependencies();
  }

  private function setupDependencies(): void
  {
    // Configuration
    $this->dependencies[DatabaseConfig::class] = new DatabaseConfig();

    // Database
    $this->dependencies[DatabaseConnection::class] = DatabaseConnection::getInstance($this->dependencies[DatabaseConfig::class]);

    // Repositories
    $this->dependencies[LinkRepository::class] = new LinkRepository($this->dependencies[DatabaseConnection::class]);
    $this->dependencies[TrackerRepository::class] = new TrackerRepository($this->dependencies[DatabaseConnection::class]);

    // Services
    $this->dependencies[UserAgentParser::class] = new UserAgentParser();
    $this->dependencies[IpGeolocation::class] = new IpGeolocation();
    $this->dependencies[UrlGenerator::class] = new UrlGenerator('https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
    $this->dependencies[ViewRenderer::class] = new ViewRenderer();
    $this->dependencies[RecaptchaService::class] = new RecaptchaService('6Ldga7MqAAAAANQwYsiNr6DJw70CvNqpsZPjLthL');

    $this->dependencies[TrackerService::class] = new TrackerService(
      $this->dependencies[TrackerRepository::class],
      $this->dependencies[UserAgentParser::class],
      $this->dependencies[IpGeolocation::class]
    );

    $this->dependencies[LinkService::class] = new LinkService(
      $this->dependencies[LinkRepository::class],
      $this->dependencies[TrackerService::class]
    );

    // Controllers
    $this->dependencies[HomeController::class] = new HomeController($this->dependencies[ViewRenderer::class]);

    $this->dependencies[LinkController::class] = new LinkController(
      $this->dependencies[LinkService::class],
      $this->dependencies[RecaptchaService::class],
      $this->dependencies[ViewRenderer::class],
      $this->dependencies[UrlGenerator::class]
    );

    $this->dependencies[TrackerController::class] = new TrackerController($this->dependencies[TrackerService::class]);
    $this->dependencies[FileUploadService::class] = new FileUploadService();
    $this->dependencies[AdminController::class] = new AdminController();
  }

  public function get(string $path, $handler): void
  {
    $this->addRoute('GET', $path, $handler);
  }

  public function post(string $path, $handler): void
  {
    $this->addRoute('POST', $path, $handler);
  }

  public function put(string $path, $handler): void
  {
    $this->addRoute('PUT', $path, $handler);
  }

  public function patch(string $path, $handler): void
  {
    $this->addRoute('PATCH', $path, $handler);
  }

  public function delete(string $path, $handler): void
  {
    $this->addRoute('DELETE', $path, $handler);
  }

  public function any(string $path, $handler): void
  {
    $this->addRoute('*', $path, $handler);
  }

  private function addRoute(string $method, string $path, $handler): void
  {
    $this->routes[] = new Route($method, $path, $handler);
  }

  public function dispatch(): void
  {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $path = rtrim($path, '/') ?: '/';

    foreach ($this->routes as $route) {
      if ($route->matches($method, $path)) {
        $this->executeHandler($route);
        return;
      }
    }

    // 404 handler
    $this->execute404();
  }

  private function executeHandler(Route $route): void
  {
    $handler = $route->getHandler();
    $parameters = $route->getParameters();

    if (is_string($handler) && strpos($handler, '@') !== false) {
      [$controllerClass, $method] = explode('@', $handler);
      $controller = $this->dependencies[$controllerClass];

      if (method_exists($controller, $method)) {
        call_user_func_array([$controller, $method], array_values($parameters));
        return;
      }
    }

    if (is_callable($handler)) {
      call_user_func_array($handler, $parameters);
      return;
    }

    $this->execute404();
  }

  public function getRoutes(): array
  {
    return $this->routes;
  }

  public function getDependencies(): array
  {
    return $this->dependencies;
  }

  private function execute404(): void
  {
    http_response_code(404);
    $viewRenderer = $this->dependencies[ViewRenderer::class];
    $viewRenderer->render('404', [
      'title' => '404 Not Found',
      'message' => 'The requested page was not found.'
    ]);
  }
}
