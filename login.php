<?php
session_start();
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — BHMS</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<nav class="login-navbar">
    <div class="login-navbar-content">
        <a href="index.php" class="login-navbar-brand">
            <span class="material-icons">health_and_safety</span>
            <h1>BHMS</h1>
        </a>
        <a href="index.php" class="login-navbar-link">
            <span class="material-icons">assignment</span>
            <span class="login-navbar-link-text">Survey Form</span>
        </a>
    </div>
</nav>

<div class="login-content">

<div class="login-card">

    <!-- LEFT: Brand Panel -->
    <div class="login-brand">
        <div class="brand-decor decor-top"></div>
        <div class="brand-decor decor-bottom"></div>
        <div class="brand-content">
            <h1>BHMS</h1>
            <p class="brand-tagline">Barangay Health Management System</p>
            <div class="brand-features">
                <div class="feature-item">
                    <span class="material-icons">groups</span>
                    <span>Manage Resident Profiles</span>
                </div>
                <div class="feature-item">
                    <span class="material-icons">favorite</span>
                    <span>Track Health &amp; Vaccinations</span>
                </div>
                <div class="feature-item">
                    <span class="material-icons">assignment</span>
                    <span>Organize Health Surveys</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Form Panel -->
    <div class="login-form-panel">
        <div class="form-header">
            <h2>Welcome Back</h2>
            <p>Please enter your credentials to continue.</p>
        </div>

        <?php if ($error): ?>
        <div class="login-error">
            <span class="material-icons">error</span>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="handlers/login.php" class="login-form">
            <div class="form-field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" autofocus>
            </div>

            <div class="form-field">
                <label for="password">Password</label>
                <div class="password-wrap">
                    <input type="password" id="password" name="password" placeholder="Enter your password">
                    <button type="button" id="togglePwd" class="toggle-pwd" aria-label="Toggle password">
                        <span class="material-icons">visibility</span>
                    </button>
                </div>
            </div>

            <label class="remember-row">
                <input type="checkbox" name="remember" id="remember">
                Remember me
            </label>

            <button type="submit" class="login-submit-btn">Sign In</button>
        </form>
    </div>

</div>

<script>
    const togglePwd = document.getElementById('togglePwd');
    const pwdInput  = document.getElementById('password');
    const eyeIcon   = togglePwd.querySelector('.material-icons');
    togglePwd.addEventListener('click', () => {
        if (pwdInput.type === 'password') {
            pwdInput.type = 'text';
            eyeIcon.textContent = 'visibility_off';
        } else {
            pwdInput.type = 'password';
            eyeIcon.textContent = 'visibility';
        }
    });
</script>

</div><!-- /.login-content -->

</body>
</html>
