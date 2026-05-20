<?php
$conn = new mysqli('localhost', 'root', '', 'bhms_db');
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'DB connection failed: ' . $conn->connect_error]));
}
$conn->set_charset('utf8mb4');
