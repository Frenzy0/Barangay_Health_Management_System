<?php
require 'db.php';

$conn->query("
    CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$username = 'admin';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO admins (username, password_hash) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)"
);
$stmt->bind_param('ss', $username, $hash);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Setup Admin — BHMS</title>
    <style>
        body { font-family: Arial,sans-serif; padding: 40px; max-width: 600px; margin: 0 auto; background: #f4f6f9; }
        .box { background: #fff; padding: 32px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,.05); }
        .ok  { color: #059669; font-size: 18px; font-weight: 700; margin-bottom: 12px; }
        .err { color: #ef4444; font-size: 18px; font-weight: 700; }
        code { background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 14px; }
        a { color: #0d9488; font-weight: 600; }
        .warn { background: #fef3c7; color: #92400e; padding: 12px 16px; border-radius: 8px; margin-top: 18px; font-size: 14px; }
    </style>
</head>
<body>
<div class="box">
<?php if ($stmt->execute()): ?>
    <p class="ok">✅ Admin account ready</p>
    <p>Username: <code><?= $username ?></code></p>
    <p>Password: <code><?= $password ?></code></p>
    <p style="margin-top:18px;"><a href="login.php">→ Go to Login</a></p>
    <div class="warn">⚠️ Delete <code>setup_admin.php</code> after first run for security.</div>
<?php else: ?>
    <p class="err">❌ Error: <?= htmlspecialchars($conn->error) ?></p>
<?php endif; ?>
</div>
</body>
</html>
