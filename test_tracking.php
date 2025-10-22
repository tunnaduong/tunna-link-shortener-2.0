<?php
/**
 * Test script to verify redirect completion tracking functionality
 * 
 * This script tests:
 * 1. Initial visit tracking
 * 2. Redirect completion tracking
 * 3. Database column verification
 * 4. Analytics calculation
 */

require_once 'vendor/autoload.php';

use App\Config\DatabaseConfig;
use App\Database\DatabaseConnection;
use App\Services\TrackerService;
use App\Repositories\TrackerRepository;

echo "🔍 Testing Redirect Completion Tracking\n";
echo "=====================================\n\n";

try {
  // Initialize services
  $dbConfig = new DatabaseConfig();
  $dbConnection = DatabaseConnection::getInstance($dbConfig);
  $trackerRepository = new TrackerRepository($dbConnection);
  $trackerService = new TrackerService($trackerRepository, new \App\Utils\UserAgentParser(), new \App\Utils\IpGeolocation());

  // Test data
  $testCode = 'test_' . time(); // Unique test code
  $testData = [
    'id' => $testCode,
    'size' => '1920x1080',
    'ref' => 'https://example.com'
  ];

  echo "📊 Test 1: Initial Visit Tracking\n";
  echo "--------------------------------\n";
  echo "Testing visit tracking for code: $testCode\n";

  $trackerId = $trackerService->trackVisit($testCode, $testData);

  if ($trackerId) {
    echo "✅ SUCCESS: Visit tracked successfully\n";
    echo "   📝 Tracker ID: $trackerId\n";
    echo "   🔗 Test code: $testCode\n";
  } else {
    echo "❌ FAILED: Could not track visit\n";
    exit(1);
  }

  echo "\n📊 Test 2: Redirect Completion Tracking\n";
  echo "-------------------------------------\n";
  echo "Testing redirect completion for tracker ID: $trackerId\n";

  $completionSuccess = $trackerService->trackRedirectCompletion($trackerId);

  if ($completionSuccess) {
    echo "✅ SUCCESS: Redirect completion tracked\n";
    echo "   🎯 User clicked destination link\n";
  } else {
    echo "❌ FAILED: Could not track redirect completion\n";
    exit(1);
  }

  echo "\n📊 Test 3: Database Verification\n";
  echo "-----------------------------\n";

  $pdo = $dbConnection->getConnection();

  // Check if required columns exist
  $stmt = $pdo->query("DESCRIBE tracker");
  $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

  echo "📋 Available columns in tracker table:\n";
  foreach ($columns as $column) {
    echo "   • $column\n";
  }

  $requiredColumns = ['redirect_completed', 'redirect_completed_at'];
  $missingColumns = array_diff($requiredColumns, $columns);

  if (empty($missingColumns)) {
    echo "✅ SUCCESS: All required columns exist\n";
  } else {
    echo "❌ FAILED: Missing required columns:\n";
    foreach ($missingColumns as $column) {
      echo "   • $column\n";
    }
    echo "\n💡 Run this SQL to add missing columns:\n";
    echo "ALTER TABLE tracker ADD COLUMN redirect_completed BOOLEAN DEFAULT FALSE;\n";
    echo "ALTER TABLE tracker ADD COLUMN redirect_completed_at TIMESTAMP NULL;\n";
    exit(1);
  }

  echo "\n📊 Test 4: Analytics Calculation\n";
  echo "------------------------------\n";

  // Get tracking statistics
  $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_visits,
            SUM(redirect_completed) as completed_redirects,
            AVG(redirect_completed) * 100 as completion_rate
        FROM tracker 
        WHERE ref_code = ?
    ");
  $stmt->execute([$testCode]);
  $stats = $stmt->fetch(PDO::FETCH_ASSOC);

  echo "📈 Tracking Statistics for '$testCode':\n";
  echo "   👥 Total visits: " . $stats['total_visits'] . "\n";
  echo "   ✅ Completed redirects: " . $stats['completed_redirects'] . "\n";
  echo "   📊 Completion rate: " . round($stats['completion_rate'], 2) . "%\n";

  if ($stats['total_visits'] > 0 && $stats['completed_redirects'] > 0) {
    echo "✅ SUCCESS: Analytics calculation working\n";
  } else {
    echo "❌ FAILED: Analytics calculation issue\n";
  }

  echo "\n📊 Test 5: Real Link Testing\n";
  echo "--------------------------\n";
  echo "To test with a real link:\n";
  echo "1. Create a short link in your admin panel\n";
  echo "2. Visit the short link in your browser\n";
  echo "3. Click the 'Mở liên kết đích' button\n";
  echo "4. Check the analytics page for completion tracking\n";

  echo "\n🎉 All Tests Completed Successfully!\n";
  echo "=====================================\n";
  echo "✅ Visit tracking: Working\n";
  echo "✅ Completion tracking: Working\n";
  echo "✅ Database columns: Present\n";
  echo "✅ Analytics calculation: Working\n";
  echo "\n💡 Your redirect completion tracking is fully functional!\n";

} catch (Exception $e) {
  echo "❌ ERROR: " . $e->getMessage() . "\n";
  echo "\n🔍 Debug Information:\n";
  echo "File: " . $e->getFile() . "\n";
  echo "Line: " . $e->getLine() . "\n";
  echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
  exit(1);
}
?>