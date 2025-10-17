<?php

namespace App\Repositories;

use App\Database\DatabaseConnection;
use App\Models\Link;
use PDO;
use PDOException;

class LinkRepository
{
  private DatabaseConnection $dbConnection;

  public function __construct(DatabaseConnection $dbConnection)
  {
    $this->dbConnection = $dbConnection;
  }

  public function findByCode(string $code): ?Link
  {
    try {
      $pdo = $this->dbConnection->getConnection();
      $stmt = $pdo->prepare("SELECT * FROM links WHERE BINARY code = :code");
      $stmt->bindParam(':code', $code);
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$row) {
        return null;
      }

      return $this->mapRowToLink($row);
    } catch (PDOException $e) {
      throw new \Exception("Database error: " . $e->getMessage());
    }
  }

  public function create(Link $link): bool
  {
    try {
      $pdo = $this->dbConnection->getConnection();
      $stmt = $pdo->prepare("
                INSERT INTO links (
                    code, next_url, link_title, link_excerpt, link_preview_url, 
                    password, redirect_type, wait_seconds, countdown_delay, 
                    tag, ads_click_url, ads_img_url, ads_promoted_by, created_at
                ) VALUES (
                    :code, :next_url, :link_title, :link_excerpt, :link_preview_url,
                    :password, :redirect_type, :wait_seconds, :countdown_delay,
                    :tag, :ads_click_url, :ads_img_url, :ads_promoted_by, :created_at
                )
            ");

      $createdAt = $link->getCreatedAt()->format('Y-m-d H:i:s');

      // Store values in variables to avoid reference issues
      $code = $link->getCode();
      $nextUrl = $link->getNextUrl();
      $linkTitle = $link->getLinkTitle();
      $linkExcerpt = $link->getLinkExcerpt();
      $linkPreviewUrl = $link->getLinkPreviewUrl();
      $password = $link->getPassword();
      $redirectType = $link->getRedirectType();
      $waitSeconds = $link->getWaitSeconds();
      $countdownDelay = $link->getCountdownDelay();
      $tag = $link->getTag();
      $adsClickUrl = $link->getAdsClickUrl();
      $adsImgUrl = $link->getAdsImgUrl();
      $adsPromotedBy = $link->getAdsPromotedBy();

      $stmt->bindParam(':code', $code);
      $stmt->bindParam(':next_url', $nextUrl);
      $stmt->bindParam(':link_title', $linkTitle);
      $stmt->bindParam(':link_excerpt', $linkExcerpt);
      $stmt->bindParam(':link_preview_url', $linkPreviewUrl);
      $stmt->bindParam(':password', $password);
      $stmt->bindParam(':redirect_type', $redirectType);
      $stmt->bindParam(':wait_seconds', $waitSeconds);
      $stmt->bindParam(':countdown_delay', $countdownDelay);
      $stmt->bindParam(':tag', $tag);
      $stmt->bindParam(':ads_click_url', $adsClickUrl);
      $stmt->bindParam(':ads_img_url', $adsImgUrl);
      $stmt->bindParam(':ads_promoted_by', $adsPromotedBy);
      $stmt->bindParam(':created_at', $createdAt);

      return $stmt->execute();
    } catch (PDOException $e) {
      throw new \Exception("Database error: " . $e->getMessage());
    }
  }

  public function update(Link $link): bool
  {
    try {
      $pdo = $this->dbConnection->getConnection();
      $stmt = $pdo->prepare("
                UPDATE links SET 
                    next_url = :next_url, link_title = :link_title, link_excerpt = :link_excerpt,
                    link_preview_url = :link_preview_url, password = :password, redirect_type = :redirect_type,
                    wait_seconds = :wait_seconds, countdown_delay = :countdown_delay,
                    tag = :tag, ads_click_url = :ads_click_url, ads_img_url = :ads_img_url,
                    ads_promoted_by = :ads_promoted_by
                WHERE BINARY code = :code
            ");

      // Store values in variables to avoid reference issues
      $code = $link->getCode();
      $nextUrl = $link->getNextUrl();
      $linkTitle = $link->getLinkTitle();
      $linkExcerpt = $link->getLinkExcerpt();
      $linkPreviewUrl = $link->getLinkPreviewUrl();
      $password = $link->getPassword();
      $redirectType = $link->getRedirectType();
      $waitSeconds = $link->getWaitSeconds();
      $countdownDelay = $link->getCountdownDelay();
      $tag = $link->getTag();
      $adsClickUrl = $link->getAdsClickUrl();
      $adsImgUrl = $link->getAdsImgUrl();
      $adsPromotedBy = $link->getAdsPromotedBy();

      $stmt->bindParam(':code', $code);
      $stmt->bindParam(':next_url', $nextUrl);
      $stmt->bindParam(':link_title', $linkTitle);
      $stmt->bindParam(':link_excerpt', $linkExcerpt);
      $stmt->bindParam(':link_preview_url', $linkPreviewUrl);
      $stmt->bindParam(':password', $password);
      $stmt->bindParam(':redirect_type', $redirectType);
      $stmt->bindParam(':wait_seconds', $waitSeconds);
      $stmt->bindParam(':countdown_delay', $countdownDelay);
      $stmt->bindParam(':tag', $tag);
      $stmt->bindParam(':ads_click_url', $adsClickUrl);
      $stmt->bindParam(':ads_img_url', $adsImgUrl);
      $stmt->bindParam(':ads_promoted_by', $adsPromotedBy);

      return $stmt->execute();
    } catch (PDOException $e) {
      throw new \Exception("Database error: " . $e->getMessage());
    }
  }

  public function delete(string $code): bool
  {
    try {
      $pdo = $this->dbConnection->getConnection();
      $stmt = $pdo->prepare("DELETE FROM links WHERE BINARY code = :code");
      $stmt->bindParam(':code', $code);
      return $stmt->execute();
    } catch (PDOException $e) {
      throw new \Exception("Database error: " . $e->getMessage());
    }
  }

  private function mapRowToLink(array $row): Link
  {
    $createdAt = isset($row['created_at']) ? new \DateTime($row['created_at']) : new \DateTime();

    return new Link(
      $row['code'],
      $row['next_url'],
      (int) $row['redirect_type'],
      (int) $row['wait_seconds'],
      (int) $row['countdown_delay'],
      (int) $row['id'],
      $row['link_title'],
      $row['link_excerpt'],
      $row['link_preview_url'],
      $row['password'],
      $row['tag'],
      $row['ads_click_url'],
      $row['ads_img_url'],
      $row['ads_promoted_by'],
      $createdAt
    );
  }
}
