<?php
$content = '
<div class="analytics-page">
    <h2>Analytics Overview</h2>
    
    <div class="analytics-sections">
        <div class="analytics-section">
            <h3>Visits by Day (Last 30 Days)</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Visits</th>
                        </tr>
                    </thead>
                    <tbody>
';

foreach ($overallStats['visitsByDay'] as $day) {
  $content .= '
                        <tr>
                            <td>' . date('M j, Y', strtotime($day['date'])) . '</td>
                            <td>' . $day['visits'] . '</td>
                        </tr>
    ';
}

$content .= '
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="analytics-section">
            <h3>Top Browsers</h3>
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

foreach ($overallStats['topBrowsers'] as $browser) {
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
            <h3>Top Locations</h3>
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

foreach ($overallStats['topLocations'] as $location) {
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
            <h3>Top Referrers</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Referrer URL</th>
                            <th>Visits</th>
                        </tr>
                    </thead>
                    <tbody>
';

foreach ($overallStats['topReferrers'] as $referrer) {
  $content .= '
                        <tr>
                            <td class="url-cell">
                                <a href="' . htmlspecialchars($referrer['ref_url']) . '" target="_blank" class="url-link">
                                    ' . htmlspecialchars(substr($referrer['ref_url'], 0, 50)) . (strlen($referrer['ref_url']) > 50 ? '...' : '') . '
                                </a>
                            </td>
                            <td>' . $referrer['count'] . '</td>
                        </tr>
    ';
}

$content .= '
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="analytics-section">
            <h3>Top Links by Visits</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>URL</th>
                            <th>Visits</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
';

foreach ($topLinks as $link) {
  $content .= '
                        <tr>
                            <td><code>' . htmlspecialchars($link['code']) . '</code></td>
                            <td class="url-cell">
                                <a href="' . htmlspecialchars($link['next_url']) . '" target="_blank" class="url-link">
                                    ' . htmlspecialchars(substr($link['next_url'], 0, 40)) . (strlen($link['next_url']) > 40 ? '...' : '') . '
                                </a>
                            </td>
                            <td>' . $link['visit_count'] . '</td>
                            <td>
                                <a href="/admin/analytics?code=' . urlencode($link['code']) . '" class="btn btn-small">View Details</a>
                            </td>
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