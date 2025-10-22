<?php
$content = '
<div class="error-page">
    <div class="error-content">
        <h2>Error</h2>
        <div class="error-message">
            ' . htmlspecialchars($error) . '
        </div>
        <div class="error-actions">
            <a href="/admin" class="btn">Go to Dashboard</a>
            <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
        </div>
    </div>
</div>
';

// The layout is handled by ViewRenderer
// No need to include layout.php directly
?>