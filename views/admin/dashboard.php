<?php
$content = '
<div class="dashboard">
    <h2>Dashboard Overview</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Links</h3>
            <div class="stat-number">' . $stats['totalLinks'] . '</div>
        </div>
        
        <div class="stat-card">
            <h3>Total Visits</h3>
            <div class="stat-number">' . $stats['totalVisits'] . '</div>
        </div>
        
        <div class="stat-card">
            <h3>Links Today</h3>
            <div class="stat-number">' . $stats['linksToday'] . '</div>
        </div>
        
        <div class="stat-card">
            <h3>Visits Today</h3>
            <div class="stat-number">' . $stats['visitsToday'] . '</div>
        </div>
    </div>
    
    <div class="dashboard-sections">
        <div class="dashboard-section">
            <h3>Recent Links</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>URL</th>
                            <th>Title</th>
                            <th>Visits</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
';

foreach ($recentLinks as $link) {
  $content .= '
                        <tr>
                            <td><code>' . htmlspecialchars($link['code']) . '</code></td>
                            <td class="url-cell">
                                <a href="' . htmlspecialchars($link['next_url']) . '" target="_blank" class="url-link">
                                    ' . htmlspecialchars(substr($link['next_url'], 0, 50)) . (strlen($link['next_url']) > 50 ? '...' : '') . '
                                </a>
                            </td>
                            <td>' . htmlspecialchars($link['link_title'] ?? 'No title') . '</td>
                            <td>' . $link['visit_count'] . '</td>
                            <td>' . date('M j, Y', strtotime($link['created_at'])) . '</td>
                            <td>
                                <a href="/admin/analytics?code=' . urlencode($link['code']) . '" class="btn btn-small">Analytics</a>
                            </td>
                        </tr>
    ';
}

$content .= '
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="dashboard-section">
            <h3>Recent Visits</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Link Code</th>
                            <th>IP Address</th>
                            <th>Location</th>
                            <th>Browser</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
';

foreach ($recentVisits as $visit) {
  $content .= '
                        <tr>
                            <td><code>' . htmlspecialchars($visit['ref_code']) . '</code></td>
                            <td>' . htmlspecialchars($visit['ip_address']) . '</td>
                            <td>' . htmlspecialchars($visit['location'] ?? 'Unknown') . '</td>
                            <td>' . htmlspecialchars($visit['browser'] ?? 'Unknown') . '</td>
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

// The layout is handled by ViewRenderer
// No need to include layout.php directly
echo $content;
?>