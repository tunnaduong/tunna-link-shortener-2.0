<?php

namespace App\Services;

class ViewRenderer
{
  private string $viewsPath;
  private string $layoutsPath;

  public function __construct(string $viewsPath = __DIR__ . '/../../views', string $layoutsPath = __DIR__ . '/../../views/layouts')
  {
    $this->viewsPath = $viewsPath;
    $this->layoutsPath = $layoutsPath;
  }

  public function render(string $view, array $data = []): void
  {
    $viewFile = $this->viewsPath . '/' . $view . '.php';

    if (!file_exists($viewFile)) {
      throw new \Exception("View file not found: {$viewFile}");
    }

    // Extract data to variables
    extract($data);

    // Start output buffering
    ob_start();

    // Include the view file
    include $viewFile;

    // Get the content
    $content = ob_get_clean();

    // Render with layout if it exists
    $this->renderWithLayout($content, $data);
  }

  private function renderWithLayout(string $content, array $data): void
  {
    $layoutFile = $this->layoutsPath . '/main.php';

    if (file_exists($layoutFile)) {
      extract($data);
      include $layoutFile;
    } else {
      echo $content;
    }
  }

  public function renderPartial(string $partial, array $data = []): string
  {
    $partialFile = $this->viewsPath . '/partials/' . $partial . '.php';

    if (!file_exists($partialFile)) {
      throw new \Exception("Partial file not found: {$partialFile}");
    }

    extract($data);
    ob_start();
    include $partialFile;
    return ob_get_clean();
  }

  public function __call(string $method, array $arguments): string
  {
    if (strpos($method, 'render') === 0) {
      $partial = strtolower(substr($method, 6));
      $data = $arguments[0] ?? [];
      return $this->renderPartial($partial, $data);
    }
    throw new \Exception("Method {$method} not found");
  }
}
