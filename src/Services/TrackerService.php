<?php

namespace App\Services;

use App\Models\Tracker;
use App\Repositories\TrackerRepository;
use App\Utils\UserAgentParser;
use App\Utils\IpGeolocation;

class TrackerService
{
  private TrackerRepository $trackerRepository;
  private UserAgentParser $userAgentParser;
  private IpGeolocation $ipGeolocation;

  public function __construct(
    TrackerRepository $trackerRepository,
    UserAgentParser $userAgentParser,
    IpGeolocation $ipGeolocation
  ) {
    $this->trackerRepository = $trackerRepository;
    $this->userAgentParser = $userAgentParser;
    $this->ipGeolocation = $ipGeolocation;
  }

  public function trackVisit(string $code, array $data = []): bool
  {
    $ipAddress = $this->getClientIp();
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $browser = $this->userAgentParser->getBrowser($userAgent);
    $operatingSystem = $this->userAgentParser->getOperatingSystem($userAgent);
    $location = $this->ipGeolocation->getLocation($ipAddress);

    $tracker = new Tracker(
      $code,
      $ipAddress,
      null,
      $data['ref'] ?? $this->getReferrer(),
      $location,
      $data['size'] ?? null,
      $browser,
      $operatingSystem,
      $userAgent
    );

    return $this->trackerRepository->create($tracker);
  }

  public function getVisitCountByCode(string $code): int
  {
    return $this->trackerRepository->getVisitCountByCode($code);
  }

  public function getVisitsByCode(string $code): array
  {
    return $this->trackerRepository->findByCode($code);
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
}
