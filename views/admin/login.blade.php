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
        <div class="login-form">
            <h1>Admin Login</h1>

            @if (isset($error))
                <div class="error-message">{{ $error }}</div>
            @endif

            <form method="POST" action="/admin/login">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
</body>

</html>
