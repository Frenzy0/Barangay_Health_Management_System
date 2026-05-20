<?php
function logAction($conn, $action, $target = null, $details = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['admin_id'])) return;

    $admin_id = $_SESSION['admin_id'];
    $stmt = $conn->prepare(
        "INSERT INTO admin_logs (admin_id, action, target, details) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('isss', $admin_id, $action, $target, $details);
    $stmt->execute();
}
