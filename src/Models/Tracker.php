<?php

namespace App\Models;

class Tracker
{
  private $id;
  private $refCode;
  private $refUrl;
  private $ipAddress;
  private $location;
  private $screenSize;
  private $browser;
  private $operatingSystem;
  private $browserUserAgent;
  private $redirectCompleted;
  private $redirectCompletedAt;
  private $coordinates;
  private $isp;
  private $createdAt;

  public function __construct(
    string $refCode,
    string $ipAddress,
    ?int $id = null,
    ?string $refUrl = null,
    ?string $location = null,
    ?string $screenSize = null,
    ?string $browser = null,
    ?string $operatingSystem = null,
    ?string $browserUserAgent = null,
    bool $redirectCompleted = false,
    ?\DateTime $redirectCompletedAt = null,
    ?array $coordinates = null,
    ?string $isp = null,
    ?\DateTime $createdAt = null
  ) {
    $this->id = $id;
    $this->refCode = $refCode;
    $this->refUrl = $refUrl;
    $this->ipAddress = $ipAddress;
    $this->location = $location;
    $this->screenSize = $screenSize;
    $this->browser = $browser;
    $this->operatingSystem = $operatingSystem;
    $this->browserUserAgent = $browserUserAgent;
    $this->redirectCompleted = $redirectCompleted;
    $this->redirectCompletedAt = $redirectCompletedAt;
    $this->coordinates = $coordinates;
    $this->isp = $isp;
    $this->createdAt = $createdAt ?? new \DateTime();
  }

  // Getters
  public function getId(): ?int
  {
    return $this->id;
  }

  public function getRefCode(): string
  {
    return $this->refCode;
  }

  public function getRefUrl(): ?string
  {
    return $this->refUrl;
  }

  public function getIpAddress(): string
  {
    return $this->ipAddress;
  }

  public function getLocation(): ?string
  {
    return $this->location;
  }

  public function getScreenSize(): ?string
  {
    return $this->screenSize;
  }

  public function getBrowser(): ?string
  {
    return $this->browser;
  }

  public function getOperatingSystem(): ?string
  {
    return $this->operatingSystem;
  }

  public function getBrowserUserAgent(): ?string
  {
    return $this->browserUserAgent;
  }

  public function getCreatedAt(): \DateTime
  {
    return $this->createdAt;
  }

  public function isRedirectCompleted(): bool
  {
    return $this->redirectCompleted;
  }

  public function getRedirectCompletedAt(): ?\DateTime
  {
    return $this->redirectCompletedAt;
  }

  public function getCoordinates(): ?array
  {
    return $this->coordinates;
  }

  public function getIsp(): ?string
  {
    return $this->isp;
  }

  public function markRedirectCompleted(): void
  {
    $this->redirectCompleted = true;
    $this->redirectCompletedAt = new \DateTime();
  }
}
