@extends('admin.layout')

@section('content')
    <div class="create-link-page">
        <h2>Create New Link</h2>

        @if (isset($error))
            <div class="error-message">{{ $error }}</div>
        @endif

        @if (isset($success))
            <div class="success-message">{{ $success }}</div>
        @endif

        <form method="POST" action="/admin/create-link" class="link-form">
            <div class="form-group">
                <label for="next_url">Destination URL:</label>
                <input type="url" id="next_url" name="next_url" required placeholder="https://example.com">
            </div>

            <div class="form-group">
                <label for="link_title">Title (optional):</label>
                <input type="text" id="link_title" name="link_title" placeholder="Link title">
            </div>

            <div class="form-group">
                <label for="link_excerpt">Description (optional):</label>
                <textarea id="link_excerpt" name="link_excerpt" placeholder="Link description"></textarea>
            </div>

            <div class="form-group">
                <label for="redirect_type">Redirect Type:</label>
                <select id="redirect_type" name="redirect_type">
                    <option value="0">Direct Redirect</option>
                    <option value="1">Click to Continue</option>
                    <option value="2">reCAPTCHA Protected</option>
                    <option value="3">Password Protected</option>
                </select>
            </div>

            <div class="form-group" id="password-group" style="display: none;">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter password">
            </div>

            <div class="form-group">
                <label for="wait_seconds">Wait Time (seconds):</label>
                <input type="number" id="wait_seconds" name="wait_seconds" value="5" min="0" max="60">
            </div>

            <div class="form-group">
                <label for="countdown_delay">Countdown Delay (milliseconds):</label>
                <input type="number" id="countdown_delay" name="countdown_delay" value="1000" min="100"
                    max="5000">
            </div>

            <button type="submit" class="btn btn-primary">Create Link</button>
        </form>
    </div>

    <script>
        document.getElementById('redirect_type').addEventListener('change', function() {
            const passwordGroup = document.getElementById('password-group');
            if (this.value === '3') {
                passwordGroup.style.display = 'block';
            } else {
                passwordGroup.style.display = 'none';
            }
        });
    </script>
@endsection
