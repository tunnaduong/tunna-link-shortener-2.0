<?php
$content = '
<div class="batch-shorten-success-page">
    <div class="success-header">
        <h2>📊 Batch URL Shortening Results</h2>
        <p>Processing completed for ' . $total . ' URLs</p>
    </div>
    
    <div class="results-summary">
        <div class="summary-cards">
            <div class="summary-card success">
                <h3>' . $successful_count . '</h3>
                <p>Successfully Created</p>
            </div>
            <div class="summary-card warning">
                <h3>' . $duplicates_count . '</h3>
                <p>Already Existed</p>
            </div>
            <div class="summary-card error">
                <h3>' . $errors_count . '</h3>
                <p>Errors</p>
            </div>
        </div>
    </div>
    
    <div class="results-details">';

// Successful Results
if (!empty($results['successful'])) {
  $content .= '
        <div class="results-section">
            <h3>✅ Successfully Created (' . count($results['successful']) . ')</h3>
            <div class="results-table">
                <table>
                    <thead>
                        <tr>
                            <th>Line</th>
                            <th>Original URL</th>
                            <th>Short Code</th>
                            <th>Short URL</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>';

  foreach ($results['successful'] as $item) {
    $content .= '
                        <tr>
                            <td>' . $item['line'] . '</td>
                            <td><a href="' . htmlspecialchars($item['url']) . '" target="_blank">' . htmlspecialchars(substr($item['url'], 0, 50)) . (strlen($item['url']) > 50 ? '...' : '') . '</a></td>
                            <td><code>' . htmlspecialchars($item['code']) . '</code></td>
                            <td><a href="' . htmlspecialchars($item['short_url']) . '" target="_blank">' . htmlspecialchars($item['short_url']) . '</a></td>
                            <td>
                                <button onclick="copyToClipboard(\'' . htmlspecialchars($item['short_url']) . '\')" class="btn btn-small">Copy</button>
                                <a href="/admin/analytics?code=' . urlencode($item['code']) . '" class="btn btn-small btn-secondary">Analytics</a>
                            </td>
                        </tr>';
  }

  $content .= '
                    </tbody>
                </table>
            </div>
        </div>';
}

// Duplicate Results
if (!empty($results['duplicates'])) {
  $content .= '
        <div class="results-section">
            <h3>⚠️ Already Existed (' . count($results['duplicates']) . ')</h3>
            <div class="results-table">
                <table>
                    <thead>
                        <tr>
                            <th>Line</th>
                            <th>Original URL</th>
                            <th>Existing Code</th>
                            <th>Short URL</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>';

  foreach ($results['duplicates'] as $item) {
    $content .= '
                        <tr>
                            <td>' . $item['line'] . '</td>
                            <td><a href="' . htmlspecialchars($item['url']) . '" target="_blank">' . htmlspecialchars(substr($item['url'], 0, 50)) . (strlen($item['url']) > 50 ? '...' : '') . '</a></td>
                            <td><code>' . htmlspecialchars($item['code']) . '</code></td>
                            <td><a href="' . htmlspecialchars($item['short_url']) . '" target="_blank">' . htmlspecialchars($item['short_url']) . '</a></td>
                            <td>
                                <button onclick="copyToClipboard(\'' . htmlspecialchars($item['short_url']) . '\')" class="btn btn-small">Copy</button>
                                <a href="/admin/analytics?code=' . urlencode($item['code']) . '" class="btn btn-small btn-secondary">Analytics</a>
                            </td>
                        </tr>';
  }

  $content .= '
                    </tbody>
                </table>
            </div>
        </div>';
}

// Error Results
if (!empty($results['errors'])) {
  $content .= '
        <div class="results-section">
            <h3>❌ Errors (' . count($results['errors']) . ')</h3>
            <div class="results-table">
                <table>
                    <thead>
                        <tr>
                            <th>Line</th>
                            <th>URL</th>
                            <th>Error</th>
                        </tr>
                    </thead>
                    <tbody>';

  foreach ($results['errors'] as $item) {
    $content .= '
                        <tr>
                            <td>' . $item['line'] . '</td>
                            <td>' . htmlspecialchars(substr($item['url'], 0, 50)) . (strlen($item['url']) > 50 ? '...' : '') . '</td>
                            <td class="error-message">' . htmlspecialchars($item['error']) . '</td>
                        </tr>';
  }

  $content .= '
                    </tbody>
                </table>
            </div>
        </div>';
}

$content .= '
    </div>
    
    <div class="success-actions">
        <a href="/admin/create-link" class="btn">Create More Links</a>
        <a href="/admin/links" class="btn btn-secondary">View All Links</a>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show feedback
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = "Copied!";
        button.style.backgroundColor = "#28a745";
        
        setTimeout(() => {
            button.textContent = originalText;
            button.style.backgroundColor = "";
        }, 2000);
    }).catch(function(err) {
        alert("Failed to copy. Please select and copy manually.");
    });
}
</script>
';

// Include the layout
include __DIR__ . '/layout.php';
?>