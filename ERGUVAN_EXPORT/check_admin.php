<?php
// Admin kullanıcısını kontrol et ve gerekirse oluştur
require_once __DIR__ . '/database/db.php';

try {
    $db = getDB();
    
    echo "Veritabanı bağlantısı başarılı!<br><br>";
    
    // Admin kullanıcılarını listele
    $stmt = $db->query("SELECT id, username, email, created_at FROM admin_users");
    $users = $stmt->fetchAll();
    
    echo "<h3>Mevcut Admin Kullanıcıları:</h3>";
    if (empty($users)) {
        echo "Hiç admin kullanıcısı yok!<br><br>";
        
        // Admin kullanıcısı oluştur
        echo "<h3>Admin kullanıcısı oluşturuluyor...</h3>";
        $admin_username = 'erguvan';
        $admin_password = password_hash('mihrimah9595', PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, 'info@erguvanpsikoloji.com')");
        $stmt->execute([$admin_username, $admin_password]);
        
        echo "✓ Admin kullanıcısı başarıyla oluşturuldu!<br>";
        echo "Kullanıcı Adı: <strong>erguvan</strong><br>";
        echo "Şifre: <strong>mihrimah9595</strong><br>";
    } else {
        echo "<ul>";
        foreach ($users as $user) {
            echo "<li>ID: {$user['id']} | Kullanıcı Adı: <strong>{$user['username']}</strong> | Email: {$user['email']} | Oluşturulma: {$user['created_at']}</li>";
        }
        echo "</ul><br>";
        
        // Şifreyi güncelle (emin olmak için)
        echo "<h3>Şifre güncelleniyor...</h3>";
        $admin_password = password_hash('mihrimah9595', PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE admin_users SET password = ? WHERE username = 'erguvan'");
        $stmt->execute([$admin_password]);
        echo "✓ Şifre güncellendi!<br>";
        echo "Kullanıcı Adı: <strong>erguvan</strong><br>";
        echo "Şifre: <strong>mihrimah9595</strong><br>";
    }
    
    echo "<br><h3>Test Giriş:</h3>";
    // Test login
    $test_username = 'erguvan';
    $test_password = 'mihrimah9595';
    
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = :username");
    $stmt->execute([':username' => $test_username]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "✓ Kullanıcı bulundu!<br>";
        if (password_verify($test_password, $user['password'])) {
            echo "✓ Şifre doğru!<br>";
            echo "<br><strong style='color: green;'>Giriş bilgileri çalışıyor! Admin paneline giriş yapabilirsiniz.</strong>";
        } else {
            echo "✗ Şifre yanlış!<br>";
        }
    } else {
        echo "✗ Kullanıcı bulunamadı!<br>";
    }
    
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>
