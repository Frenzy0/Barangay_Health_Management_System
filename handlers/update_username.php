<?php
session_start();
header('Content-Type: application/json');
require '../db.php';
require '../helpers/log.php';

if (empty($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated.']);
    exit;
}

$admin_id     = (int)$_SESSION['admin_id'];
$new_username = trim($_POST['username'] ?? '');

if (strlen($new_username) < 3 || strlen($new_username) > 50) {
    echo json_encode(['success' => false, 'error' => 'Username must be 3–50 characters.']);
    exit;
}
if (!preg_match('/^[A-Za-z0-9_.\-]+$/', $new_username)) {
    echo json_encode(['success' => false, 'error' => 'Only letters, numbers, dots, underscores, and hyphens allowed.']);
    exit;
}

// Check if taken
$check = $conn->prepare("SELECT id FROM admins WHERE username = ? AND id != ? LIMIT 1");
$check->bind_param('si', $new_username, $admin_id);
$check->execute();
if ($check->get_result()->fetch_assoc()) {
    echo json_encode(['success' => false, 'error' => 'Username is already taken.']);
    exit;
}

// Get old username for log
$prev = $conn->prepare("SELECT username FROM admins WHERE id = ?");
$prev->bind_param('i', $admin_id);
$prev->execute();
$old = $prev->get_result()->fetch_assoc()['username'] ?? '';

if ($old === $new_username) {
    echo json_encode(['success' => true, 'username' => $new_username, 'unchanged' => true]);
    exit;
}

$stmt = $conn->prepare("UPDATE admins SET username = ? WHERE id = ?");
$stmt->bind_param('si', $new_username, $admin_id);

if ($stmt->execute()) {
    $_SESSION['admin_username'] = $new_username;
    logAction($conn, 'Changed username', null, "From '$old' to '$new_username'");
    echo json_encode(['success' => true, 'username' => $new_username]);
} else {
    echo json_encode(['success' => false, 'error' => 'DB error.']);
}
