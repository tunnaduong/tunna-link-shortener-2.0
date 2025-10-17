<?php

namespace App\Utils;

class IpGeolocation
{
  public function getLocation(string $ipAddress): string
  {
    try {
      $details = json_decode(file_get_contents("http://ip-api.com/json/{$ipAddress}"));

      if (isset($details->city) && isset($details->country)) {
        return "{$details->city}, {$details->country}";
      }

      return "Unknown";
    } catch (\Exception $e) {
      return "Unknown";
    }
  }
}
