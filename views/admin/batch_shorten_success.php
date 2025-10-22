<?php
// Set page title
$pageTitle = 'Batch URL Shortening Results';
?>

<div class="batch-shorten-success-page">
  <div class="success-header">
    <h2>📊 Batch URL Shortening Results</h2>
    <p>Processing completed for <?php echo $total; ?> URLs</p>
  </div>

  <div class="results-summary">
    <div class="summary-cards">
      <div class="summary-card success">
        <h3><?php echo $successful_count; ?></h3>
        <p>Successfully Created</p>
      </div>
      <div class="summary-card warning">
        <h3><?php echo $duplicates_count; ?></h3>
        <p>Already Existed</p>
      </div>
      <div class="summary-card error">
        <h3><?php echo $errors_count; ?></h3>
        <p>Errors</p>
      </div>
    </div>
  </div>

  <div class="results-details">
    <!-- Successful Results -->
    <?php if (!empty($results['successful'])): ?>
      <div class="results-section">
        <h3>✅ Successfully Created (<?php echo count($results['successful']); ?>)</h3>
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
            <tbody>
              <?php foreach ($results['successful'] as $item): ?>
                <tr>
                  <td><?php echo $item['line']; ?></td>
                  <td><a href="<?php echo htmlspecialchars($item['url']); ?>"
                      target="_blank"><?php echo htmlspecialchars(substr($item['url'], 0, 50)); ?><?php echo strlen($item['url']) > 50 ? '...' : ''; ?></a>
                  </td>
                  <td><code><?php echo htmlspecialchars($item['code']); ?></code></td>
                  <td><a href="<?php echo htmlspecialchars($item['short_url']); ?>"
                      target="_blank"><?php echo htmlspecialchars($item['short_url']); ?></a></td>
                  <td>
                    <button onclick="copyToClipboard('<?php echo htmlspecialchars($item['short_url']); ?>')"
                      class="btn btn-small">Copy</button>
                    <a href="/admin/analytics?code=<?php echo urlencode($item['code']); ?>"
                      class="btn btn-small btn-secondary">Analytics</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>

    <!-- Duplicate Results -->
    <?php if (!empty($results['duplicates'])): ?>
      <div class="results-section">
        <h3>⚠️ Already Existed (<?php echo count($results['duplicates']); ?>)</h3>
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
            <tbody>
              <?php foreach ($results['duplicates'] as $item): ?>
                <tr>
                  <td><?php echo $item['line']; ?></td>
                  <td><a href="<?php echo htmlspecialchars($item['url']); ?>"
                      target="_blank"><?php echo htmlspecialchars(substr($item['url'], 0, 50)); ?><?php echo strlen($item['url']) > 50 ? '...' : ''; ?></a>
                  </td>
                  <td><code><?php echo htmlspecialchars($item['code']); ?></code></td>
                  <td><a href="<?php echo htmlspecialchars($item['short_url']); ?>"
                      target="_blank"><?php echo htmlspecialchars($item['short_url']); ?></a></td>
                  <td>
                    <button onclick="copyToClipboard('<?php echo htmlspecialchars($item['short_url']); ?>')"
                      class="btn btn-small">Copy</button>
                    <a href="/admin/analytics?code=<?php echo urlencode($item['code']); ?>"
                      class="btn btn-small btn-secondary">Analytics</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>

    <!-- Error Results -->
    <?php if (!empty($results['errors'])): ?>
      <div class="results-section">
        <h3>❌ Errors (<?php echo count($results['errors']); ?>)</h3>
        <div class="results-table">
          <table>
            <thead>
              <tr>
                <th>Line</th>
                <th>URL</th>
                <th>Error</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results['errors'] as $item): ?>
                <tr>
                  <td><?php echo $item['line']; ?></td>
                  <td>
                    <?php echo htmlspecialchars(substr($item['url'], 0, 50)); ?>
                    <?php echo strlen($item['url']) > 50 ? '...' : ''; ?>
                  </td>
                  <td class="error-message"><?php echo htmlspecialchars($item['error']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <div class="success-actions">
    <a href="/admin/create-link" class="btn">Create More Links</a>
    <a href="/admin/links" class="btn btn-secondary">View All Links</a>
  </div>
</div>

<script>
  function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function () {
      // Show feedback
      const button = event.target;
      const originalText = button.textContent;
      button.textContent = "Copied!";
      button.style.backgroundColor = "#28a745";

      setTimeout(() => {
        button.textContent = originalText;
        button.style.backgroundColor = "";
      }, 2000);
    }).catch(function (err) {
      alert("Failed to copy. Please select and copy manually.");
    });
  }
</script>

<?php
// The layout is handled by ViewRenderer
// No need to include layout.php directly
?>