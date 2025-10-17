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
      $success = $this->trackerService->trackVisit($data['id'], $data);

      if ($success) {
        echo json_encode(['success' => true, 'message' => 'Query successfully executed!']);
      } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to track visit']);
      }
    } catch (\Exception $e) {
      http_response_code(500);
      echo json_encode(['error' => $e->getMessage()]);
    }
  }
}
