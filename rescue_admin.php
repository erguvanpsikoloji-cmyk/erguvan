<?php
/**
 * ACİL DURUM KURTARMA DOSYASI (ADMIN)
 * Bu dosya 404 hatalarını bypass etmek ve doğrudan giriş sağlamak için oluşturulmuştur.
 */

// Hataları göster (Sorun tespiti için)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // 1. Gerekli dosyaları yükle
    $configPath = __DIR__ . '/config.php';
    $dbPath = __DIR__ . '/database/db.php';

    if (!file_exists($configPath) || !file_exists($dbPath)) {
        throw new Exception("Kritik dosyalar eksik: config.php veya database/db.php");
    }

    require_once $configPath;
    require_once $dbPath;

    // 2. Veritabanı bağlantısı
    $db = getDB();
    if (!$db) {
        throw new Exception("Veritabanına bağlanılamadı.");
    }

    // 3. İlk aktif admin kullanıcısını al
    $stmt = $db->query("SELECT * FROM admin_users LIMIT 1");
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception("Sistemde admin kullanıcısı bulunamadı (admin_users tablosu boş olabilir).");
    }

    // 4. Oturumu manuel başlat (Force Login)
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_username'] = $user['username'];
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // 5. Başarı Mesajı ve Yönlendirme
    ?>
    <!DOCTYPE html>
    <html lang="tr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Giriş Başarılı - Erguvan Psikoloji</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: #fdf2f8;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
            }

            .card {
                background: white;
                padding: 3rem;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(219, 39, 119, 0.1);
                text-align: center;
                max-width: 450px;
            }

            .icon {
                font-size: 4rem;
                margin-bottom: 1.5rem;
            }

            h1 {
                color: #db2777;
                margin-bottom: 1rem;
            }

            p {
                color: #4b5563;
                font-size: 1.1rem;
                line-height: 1.6;
            }

            .btn {
                display: inline-block;
                background: #db2777;
                color: white;
                padding: 12px 30px;
                text-decoration: none;
                border-radius: 10px;
                font-weight: 600;
                margin-top: 1.5rem;
                transition: background 0.3s;
            }

            .btn:hover {
                background: #be185d;
            }

            .debug-info {
                margin-top: 2rem;
                font-size: 0.8rem;
                color: #9ca3af;
                text-align: left;
                background: #f9fafb;
                padding: 10px;
                border-radius: 8px;
            }
        </style>
    </head>

    <body>
        <div class="card">
            <div class="icon">🌸</div>
            <h1>Yönetici Girişi Yapıldı</h1>
            <p>Hoş geldiniz, <strong>
                    <?php echo htmlspecialchars($user['username']); ?>
                </strong>. Hesabınız başarıyla kurtarıldı ve oturumunuz açıldı.</p>

            <a href="admin/index.php" class="btn">Admin Paneline Git</a>

            <div class="debug-info">
                <strong>Sistem Notu:</strong> Eğer yukarıdaki butona bastığınızda tekrar 404 alırsanız, sunucuda
                <code>admin/index.php</code> dosyasının fiziksel olarak var olduğunu veya <code>.htaccess</code>
                yönlendirmelerini kontrol etmemiz gerekecek.
            </div>
        </div>
        <script>
            // Buton tıklandığında 404 olasılığına karşı kullanıcıyı uyarmak için kısa bekleme
            console.log("Oturum açıldı, yönlendirme linki hazır.");
        </script>
    </body>

    </html>
    <?php
} catch (Exception $e) {
    echo "<div style='background:#fee2e2; border:2px solid #ef4444; color:#991b1b; padding:20px; border-radius:10px; font-family:sans-serif; margin:20px;'>";
    echo "<h2>❌ Kurtarma Başarısız Oldu</h2>";
    echo "<strong>Hata Mesajı:</strong> " . htmlspecialchars($e->getMessage());
    echo "<br><br>Lütfen bu hatayı kopyalayıp bana bildirin.";
    echo "</div>";
}
