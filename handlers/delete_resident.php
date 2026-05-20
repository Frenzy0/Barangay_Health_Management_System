<?php
session_start();
header('Content-Type: application/json');
require '../db.php';
require '../helpers/log.php';

$id = (int)($_POST['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'Invalid ID.']);
    exit;
}

// Fetch name before delete (for logging)
$nm = $conn->prepare("SELECT full_name, suffix FROM residents WHERE id = ?");
$nm->bind_param('i', $id);
$nm->execute();
$row    = $nm->get_result()->fetch_assoc();
$base   = $row['full_name'] ?? 'Unknown';
$sx     = trim($row['suffix'] ?? '');
$name   = $sx !== '' ? $base . ' ' . $sx : $base;

$stmt = $conn->prepare("DELETE FROM residents WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    logAction($conn, 'Deleted resident', $name);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'DB error.']);
}
