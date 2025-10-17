<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Tunna Link Shortener</title>
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>

<body>
  <div class="admin-container">
    <header class="admin-header">
      <div class="admin-header-content">
        <h1>Admin Panel</h1>
        <nav class="admin-nav">
          <a href="/admin/create-link" class="nav-link create-link">Create Link</a>
          <a href="/admin" class="nav-link">Dashboard</a>
          <a href="/admin/links" class="nav-link">Links</a>
          <a href="/admin/analytics" class="nav-link">Analytics</a>
          <a href="/admin/logout" class="nav-link logout">Logout</a>
        </nav>
      </div>
    </header>

    <main class="admin-main">
      <?php if (isset($error)): ?>
        <div class="error-message">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <?php if (isset($success)): ?>
        <div class="success-message">
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <?= $content ?? '' ?>
    </main>

    <footer class="admin-footer">
      <p>&copy; <?= date('Y') ?> Tunna Link Shortener Admin Panel</p>
    </footer>
  </div>

  <script>
    // Simple JavaScript for admin functionality
    function confirmDelete(code) {
      if (confirm('Are you sure you want to delete this link?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/delete-link';

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'code';
        input.value = code;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
      }
    }

    // Auto-hide messages after 5 seconds
    setTimeout(function () {
      const messages = document.querySelectorAll('.error-message, .success-message');
      messages.forEach(function (msg) {
        msg.style.opacity = '0';
        setTimeout(function () {
          msg.remove();
        }, 500);
      });
    }, 5000);
  </script>
</body>

</html>