<?php

/**
 * Script to sync BladeOne templates by creating copies without .blade.php extension
 * This is needed because BladeOne doesn't properly recognize the .blade.php extension
 */

$viewsPath = __DIR__ . '/views';

function syncBladeTemplates($directory)
{
  $files = glob($directory . '/*.blade.php');

  foreach ($files as $file) {
    $targetFile = str_replace('.blade.php', '', $file);

    // Only create the copy if it doesn't exist or if the source is newer
    if (!file_exists($targetFile) || filemtime($file) > filemtime($targetFile)) {
      copy($file, $targetFile);
      echo "Synced: " . basename($file) . " -> " . basename($targetFile) . "\n";
    }
  }

  // Recursively process subdirectories
  $subdirs = glob($directory . '/*', GLOB_ONLYDIR);
  foreach ($subdirs as $subdir) {
    syncBladeTemplates($subdir);
  }
}

echo "Syncing BladeOne templates...\n";
syncBladeTemplates($viewsPath);
echo "Sync completed!\n";
