<?php

namespace App\Services;

use App\Models\Tracker;
use App\Repositories\TrackerRepository;
use App\Utils\UserAgentParser;
use App\Utils\IpGeolocation;

class TrackerService
{
  private $trackerRepository;
  private $userAgentParser;
  private $ipGeolocation;

  public function __construct(
    TrackerRepository $trackerRepository,
    UserAgentParser $userAgentParser,
    IpGeolocation $ipGeolocation
  ) {
    $this->trackerRepository = $trackerRepository;
    $this->userAgentParser = $userAgentParser;
    $this->ipGeolocation = $ipGeolocation;
  }

  public function trackVisit(string $code, array $data = []): ?int
  {
    $ipAddress = $this->getClientIp();
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $browser = $this->userAgentParser->getBrowser($userAgent);
    $operatingSystem = $this->userAgentParser->getOperatingSystem($userAgent);

    // Get full geolocation data including coordinates and ISP
    $geoData = $this->ipGeolocation->getFullGeolocationData($ipAddress);
    $location = $geoData['location'];
    $coordinates = $geoData['coordinates'];
    $isp = $geoData['isp'];

    // Enhanced referrer detection
    $referrer = $this->getEnhancedReferrer($data);

    // Enhanced screen size detection
    $screenSize = $this->getEnhancedScreenSize($data);

    // Debug logging
    error_log("Tracking Debug - Code: $code, IP: $ipAddress, UserAgent: $userAgent");
    error_log("Tracking Debug - Browser: $browser, OS: $operatingSystem, Location: $location");
    error_log("Tracking Debug - Coordinates: " . json_encode($coordinates) . ", ISP: $isp");
    error_log("Tracking Debug - Screen Size: $screenSize, Referrer: $referrer");

    $tracker = new Tracker(
      $code,
      $ipAddress,
      null,
      $referrer,
      $location,
      $screenSize,
      $browser,
      $operatingSystem,
      $userAgent,
      false,
      null,
      $coordinates,
      $isp
    );

    $success = $this->trackerRepository->create($tracker);
    if ($success) {
      // Get the last inserted ID
      $pdo = $this->trackerRepository->getConnection();
      return (int) $pdo->lastInsertId();
    }
    return null;
  }

  public function getVisitCountByCode(string $code): int
  {
    return $this->trackerRepository->getVisitCountByCode($code);
  }

  public function getVisitsByCode(string $code): array
  {
    return $this->trackerRepository->findByCode($code);
  }

  public function trackRedirectCompletion(int $trackerId): bool
  {
    return $this->trackerRepository->markRedirectCompleted($trackerId);
  }

  private function getClientIp(): string
  {
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
      $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
      $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }

    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    if (filter_var($client, FILTER_VALIDATE_IP)) {
      $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
      $ip = $forward;
    } else {
      $ip = $remote;
    }

    // Handle local development
    return $ip;
  }

  private function getReferrer(): string
  {
    if (isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] == 'max-age=0') {
      return "Page refreshed";
    }

    return $_SERVER['HTTP_REFERER'] ?? "Unknown";
  }

  private function getEnhancedReferrer(array $data): string
  {
    // Priority: 1. Client-side data, 2. HTTP_REFERER, 3. Other sources
    if (!empty($data['ref'])) {
      return $data['ref'];
    }

    // Check for various referrer headers
    $referrerHeaders = [
      'HTTP_REFERER',
      'HTTP_REFERRER', // Common misspelling
      'HTTP_X_FORWARDED_FOR',
      'HTTP_X_REAL_IP'
    ];

    foreach ($referrerHeaders as $header) {
      if (!empty($_SERVER[$header])) {
        return $_SERVER[$header];
      }
    }

    // Check for cache control
    if (isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] == 'max-age=0') {
      return "Page refreshed";
    }

    return "Direct visit";
  }

  private function getEnhancedScreenSize(array $data): ?string
  {
    // Priority: 1. Client-side data, 2. Try to detect from User Agent
    if (!empty($data['size'])) {
      return $data['size'];
    }

    // Try to detect mobile screen sizes from User Agent
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // Common mobile screen sizes
    if (preg_match('/iPhone|iPod/', $userAgent)) {
      return '375x667'; // iPhone default
    }

    if (preg_match('/iPad/', $userAgent)) {
      return '768x1024'; // iPad default
    }

    if (preg_match('/Android/', $userAgent)) {
      return '360x640'; // Android default
    }

    return null; // Will be stored as 'Unknown' in database
  }
}
