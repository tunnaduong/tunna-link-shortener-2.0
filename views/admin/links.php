<?php
$content = '
<div class="links-page">
    <div class="page-header">
        <h2>All Links</h2>
        <div class="page-info">
            Showing ' . count($links) . ' of ' . $totalLinks . ' links
        </div>
    </div>
    
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>URL</th>
                    <th>Title</th>
                    <th>Visits</th>
                    <th>Type</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
';

foreach ($links as $link) {
  $redirectType = $link['redirect_type'] == 0 ? 'Direct' :
    ($link['redirect_type'] == 1 ? 'Click' :
      ($link['redirect_type'] == 2 ? 'Captcha' :
        ($link['redirect_type'] == 3 ? 'Password' : 'Unknown')));
  $content .= '
                <tr>
                    <td><a href="/' . htmlspecialchars($link['code']) . '" target="_blank" class="url-link"><code>' . htmlspecialchars($link['code']) . '</code></a></td>
                    <td class="url-cell">
                        <a href="' . htmlspecialchars($link['next_url']) . '" target="_blank" class="url-link">
                            ' . htmlspecialchars(substr($link['next_url'], 0, 40)) . (strlen($link['next_url']) > 40 ? '...' : '') . '
                        </a>
                    </td>
                    <td>' . htmlspecialchars($link['link_title'] ?? 'No title') . '</td>
                    <td>' . $link['visit_count'] . '</td>
                    <td>' . $redirectType . '</td>
                    <td>' . date('M j, Y', strtotime($link['created_at'])) . '</td>
                    <td class="actions">
                        <a href="/admin/edit-link?code=' . urlencode($link['code']) . '" class="btn btn-small">Manage</a>
                        <a href="/admin/analytics?code=' . urlencode($link['code']) . '" class="btn btn-small">Analytics</a>
                        <button onclick="confirmDelete(\'' . htmlspecialchars($link['code']) . '\')" class="btn btn-small btn-danger">Delete</button>
                    </td>
                </tr>
    ';
}

$content .= '
            </tbody>
        </table>
    </div>
    
    <div class="pagination">
';

if ($totalPages > 1) {
  $content .= '<div class="pagination-info">Page ' . $currentPage . ' of ' . $totalPages . '</div>';

  if ($currentPage > 1) {
    $content .= '<a href="/admin/links?page=' . ($currentPage - 1) . '" class="btn">Previous</a>';
  }

  if ($currentPage < $totalPages) {
    $content .= '<a href="/admin/links?page=' . ($currentPage + 1) . '" class="btn">Next</a>';
  }
}

$content .= '
    </div>
</div>
';

// Include the layout
include __DIR__ . '/layout.php';
?>