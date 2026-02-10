<?php
// Session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/db.php';

try {
    // DB bağlantısı
    $db = getDB();

    // İlk admin kullanıcısını al
    $stmt = $db->query("SELECT * FROM admin_users LIMIT 1");
    $user = $stmt->fetch();

    if ($user) {
        // Session değişkenlerini manuel ata
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        echo "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Giriş Yapılıyor...</title>
            <style>
                body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #fce7f3; margin: 0; }
                .card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; }
                h1 { color: #db2777; }
                .btn { display: inline-block; background: #db2777; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='card'>
                <h1>✅ Giriş Başarılı</h1>
                <p>Hoş geldiniz, <strong>" . htmlspecialchars($user['username']) . "</strong></p>
                <p>Yönetim paneline yönlendiriliyorsunuz...</p>
                <a href='index.php' class='btn'>Manuel Yönlendirme</a>
            </div>
            <script>
                setTimeout(function(){ window.location.href = 'index.php'; }, 1500);
            </script>
        </body>
        </html>";
        exit;
    } else {
        echo "Hata: Kullanıcı bulunamadı.";
    }

} catch (Exception $e) {
    echo "Veritabanı Hatası: " . $e->getMessage();
}
?>