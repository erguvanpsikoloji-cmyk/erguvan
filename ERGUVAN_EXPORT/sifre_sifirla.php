<?php
/**
 * Admin Şifre Sıfırlama Aracı
 * Bu dosya admin şifrenizi 'erguvan2026' olarak günceller.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/db.php';

echo "<h2>Şifre Sıfırlama İşlemi</h2>";

try {
    $db = getDB();

    // Mevcut kullanıcıyı bul
    $stmt = $db->query("SELECT username FROM admin_users LIMIT 1");
    $user = $stmt->fetch();

    if (!$user) {
        die("<p style='color:red;'>HATA: Veritabanında admin kullanıcısı bulunamadı!</p>");
    }

    $username = $user['username'];
    $new_password = 'erguvan2026'; // Geçici şifre
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $update = $db->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
    $update->execute([$hashed_password, $username]);

    echo "<p style='color:green;'>✓ <b>'$username'</b> kullanıcısının şifresi başarıyla <b>'$new_password'</b> olarak güncellendi.</p>";
    echo "<p>Artık bu şifre ile giriş yapabilirsiniz.</p>";
    echo "<hr><p style='color:red;'>ÖNEMLİ: Güvenliğiniz için bu dosyayı çalıştırdıktan sonra sunucudan HEMEN silin.</p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>HATA: " . $e->getMessage() . "</p>";
}

// Güvenlik için kendini silebilir ancak kullanıcıya rapor vermesi için şimdilik kalsın.
// unlink(__FILE__); 
?>