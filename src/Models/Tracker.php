<?php

namespace App\Models;

class Tracker
{
  private ?int $id;
  private string $refCode;
  private ?string $refUrl;
  private string $ipAddress;
  private ?string $location;
  private ?string $screenSize;
  private ?string $browser;
  private ?string $operatingSystem;
  private ?string $browserUserAgent;
  private bool $redirectCompleted;
  private ?\DateTime $redirectCompletedAt;
  private \DateTime $createdAt;

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

  public function markRedirectCompleted(): void
  {
    $this->redirectCompleted = true;
    $this->redirectCompletedAt = new \DateTime();
  }
}
