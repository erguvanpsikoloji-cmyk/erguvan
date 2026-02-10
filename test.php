<?php
// TEST - Admin bilgileri göster
require_once 'config.php';
require_once 'database/db.php';

echo "<h2>Admin User Info</h2>";
$db = getDB();
$stmt = $db->query("SELECT username FROM admin_users LIMIT 1");
$user = $stmt->fetch();
echo "<p>Username: <strong>" . ($user ? $user['username'] : 'NOT FOUND') . "</strong></p>";

// Şifre hash'i oluştur
$password = 'test123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "<p>Password: test123</p>";
echo "<p>Hash to use: <code>$hash</code></p>";
?>