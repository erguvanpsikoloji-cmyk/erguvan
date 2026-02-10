<?php
require_once __DIR__ . '/database/db.php';
$db = getDB();
$stmt = $db->query("SELECT id, username FROM admin_users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<h1>Admin Kullanıcıları</h1>";
foreach ($users as $user) {
    echo "ID: " . $user['id'] . " - Kullanıcı Adı: " . $user['username'] . "<br>";
}
unlink(__FILE__);
?>