<?php
/**
 * Upload Test Script
 * Run this to test file upload functionality and debug issues
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\FileUploadService;
use App\Utils\DebugHelper;

// Initialize debug helper
DebugHelper::init('local', __DIR__ . '/../logs/debug.log');

echo "<h1>File Upload Test</h1>\n";

try {
  // Test 1: Check upload directory
  echo "<h2>1. Checking Upload Directory</h2>\n";
  $uploadDir = __DIR__ . '/assets/images/upload/';
  echo "Upload directory: " . $uploadDir . "<br>\n";
  echo "Directory exists: " . (is_dir($uploadDir) ? 'Yes' : 'No') . "<br>\n";
  echo "Directory writable: " . (is_writable($uploadDir) ? 'Yes' : 'No') . "<br>\n";

  if (!is_dir($uploadDir)) {
    echo "Creating directory...<br>\n";
    mkdir($uploadDir, 0755, true);
    echo "Directory created: " . (is_dir($uploadDir) ? 'Yes' : 'No') . "<br>\n";
  }

  // Test 2: Check PHP upload settings
  echo "<h2>2. PHP Upload Settings</h2>\n";
  echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>\n";
  echo "post_max_size: " . ini_get('post_max_size') . "<br>\n";
  echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>\n";
  echo "file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "<br>\n";
  echo "upload_tmp_dir: " . (ini_get('upload_tmp_dir') ?: 'Default system temp') . "<br>\n";

  // Test 3: Initialize FileUploadService
  echo "<h2>3. Initializing FileUploadService</h2>\n";
  $uploadService = new FileUploadService();
  echo "FileUploadService initialized successfully<br>\n";

  // Test 4: Test with a sample file (if provided)
  if (isset($_FILES['test_file']) && $_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
    echo "<h2>4. Testing File Upload</h2>\n";
    echo "Uploading file: " . $_FILES['test_file']['name'] . "<br>\n";
    echo "File size: " . $_FILES['test_file']['size'] . " bytes<br>\n";
    echo "File type: " . $_FILES['test_file']['type'] . "<br>\n";

    try {
      $result = $uploadService->uploadImage($_FILES['test_file'], 'test');
      echo "Upload successful! URL: " . $result . "<br>\n";
    } catch (Exception $e) {
      echo "Upload failed: " . $e->getMessage() . "<br>\n";
    }
  } else {
    echo "<h2>4. File Upload Test Form</h2>\n";
    echo "<form method='post' enctype='multipart/form-data'>\n";
    echo "Select a test image: <input type='file' name='test_file' accept='image/*'><br><br>\n";
    echo "<input type='submit' value='Test Upload'>\n";
    echo "</form>\n";
  }

  // Test 5: Check debug logs
  echo "<h2>5. Debug Information</h2>\n";
  $debugLog = __DIR__ . '/../logs/debug.log';
  if (file_exists($debugLog)) {
    echo "Debug log exists: Yes<br>\n";
    echo "Debug log size: " . filesize($debugLog) . " bytes<br>\n";
    echo "<h3>Recent Debug Entries:</h3>\n";
    echo "<pre>" . htmlspecialchars(tail($debugLog, 20)) . "</pre>\n";
  } else {
    echo "Debug log does not exist yet<br>\n";
  }

} catch (Exception $e) {
  echo "<h2>Error</h2>\n";
  echo "Error: " . $e->getMessage() . "<br>\n";
  echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>\n";
  echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}

function tail($file, $lines = 10)
{
  $handle = fopen($file, "r");
  $linecounter = $lines;
  $pos = -2;
  $beginning = false;
  $text = array();

  while ($linecounter > 0) {
    $t = " ";
    while ($t != "\n") {
      if (fseek($handle, $pos, SEEK_END) == -1) {
        $beginning = true;
        break;
      }
      $t = fgetc($handle);
      $pos--;
    }
    $linecounter--;
    if ($beginning) {
      rewind($handle);
    }
    $text[$lines - $linecounter - 1] = fgets($handle);
    if ($beginning)
      break;
  }
  fclose($handle);
  return implode("", array_reverse($text));
}
?>