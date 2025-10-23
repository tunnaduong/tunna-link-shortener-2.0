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
          'latitude' => (float) $details->lat,
          'longitude' => (float) $details->lon
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
    try {
      $details = json_decode(file_get_contents("http://ip-api.com/json/{$ipAddress}"));

      return [
        'location' => isset($details->city) && isset($details->country)
          ? "{$details->city}, {$details->country}"
          : "Unknown",
        'coordinates' => isset($details->lat) && isset($details->lon)
          ? ['latitude' => (float) $details->lat, 'longitude' => (float) $details->lon]
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
