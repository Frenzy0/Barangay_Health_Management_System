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

        <form method="POST" action="handlers/login.php" class="login-form" id="loginForm" novalidate>
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

<div class="toast-container" id="toastContainer"></div>

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

    /* ===== TOAST ===== */
    const toastContainer = document.getElementById('toastContainer');
    function showToast(title, message, duration = 5000) {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `
            <span class="material-icons toast-icon">error</span>
            <div class="toast-content">
                <div class="toast-title"></div>
                <div class="toast-message"></div>
            </div>
            <button class="toast-close" type="button" aria-label="Close">
                <span class="material-icons">close</span>
            </button>`;
        toast.querySelector('.toast-title').textContent = title;
        toast.querySelector('.toast-message').textContent = message;
        const remove = () => {
            toast.classList.add('removing');
            setTimeout(() => toast.remove(), 300);
        };
        toast.querySelector('.toast-close').addEventListener('click', remove);
        setTimeout(remove, duration);
        toastContainer.appendChild(toast);
    }

    /* ===== PASSWORD REQUIREMENTS CHECK ===== */
    function validatePasswordStrength(pwd) {
        if (pwd.length < 12) return "Password must be at least 12 characters long.";
        if (!/[a-z]/.test(pwd)) return "Password must include a lowercase letter.";
        if (!/[A-Z]/.test(pwd)) return "Password must include an uppercase letter.";
        if (!/[0-9]/.test(pwd)) return "Password must include a number.";
        if (!/[^A-Za-z0-9]/.test(pwd)) return "Password must include a special character.";
        return "";
    }

    document.getElementById('loginForm').addEventListener('submit', e => {
        const username = document.getElementById('username').value.trim();
        const password = pwdInput.value;

        if (!username || !password) {
            e.preventDefault();
            showToast('Missing Credentials', 'Please enter both your username and password.');
            return;
        }
        const pwdErr = validatePasswordStrength(password);
        if (pwdErr) {
            e.preventDefault();
            showToast('Password Requirements', pwdErr);
        }
    });
</script>

</div><!-- /.login-content -->

</body>
</html>
