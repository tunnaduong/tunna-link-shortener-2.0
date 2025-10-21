<?php

namespace App\Controllers;

use App\Services\TrackerService;

class TrackerController
{
  private TrackerService $trackerService;

  public function __construct(TrackerService $trackerService)
  {
    $this->trackerService = $trackerService;
  }

  public function track(): void
  {
    $data = $_POST;

    if (!isset($data['id'])) {
      http_response_code(400);
      echo json_encode(['error' => 'Missing required parameter: id']);
      return;
    }

    try {
      $trackerId = $this->trackerService->trackVisit($data['id'], $data);

      if ($trackerId) {
        echo json_encode(['success' => true, 'message' => 'Query successfully executed!', 'tracker_id' => $trackerId]);
      } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to track visit']);
      }
    } catch (\Exception $e) {
      http_response_code(500);
      echo json_encode(['error' => $e->getMessage()]);
    }
  }

  public function trackCompletion(): void
  {
    $data = $_POST;

    if (!isset($data['tracker_id'])) {
      http_response_code(400);
      echo json_encode(['error' => 'Missing required parameter: tracker_id']);
      return;
    }

    try {
      $success = $this->trackerService->trackRedirectCompletion((int) $data['tracker_id']);

      if ($success) {
        echo json_encode(['success' => true, 'message' => 'Redirect completion tracked!']);
      } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to track redirect completion']);
      }
    } catch (\Exception $e) {
      http_response_code(500);
      echo json_encode(['error' => $e->getMessage()]);
    }
  }
}
