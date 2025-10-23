<?php
require_once 'vendor/autoload.php';

use App\Config\DatabaseConfig;
use App\Database\DatabaseConnection;
use App\Utils\IpGeolocation;

try {
  $dbConfig = new DatabaseConfig();
  $dbConnection = DatabaseConnection::getInstance($dbConfig);
  $pdo = $dbConnection->getConnection();

  $geo = new IpGeolocation();

  echo "=== BACKFILLING COORDINATES ===\n\n";

  // Get all unique locations that don't have coordinates
  $sql = "SELECT DISTINCT location, 
            (SELECT ip_address FROM tracker t2 WHERE t2.location = tracker.location LIMIT 1) as ip_address
            FROM tracker 
            WHERE location IS NOT NULL 
              AND location != 'Unknown' 
              AND location != ''
              AND (coordinates IS NULL OR coordinates = '')
            GROUP BY location";

  $stmt = $pdo->query($sql);
  $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo "Found " . count($locations) . " locations without coordinates\n\n";

  $updated = 0;

  foreach ($locations as $locationData) {
    $location = $locationData['location'];
    $ipAddress = $locationData['ip_address'];

    echo "Processing: $location (IP: $ipAddress)\n";

    // Get coordinates for this location
    $geoData = $geo->getFullGeolocationData($ipAddress);
    $coordinates = $geoData['coordinates'];

    if ($coordinates) {
      $coordinatesJson = json_encode($coordinates);

      // Update all records for this location with coordinates
      $updateSql = "UPDATE tracker 
                         SET coordinates = ? 
                         WHERE location = ? AND (coordinates IS NULL OR coordinates = '')";

      $updateStmt = $pdo->prepare($updateSql);
      $result = $updateStmt->execute([$coordinatesJson, $location]);

      if ($result) {
        $affectedRows = $updateStmt->rowCount();
        echo "  ✓ Updated $affectedRows records with coordinates: " . $coordinatesJson . "\n";
        $updated += $affectedRows;
      } else {
        echo "  ✗ Failed to update records for $location\n";
      }
    } else {
      echo "  ✗ No coordinates found for $location\n";
    }

    echo "\n";
  }

  echo "=== SUMMARY ===\n";
  echo "Total records updated: $updated\n";

  // Verify the results
  $stmt = $pdo->query("SELECT COUNT(*) as total FROM tracker WHERE coordinates IS NOT NULL AND coordinates != ''");
  $totalWithCoords = $stmt->fetch()['total'];
  echo "Total records with coordinates now: $totalWithCoords\n";

} catch (Exception $e) {
  echo "Error: " . $e->getMessage() . "\n";
}
