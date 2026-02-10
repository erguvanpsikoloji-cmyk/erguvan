<?php
/**
 * Admin Şifre Kontrolü ve Sıfırlama
 * Mevcut admin bilgilerini gösterir ve şifreyi sıfırlar
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/db.php';

echo "<h2>Admin Kullanıcı Yönetimi</h2>";
echo "<style>body{font-family:Arial;padding:20px;} table{border-collapse:collapse;margin:20px 0;} td,th{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#ec4899;color:white;}</style>";

try {
    $db = getDB();

    // Tüm admin kullanıcıları göster
    echo "<h3>Mevcut Admin Kullanıcıları:</h3>";
    $stmt = $db->query("SELECT id, username, created_at FROM admin_users");
    $users = $stmt->fetchAll();

    if (empty($users)) {
        die("<p style='color:red;'>HATA: Veritabanında admin kullanıcısı bulunamadı!</p>");
    }

    echo "<table><tr><th>ID</th><th>Kullanıcı Adı</th><th>Oluşturulma</th></tr>";
    foreach ($users as $user) {
        echo "<tr><td>{$user['id']}</td><td><b>{$user['username']}</b></td><td>{$user['created_at']}</td></tr>";
    }
    echo "</table>";

    // İlk kullanıcının şifresini sıfırla
    $username = $users[0]['username'];
    $new_password = 'erguvan2026';
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $update = $db->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
    $update->execute([$hashed_password, $username]);

    echo "<div style='background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:15px;margin:20px 0;border-radius:5px;'>";
    echo "<h3 style='margin:0 0 10px 0;'>✓ Şifre Başarıyla Güncellendi</h3>";
    echo "<p><b>Kullanıcı Adı:</b> <code style='background:#fff;padding:4px 8px;border-radius:3px;'>{$username}</code></p>";
    echo "<p><b>Yeni Şifre:</b> <code style='background:#fff;padding:4px 8px;border-radius:3px;'>{$new_password}</code></p>";
    echo "</div>";

    echo "<p><a href='admin/login.php?bypass=1' style='display:inline-block;background:#ec4899;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Admin Panele Git (Bypass Mode)</a></p>";

    echo "<hr>";
    echo "<p style='color:red;'><b>⚠️ GÜVENLİK UYARISI:</b> Bu dosyayı kullandıktan sonra MUTLAKA sunucudan silin!</p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>HATA: " . $e->getMessage() . "</p>";
}
?>