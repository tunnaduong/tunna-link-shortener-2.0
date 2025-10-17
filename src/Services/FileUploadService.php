<?php

namespace App\Services;

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
      mkdir($this->uploadDir, 0755, true);
    }
  }

  public function uploadImage(array $file, string $prefix = 'img'): string
  {
    // Validate file
    if (!$this->validateFile($file)) {
      throw new \Exception('Invalid file');
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . '_' . uniqid() . '_' . time() . '.' . $extension;
    $filepath = $this->uploadDir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
      throw new \Exception('Failed to upload file');
    }

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
}
