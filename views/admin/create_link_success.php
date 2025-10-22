<?php
$content = '
<div class="create-link-success-page">
    <div class="success-header">
        <h2>✅ Link Created Successfully!</h2>
        <p>Your new short link has been created and is ready to use.</p>
    </div>
    
    <div class="link-details">
        <div class="link-info-card">
            <h3>Link Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Short Code:</strong>
                    <code class="link-code">
                        <a href="' . ($_ENV['APP_URL'] ?? 'https://tunn.ad') . '/' . htmlspecialchars($link->getCode()) . '" target="_blank">
                            ' . htmlspecialchars($link->getCode()) . '
                        </a>
                    </code>
                </div>
                <div class="info-item">
                    <strong>Short URL:</strong>
                    <a href="' . htmlspecialchars($link->getNextUrl()) . '" target="_blank" class="url-link">
                        ' . htmlspecialchars($link->getNextUrl()) . '
                    </a>
                </div>
                <div class="info-item">
                    <strong>Destination URL:</strong>
                    <a href="' . htmlspecialchars($link->getNextUrl()) . '" target="_blank" class="url-link">
                        ' . htmlspecialchars($link->getNextUrl()) . '
                    </a>
                </div>
                <div class="info-item">
                    <strong>Title:</strong>
                    ' . htmlspecialchars($link->getLinkTitle() ?? 'No title') . '
                </div>
                <div class="info-item">
                    <strong>Type:</strong>
                    ' . ($link->getRedirectType() == 0 ? 'Direct Redirect' :
  ($link->getRedirectType() == 1 ? 'Click Through' :
    ($link->getRedirectType() == 2 ? 'reCAPTCHA Protected' :
      ($link->getRedirectType() == 3 ? 'Password Protected' : 'Unknown')))) . '
                </div>
                <div class="info-item">
                    <strong>Wait Time:</strong>
                    ' . $link->getWaitSeconds() . ' seconds
                </div>
                <div class="info-item">
                    <strong>Preview Image:</strong>
                    ' . ($link->getLinkPreviewUrl() ? '<a href="' . htmlspecialchars($link->getLinkPreviewUrl()) . '" target="_blank">View Image</a>' : 'None') . '
                </div>
                <div class="info-item">
                    <strong>Advertisement Image:</strong>
                    ' . ($link->getAdsImgUrl() ? '<a href="' . htmlspecialchars($link->getAdsImgUrl()) . '" target="_blank">View Ad Image</a>' : 'None') . '
                </div>
                <div class="info-item">
                    <strong>Ad Click URL:</strong>
                    ' . ($link->getAdsClickUrl() ? '<a href="' . htmlspecialchars($link->getAdsClickUrl()) . '" target="_blank">' . htmlspecialchars($link->getAdsClickUrl()) . '</a>' : 'None') . '
                </div>
                <div class="info-item">
                    <strong>Promoted By:</strong>
                    ' . htmlspecialchars($link->getAdsPromotedBy() ?? 'None') . '
                </div>
            </div>
        </div>
        
        <div class="link-actions">
            <h3>Quick Actions</h3>
            <div class="action-buttons">
                <a href="/' . htmlspecialchars($link->getCode()) . '" target="_blank" class="btn">
                    Test Link
                </a>
                <a href="/admin/analytics?code=' . urlencode($link->getCode()) . '" class="btn btn-secondary">
                    View Analytics
                </a>
                <a href="/admin/links" class="btn btn-secondary">
                    Manage All Links
                </a>
            </div>
        </div>
        
        <div class="share-section">
            <h3>Share Your Link</h3>
            <div class="share-options">
                <div class="copy-section">
                    <label for="short-url">Short URL:</label>
                    <div class="copy-input">
                        <input type="text" id="short-url" value="' . ($_ENV['APP_URL'] ?? 'https://tunn.ad') . '/' . htmlspecialchars($link->getCode()) . '" readonly>
                        <button onclick="copyToClipboard(\'short-url\')" class="btn btn-small">Copy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="success-actions">
        <a href="/admin/create-link" class="btn">Create Another Link</a>
        <a href="/admin" class="btn btn-secondary">Go to Dashboard</a>
    </div>
</div>

<script>
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand("copy");
        // Show feedback
        const button = element.nextElementSibling;
        const originalText = button.textContent;
        button.textContent = "Copied!";
        button.style.backgroundColor = "#28a745";
        
        setTimeout(() => {
            button.textContent = originalText;
            button.style.backgroundColor = "";
        }, 2000);
    } catch (err) {
        alert("Failed to copy. Please select and copy manually.");
    }
}
</script>
';

// The layout is handled by ViewRenderer
// No need to include layout.php directly
echo $content;
?>