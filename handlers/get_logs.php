<?php
session_start();
header('Content-Type: application/json');
require '../db.php';

if (empty($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated.']);
    exit;
}

$admin_id = (int)$_SESSION['admin_id'];

$stmt = $conn->prepare(
    "SELECT action, target, details, created_at
     FROM admin_logs
     WHERE admin_id = ?
     ORDER BY created_at DESC
     LIMIT 50"
);
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode(['success' => true, 'logs' => $logs]);
