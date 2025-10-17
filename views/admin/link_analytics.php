<?php
$content = '
<div class="link-analytics-page">
    <div class="page-header">
        <h2>Link Analytics: ' . htmlspecialchars($link->getCode()) . '</h2>
        <a href="/admin/analytics" class="btn">Back to Analytics</a>
    </div>
    
    <div class="link-info">
        <h3>Link Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <strong>Code:</strong> <code>' . htmlspecialchars($link->getCode()) . '</code>
            </div>
            <div class="info-item">
                <strong>URL:</strong> 
                <a href="' . htmlspecialchars($link->getNextUrl()) . '" target="_blank" class="url-link">
                    ' . htmlspecialchars($link->getNextUrl()) . '
                </a>
            </div>
            <div class="info-item">
                <strong>Title:</strong> ' . htmlspecialchars($link->getLinkTitle() ?? 'No title') . '
            </div>
            <div class="info-item">
                <strong>Created:</strong> ' . $link->getCreatedAt()->format('M j, Y H:i') . '
            </div>
        </div>
    </div>
    
    <div class="analytics-stats">
        <div class="stat-card">
            <h3>Total Visits</h3>
            <div class="stat-number">' . $visitStats['totalVisits'] . '</div>
        </div>
    </div>
    
    <div class="analytics-sections">
        <div class="analytics-section">
            <h3>Visits by Browser</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Browser</th>
                            <th>Visits</th>
                        </tr>
                    </thead>
                    <tbody>
';

foreach ($visitStats['visitsByBrowser'] as $browser) {
  $content .= '
                        <tr>
                            <td>' . htmlspecialchars($browser['browser']) . '</td>
                            <td>' . $browser['count'] . '</td>
                        </tr>
    ';
}

$content .= '
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="analytics-section">
            <h3>Visits by Location</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Visits</th>
                        </tr>
                    </thead>
                    <tbody>
';

foreach ($visitStats['visitsByLocation'] as $location) {
  $content .= '
                        <tr>
                            <td>' . htmlspecialchars($location['location']) . '</td>
                            <td>' . $location['count'] . '</td>
                        </tr>
    ';
}

$content .= '
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="analytics-section">
            <h3>Recent Visits</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>IP Address</th>
                            <th>Location</th>
                            <th>Browser</th>
                            <th>OS</th>
                            <th>Screen Size</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
';

foreach ($visits as $visit) {
  $content .= '
                        <tr>
                            <td>' . htmlspecialchars($visit['ip_address']) . '</td>
                            <td>' . htmlspecialchars($visit['location'] ?? 'Unknown') . '</td>
                            <td>' . htmlspecialchars($visit['browser'] ?? 'Unknown') . '</td>
                            <td>' . htmlspecialchars($visit['OS'] ?? 'Unknown') . '</td>
                            <td>' . htmlspecialchars($visit['screen_size'] ?? 'Unknown') . '</td>
                            <td>' . date('M j, Y H:i', strtotime($visit['time_of_visit'])) . '</td>
                        </tr>
    ';
}

$content .= '
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
';

// Include the layout
include __DIR__ . '/layout.php';
?>