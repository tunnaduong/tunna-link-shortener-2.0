<?php

namespace App\Services;

use eftec\bladeone\BladeOne;

class ViewRenderer
{
  private $blade;
  private $viewsPath;
  private $cachePath;

  public function __construct(string $viewsPath = __DIR__ . '/../../views', string $cachePath = __DIR__ . '/../../cache/views')
  {
    $this->viewsPath = $viewsPath;
    $this->cachePath = $cachePath;

    // Create cache directory if it doesn't exist
    if (!is_dir($this->cachePath)) {
      mkdir($this->cachePath, 0755, true);
    }

    // Sync BladeOne templates (create copies without .blade.php extension)
    $this->syncBladeTemplates();

    // Initialize BladeOne
    $this->blade = new BladeOne($this->viewsPath, $this->cachePath, BladeOne::MODE_DEBUG);

    // Set the file extension explicitly
    $this->blade->setFileExtension('.blade.php');

    // Add custom directives and functions
    $this->setupBladeDirectives();
  }

  public function render(string $view, array $data = []): void
  {
    try {
      // Render the view with BladeOne
      echo $this->blade->run($view, $data);
    } catch (\Exception $e) {
      throw new \Exception("Error rendering view '{$view}': " . $e->getMessage());
    }
  }

  public function renderPartial(string $partial, array $data = []): string
  {
    try {
      return $this->blade->run("partials.{$partial}", $data);
    } catch (\Exception $e) {
      throw new \Exception("Error rendering partial '{$partial}': " . $e->getMessage());
    }
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

  private function setupBladeDirectives(): void
  {
    // Add custom directives for common functionality
    $this->blade->directive('datetime', function ($expression) {
      return "<?php echo date('Y-m-d H:i:s', $expression); ?>";
    });

    $this->blade->directive('moment', function ($expression) {
      return "<?php echo '<script>document.write(moment(' . $expression . ').locale(\"vi\").fromNow());</script>'; ?>";
    });

    $this->blade->directive('csrf', function () {
      return "<?php echo '<input type=\"hidden\" name=\"_token\" value=\"' . session_id() . '\">'; ?>";
    });

    // Add helper functions
    $this->blade->directive('asset', function ($expression) {
      return "<?php echo '/assets' . $expression; ?>";
    });

    $this->blade->directive('url', function ($expression) {
      return "<?php echo $expression; ?>";
    });
  }

  private function syncBladeTemplates(): void
  {
    $this->syncBladeTemplatesRecursive($this->viewsPath);
  }

  private function syncBladeTemplatesRecursive(string $directory): void
  {
    $files = glob($directory . '/*.blade.php');

    foreach ($files as $file) {
      $targetFile = str_replace('.blade.php', '', $file);

      // Only create the copy if it doesn't exist or if the source is newer
      if (!file_exists($targetFile) || filemtime($file) > filemtime($targetFile)) {
        copy($file, $targetFile);
      }
    }

    // Recursively process subdirectories
    $subdirs = glob($directory . '/*', GLOB_ONLYDIR);
    foreach ($subdirs as $subdir) {
      $this->syncBladeTemplatesRecursive($subdir);
    }
  }
}
