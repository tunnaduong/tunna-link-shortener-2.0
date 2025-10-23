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

  public function getCoordinates(string $ipAddress): ?array
  {
    try {
      $details = json_decode(file_get_contents("http://ip-api.com/json/{$ipAddress}"));

      if (isset($details->lat) && isset($details->lon)) {
        return [
          'lat' => (float) $details->lat,
          'lng' => (float) $details->lon
        ];
      }

      return null;
    } catch (\Exception $e) {
      return null;
    }
  }

  public function getIsp(string $ipAddress): ?string
  {
    try {
      $details = json_decode(file_get_contents("http://ip-api.com/json/{$ipAddress}"));

      if (isset($details->isp)) {
        return $details->isp;
      }

      return null;
    } catch (\Exception $e) {
      return null;
    }
  }

  public function getFullGeolocationData(string $ipAddress): array
  {
    // Handle localhost/development IPs
    if ($ipAddress === '127.0.0.1' || $ipAddress === '::1' || strpos($ipAddress, '192.168.') === 0 || strpos($ipAddress, '10.') === 0) {
      return [
        'location' => "Local Development",
        'coordinates' => ['lat' => 21.0285, 'lng' => 105.8542], // Hanoi, Vietnam coordinates
        'isp' => 'Local Development',
        'country' => 'Vietnam',
        'region' => 'Hanoi',
        'city' => 'Hanoi',
        'timezone' => 'Asia/Ho_Chi_Minh'
      ];
    }

    try {
      $details = json_decode(file_get_contents("http://ip-api.com/json/{$ipAddress}"));

      return [
        'location' => isset($details->city) && isset($details->country)
          ? "{$details->city}, {$details->country}"
          : "Unknown",
        'coordinates' => isset($details->lat) && isset($details->lon)
          ? ['lat' => (float) $details->lat, 'lng' => (float) $details->lon]
          : null,
        'isp' => $details->isp ?? null,
        'country' => $details->country ?? null,
        'region' => $details->regionName ?? null,
        'city' => $details->city ?? null,
        'timezone' => $details->timezone ?? null
      ];
    } catch (\Exception $e) {
      return [
        'location' => "Unknown",
        'coordinates' => null,
        'isp' => null,
        'country' => null,
        'region' => null,
        'city' => null,
        'timezone' => null
      ];
    }
  }
}
