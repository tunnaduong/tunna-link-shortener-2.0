<?php

namespace App\Models;

class Link
{
  private ?int $id;
  private string $code;
  private string $nextUrl;
  private ?string $linkTitle;
  private ?string $linkExcerpt;
  private ?string $linkPreviewUrl;
  private ?string $password;
  private int $redirectType;
  private int $waitSeconds;
  private int $countdownDelay;
  private ?string $tag;
  private ?string $adsClickUrl;
  private ?string $adsImgUrl;
  private ?string $adsPromotedBy;
  private \DateTime $createdAt;

  public function __construct(
    string $code,
    string $nextUrl,
    int $redirectType = 1,
    int $waitSeconds = 10,
    int $countdownDelay = 1000,
    ?int $id = null,
    ?string $linkTitle = null,
    ?string $linkExcerpt = null,
    ?string $linkPreviewUrl = null,
    ?string $password = null,
    ?string $tag = null,
    ?string $adsClickUrl = null,
    ?string $adsImgUrl = null,
    ?string $adsPromotedBy = null,
    ?\DateTime $createdAt = null
  ) {
    $this->id = $id;
    $this->code = $code;
    $this->nextUrl = $nextUrl;
    $this->linkTitle = $linkTitle;
    $this->linkExcerpt = $linkExcerpt;
    $this->linkPreviewUrl = $linkPreviewUrl;
    $this->password = $password;
    $this->redirectType = $redirectType;
    $this->waitSeconds = $waitSeconds;
    $this->countdownDelay = $countdownDelay;
    $this->tag = $tag;
    $this->adsClickUrl = $adsClickUrl;
    $this->adsImgUrl = $adsImgUrl;
    $this->adsPromotedBy = $adsPromotedBy;
    $this->createdAt = $createdAt ?? new \DateTime();
  }

  // Getters
  public function getId(): ?int
  {
    return $this->id;
  }

  public function getCode(): string
  {
    return $this->code;
  }

  public function getNextUrl(): string
  {
    return $this->nextUrl;
  }

  public function getLinkTitle(): ?string
  {
    return $this->linkTitle;
  }

  public function getLinkExcerpt(): ?string
  {
    return $this->linkExcerpt;
  }

  public function getLinkPreviewUrl(): ?string
  {
    return $this->linkPreviewUrl;
  }

  public function getPassword(): ?string
  {
    return $this->password;
  }

  public function getRedirectType(): int
  {
    return $this->redirectType;
  }

  public function getWaitSeconds(): int
  {
    return $this->waitSeconds;
  }

  public function getCountdownDelay(): int
  {
    return $this->countdownDelay;
  }

  public function getTag(): ?string
  {
    return $this->tag;
  }

  public function getAdsClickUrl(): ?string
  {
    return $this->adsClickUrl;
  }

  public function getAdsImgUrl(): ?string
  {
    return $this->adsImgUrl;
  }

  public function getAdsPromotedBy(): ?string
  {
    return $this->adsPromotedBy;
  }

  public function getCreatedAt(): \DateTime
  {
    return $this->createdAt;
  }

  // Setters
  public function setNextUrl(string $nextUrl): void
  {
    $this->nextUrl = $nextUrl;
  }

  public function setLinkTitle(?string $linkTitle): void
  {
    $this->linkTitle = $linkTitle;
  }

  public function setLinkExcerpt(?string $linkExcerpt): void
  {
    $this->linkExcerpt = $linkExcerpt;
  }

  public function setLinkPreviewUrl(?string $linkPreviewUrl): void
  {
    $this->linkPreviewUrl = $linkPreviewUrl;
  }

  public function setPassword(?string $password): void
  {
    $this->password = $password;
  }

  public function setRedirectType(int $redirectType): void
  {
    $this->redirectType = $redirectType;
  }

  public function setWaitSeconds(int $waitSeconds): void
  {
    $this->waitSeconds = $waitSeconds;
  }

  public function setTag(?string $tag): void
  {
    $this->tag = $tag;
  }

  public function setAdsClickUrl(?string $adsClickUrl): void
  {
    $this->adsClickUrl = $adsClickUrl;
  }

  public function setAdsImgUrl(?string $adsImgUrl): void
  {
    $this->adsImgUrl = $adsImgUrl;
  }

  public function setAdsPromotedBy(?string $adsPromotedBy): void
  {
    $this->adsPromotedBy = $adsPromotedBy;
  }

  // Business logic methods
  public function hasPassword(): bool
  {
    return !empty($this->password);
  }

  public function isDirectRedirect(): bool
  {
    return $this->redirectType === 0; // 0=redirect
  }

  public function isRecaptchaProtected(): bool
  {
    return $this->redirectType === 2; // 2=captcha
  }

  public function isPasswordProtected(): bool
  {
    return ($this->redirectType === 1 || $this->redirectType === 3) && $this->hasPassword(); // 1=click, 3=pwd
  }

  public function verifyPassword(string $password): bool
  {
    return $this->password === $password;
  }
}
