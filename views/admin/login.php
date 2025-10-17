<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Tunna Link Shortener</title>
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>

<body>
  <div class="login-container">
    <div class="login-box">
      <h1>Admin Login</h1>

      <?php if (isset($error)): ?>
        <div class="error-message">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="/admin/login" class="login-form">
        <div class="form-group">
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="login-btn">Login</button>
      </form>
    </div>
  </div>
</body>

</html>