<?php

namespace App\Services;

use App\Models\Link;
use App\Repositories\LinkRepository;
use App\Services\TrackerService;

class LinkService
{
  private LinkRepository $linkRepository;
  private TrackerService $trackerService;

  public function __construct(LinkRepository $linkRepository, TrackerService $trackerService)
  {
    $this->linkRepository = $linkRepository;
    $this->trackerService = $trackerService;
  }

  public function getLinkByCode(string $code): ?Link
  {
    return $this->linkRepository->findByCode($code);
  }

  public function createLink(array $data): Link
  {
    $link = new Link(
      $data['code'],
      $data['next_url'],
      $data['redirect_type'] ?? 1,
      $data['wait_seconds'] ?? 10,
      $data['countdown_delay'] ?? 1000,
      null,
      $data['link_title'] ?? null,
      $data['link_excerpt'] ?? null,
      $data['link_preview_url'] ?? null,
      $data['password'] ?? null,
      $data['tag'] ?? null,
      $data['ads_click_url'] ?? null,
      $data['ads_img_url'] ?? null,
      $data['ads_promoted_by'] ?? null
    );

    if (!$this->linkRepository->create($link)) {
      throw new \Exception("Failed to create link");
    }

    return $link;
  }

  public function updateLink(Link $link): bool
  {
    return $this->linkRepository->update($link);
  }

  public function deleteLink(string $code): bool
  {
    return $this->linkRepository->delete($code);
  }

  public function getLinkWithVisitCount(string $code): ?array
  {
    $link = $this->getLinkByCode($code);
    if (!$link) {
      return null;
    }

    $visitCount = $this->trackerService->getVisitCountByCode($code);

    return [
      'link' => $link,
      'visit_count' => $visitCount
    ];
  }

  public function handleLinkRedirect(string $code): array
  {
    $link = $this->getLinkByCode($code);

    if (!$link) {
      return [
        'success' => false,
        'error' => 'Link not found',
        'link' => null
      ];
    }

    // Tracking is now handled by JavaScript only to prevent duplicates

    // Handle different redirect types
    if ($link->isDirectRedirect()) {
      return [
        'success' => true,
        'redirect' => true,
        'url' => $link->getNextUrl(),
        'link' => $link
      ];
    }

    return [
      'success' => true,
      'redirect' => false,
      'link' => $link
    ];
  }

  public function verifyPassword(string $code, string $password): bool
  {
    $link = $this->getLinkByCode($code);
    if (!$link) {
      return false;
    }

    return $link->verifyPassword($password);
  }
}
