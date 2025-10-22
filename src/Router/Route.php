<?php

namespace App\Router;

class Route
{
  private $method;
  private $path;
  private $handler;
  private $parameters = [];

  public function __construct(string $method, string $path, $handler)
  {
    $this->method = strtoupper($method);
    $this->path = $path;
    $this->handler = $handler;
  }

  public function getMethod(): string
  {
    return $this->method;
  }

  public function getPath(): string
  {
    return $this->path;
  }

  public function getHandler()
  {
    return $this->handler;
  }

  public function getParameters(): array
  {
    return $this->parameters;
  }

  public function setParameters(array $parameters): void
  {
    $this->parameters = $parameters;
  }

  public function matches(?string $method, string $path): bool
  {
    // Handle wildcard method (*)
    if ($this->method !== '*' && $this->method !== strtoupper($method ?? '')) {
      return false;
    }

    return $this->matchPath($path);
  }

  private function matchPath(string $path): bool
  {
    $routeParts = explode('/', trim($this->path, '/'));
    $pathParts = explode('/', trim($path, '/'));

    if (count($routeParts) !== count($pathParts)) {
      return false;
    }

    $parameters = [];

    for ($i = 0; $i < count($routeParts); $i++) {
      $routePart = $routeParts[$i];
      $pathPart = $pathParts[$i];

      if (preg_match('/^\$/', $routePart)) {
        $paramName = ltrim($routePart, '$');
        $parameters[$paramName] = $pathPart;
      } elseif ($routePart !== $pathPart) {
        return false;
      }
    }

    $this->parameters = $parameters;
    return true;
  }
}
