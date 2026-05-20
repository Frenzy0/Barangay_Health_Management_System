<?php
session_start();
header('Content-Type: application/json');
require '../db.php';
require '../helpers/log.php';

if (empty($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated.']);
    exit;
}

$admin_id = (int)$_SESSION['admin_id'];
$old_pwd  = $_POST['old_password'] ?? '';
$new_pwd  = $_POST['new_password'] ?? '';

if (!$old_pwd || !$new_pwd) {
    echo json_encode(['success' => false, 'error' => 'Please provide both old and new passwords.']);
    exit;
}
if (strlen($new_pwd) < 6) {
    echo json_encode(['success' => false, 'error' => 'New password must be at least 6 characters.']);
    exit;
}

$stmt = $conn->prepare("SELECT password_hash FROM admins WHERE id = ?");
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row || !password_verify($old_pwd, $row['password_hash'])) {
    echo json_encode(['success' => false, 'error' => 'Old password is incorrect.']);
    exit;
}

if (password_verify($new_pwd, $row['password_hash'])) {
    echo json_encode(['success' => false, 'error' => 'New password must be different from the old one.']);
    exit;
}

$new_hash = password_hash($new_pwd, PASSWORD_DEFAULT);
$upd = $conn->prepare("UPDATE admins SET password_hash = ? WHERE id = ?");
$upd->bind_param('si', $new_hash, $admin_id);

if ($upd->execute()) {
    logAction($conn, 'Changed password');
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'DB error.']);
}
