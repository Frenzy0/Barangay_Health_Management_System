<?php
session_start();
require '../db.php';
require '../helpers/log.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    $_SESSION['login_error'] = 'Please enter both username and password.';
    header('Location: ../login.php');
    exit;
}

// Case-sensitive username match (binary byte comparison).
$stmt = $conn->prepare("SELECT id, username, password_hash FROM admins WHERE BINARY username = ? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if (!$admin || !password_verify($password, $admin['password_hash'])) {
    $_SESSION['login_error'] = 'Invalid username or password.';
    header('Location: ../login.php');
    exit;
}

$_SESSION['admin_id']       = $admin['id'];
$_SESSION['admin_username'] = $admin['username'];
$_SESSION['fresh_login']    = true;
unset($_SESSION['login_error']);

logAction($conn, 'Logged in');

header('Location: ../dashboard.php');
exit;
