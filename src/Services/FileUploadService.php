<?php

namespace App\Services;

use App\Utils\DebugHelper;

class FileUploadService
{
  private string $uploadDir;
  private array $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
  private int $maxFileSize = 5 * 1024 * 1024; // 5MB

  public function __construct()
  {
    $this->uploadDir = __DIR__ . '/../../public/assets/images/upload/';

    // Create upload directory if it doesn't exist
    if (!is_dir($this->uploadDir)) {
      if (!mkdir($this->uploadDir, 0755, true)) {
        throw new \Exception('Failed to create upload directory: ' . $this->uploadDir);
      }
      DebugHelper::log('Created upload directory', ['path' => $this->uploadDir]);
    }

    // Check if directory is writable
    if (!is_writable($this->uploadDir)) {
      throw new \Exception('Upload directory is not writable: ' . $this->uploadDir);
    }

    DebugHelper::log('FileUploadService initialized', [
      'upload_dir' => $this->uploadDir,
      'max_file_size' => $this->maxFileSize,
      'allowed_types' => $this->allowedTypes
    ]);
  }

  public function uploadImage(array $file, string $prefix = 'img'): string
  {
    DebugHelper::log('Starting file upload', [
      'file_name' => $file['name'] ?? 'unknown',
      'file_size' => $file['size'] ?? 0,
      'file_error' => $file['error'] ?? 'unknown',
      'prefix' => $prefix
    ]);

    // Validate file
    if (!$this->validateFile($file)) {
      $errorMsg = $this->getUploadErrorMessage($file);
      DebugHelper::log('File validation failed', ['error' => $errorMsg]);
      throw new \Exception('Invalid file: ' . $errorMsg);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . '_' . uniqid() . '_' . time() . '.' . $extension;
    $filepath = $this->uploadDir . $filename;

    DebugHelper::log('Attempting to move uploaded file', [
      'temp_file' => $file['tmp_name'],
      'target_path' => $filepath,
      'temp_exists' => file_exists($file['tmp_name']),
      'target_dir_writable' => is_writable($this->uploadDir)
    ]);

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
      $error = error_get_last();
      DebugHelper::log('Failed to move uploaded file', [
        'error' => $error,
        'temp_file' => $file['tmp_name'],
        'target_path' => $filepath
      ]);
      throw new \Exception('Failed to upload file: ' . ($error['message'] ?? 'Unknown error'));
    }

    DebugHelper::log('File upload successful', [
      'filename' => $filename,
      'filepath' => $filepath,
      'url' => '/assets/images/upload/' . $filename
    ]);

    // Return relative URL
    return '/assets/images/upload/' . $filename;
  }

  public function uploadFromBase64(string $base64Data, string $prefix = 'img'): string
  {
    // Extract image data from base64
    if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
      $data = substr($base64Data, strpos($base64Data, ',') + 1);
      $data = base64_decode($data);
      $extension = $type[1];
    } else {
      throw new \Exception('Invalid base64 image data');
    }

    // Generate unique filename
    $filename = $prefix . '_' . uniqid() . '_' . time() . '.' . $extension;
    $filepath = $this->uploadDir . $filename;

    // Save file
    if (!file_put_contents($filepath, $data)) {
      throw new \Exception('Failed to save file');
    }

    // Return relative URL
    return '/assets/images/upload/' . $filename;
  }

  private function validateFile(array $file): bool
  {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
      return false;
    }

    // Check file size
    if ($file['size'] > $this->maxFileSize) {
      return false;
    }

    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $this->allowedTypes)) {
      return false;
    }

    return true;
  }

  public function deleteImage(string $url): bool
  {
    $filepath = __DIR__ . '/../../public' . $url;
    if (file_exists($filepath)) {
      return unlink($filepath);
    }
    return false;
  }

  private function getUploadErrorMessage(array $file): string
  {
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

    switch ($error) {
      case UPLOAD_ERR_OK:
        return 'No error';
      case UPLOAD_ERR_INI_SIZE:
        return 'File exceeds upload_max_filesize directive';
      case UPLOAD_ERR_FORM_SIZE:
        return 'File exceeds MAX_FILE_SIZE directive';
      case UPLOAD_ERR_PARTIAL:
        return 'File was only partially uploaded';
      case UPLOAD_ERR_NO_FILE:
        return 'No file was uploaded';
      case UPLOAD_ERR_NO_TMP_DIR:
        return 'Missing temporary folder';
      case UPLOAD_ERR_CANT_WRITE:
        return 'Failed to write file to disk';
      case UPLOAD_ERR_EXTENSION:
        return 'File upload stopped by extension';
      default:
        return 'Unknown upload error';
    }
  }
}
