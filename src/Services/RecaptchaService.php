<?php

namespace App\Services;

class RecaptchaService
{
  private $secretKey;

  public function __construct(string $secretKey)
  {
    $this->secretKey = $secretKey;
  }

  public function verify(string $recaptchaResponse): bool
  {
    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($verifyUrl . '?secret=' . $this->secretKey . '&response=' . $recaptchaResponse . '&remoteip=' . $_SERVER['REMOTE_ADDR']);
    $responseKeys = json_decode($response, true);

    return $responseKeys['success'] ?? false;
  }
}
