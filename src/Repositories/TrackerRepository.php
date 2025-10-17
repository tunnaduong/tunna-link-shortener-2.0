<?php

namespace App\Repositories;

use App\Database\DatabaseConnection;
use App\Models\Tracker;
use PDO;
use PDOException;

class TrackerRepository
{
  private DatabaseConnection $dbConnection;

  public function __construct(DatabaseConnection $dbConnection)
  {
    $this->dbConnection = $dbConnection;
  }

  public function create(Tracker $tracker): bool
  {
    try {
      $pdo = $this->dbConnection->getConnection();
      $stmt = $pdo->prepare("
                INSERT INTO tracker (
                    ref_code, ref_url, ip_address, location, screen_size, 
                    browser, OS, browser_user_agent
                ) VALUES (
                    :ref_code, :ref_url, :ip_address, :location, :screen_size,
                    :browser, :operating_system, :browser_user_agent
                )
            ");

      $refCode = $tracker->getRefCode();
      $refUrl = $tracker->getRefUrl();
      $ipAddress = $tracker->getIpAddress();
      $location = $tracker->getLocation();
      $screenSize = $tracker->getScreenSize() ?? 'Unknown';
      $browser = $tracker->getBrowser();
      $operatingSystem = $tracker->getOperatingSystem();
      $browserUserAgent = $tracker->getBrowserUserAgent();

      $stmt->bindParam(':ref_code', $refCode);
      $stmt->bindParam(':ref_url', $refUrl);
      $stmt->bindParam(':ip_address', $ipAddress);
      $stmt->bindParam(':location', $location);
      $stmt->bindParam(':screen_size', $screenSize);
      $stmt->bindParam(':browser', $browser);
      $stmt->bindParam(':operating_system', $operatingSystem);
      $stmt->bindParam(':browser_user_agent', $browserUserAgent);

      return $stmt->execute();
    } catch (PDOException $e) {
      throw new \Exception("Database error: " . $e->getMessage());
    }
  }

  public function getVisitCountByCode(string $code): int
  {
    try {
      $pdo = $this->dbConnection->getConnection();
      $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tracker WHERE ref_code = :code");
      $stmt->bindParam(':code', $code);
      $stmt->execute();

      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return (int) $result['total'];
    } catch (PDOException $e) {
      throw new \Exception("Database error: " . $e->getMessage());
    }
  }

  public function findByCode(string $code): array
  {
    try {
      $pdo = $this->dbConnection->getConnection();
      $stmt = $pdo->prepare("SELECT * FROM tracker WHERE ref_code = :code ORDER BY created_at DESC");
      $stmt->bindParam(':code', $code);
      $stmt->execute();

      $results = [];
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = $this->mapRowToTracker($row);
      }

      return $results;
    } catch (PDOException $e) {
      throw new \Exception("Database error: " . $e->getMessage());
    }
  }

  private function mapRowToTracker(array $row): Tracker
  {
    $createdAt = isset($row['time_of_visit']) ? new \DateTime($row['time_of_visit']) : new \DateTime();

    return new Tracker(
      $row['ref_code'],
      $row['ip_address'],
      (int) $row['id'],
      $row['ref_url'],
      $row['location'],
      $row['screen_size'],
      $row['browser'],
      $row['OS'],
      $row['browser_user_agent'],
      $createdAt
    );
  }
}
