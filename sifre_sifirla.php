<?php
/**
 * RECOVERY TOOL
 * 1. Repairs admin/login.php (removes redirect loop)
 * 2. Resets admin password to 'admin123'
 */

// 1. REPAIR LOGIN.PHP
$loginFile = __DIR__ . '/admin/login.php';
$cleanLoginContent = <<<'PHP'
<?php
// Session'ı sadece bir kez başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/db.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';

// Zaten giriş yapmışsa dashboard'a yönlendir (force_login ve reset_pass hariç)
$skipLoginCheck = isset($_GET['force_login']) || isset($_GET['reset_pass']);
if (!$skipLoginCheck && isLoggedIn()) {
    redirect(admin_url());
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (login($username, $password)) {
        // CSRF token'ı yenile
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        redirect(admin_url());
    } else {
        $error = 'Kullanıcı adı veya şifre hatalı!';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #fce7f3; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; font-family: sans-serif; }
        .login-card { background: white; padding: 2.5rem; border-radius: 15px; box-shadow: 0 10px 25px rgba(236, 72, 153, 0.15); width: 100%; max-width: 400px; text-align: center; }
        .btn-primary { width: 100%; padding: 0.75rem; background-color: #ec4899; color: white; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; }
        .form-control { width: 100%; padding: 0.75rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .error-message { background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Admin Girişi</h2>
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="username" class="form-control" placeholder="Kullanıcı Adı" required autofocus>
            <input type="password" name="password" class="form-control" placeholder="Şifre" required>
            <button type="submit" class="btn btn-primary">Giriş Yap</button>
        </form>
    </div>
</body>
</html>
PHP;

if (file_put_contents($loginFile, $cleanLoginContent)) {
    echo "<h3 style='color:green'>✓ admin/login.php onarıldı (Redirect loop kaldırıldı)</h3>";
} else {
    echo "<h3 style='color:red'>✗ admin/login.php yazılamadı! Dosya izinlerini kontrol edin.</h3>";
}

// 2. RESET PASSWORD
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/db.php';

try {
    $db = getDB();
    $username = 'Erguvan';
    $new_password = 'admin123';
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);

    // Kullanıcı adını da güncelle (büyük/küçük harf garantisi için)
    $stmt = $db->prepare("UPDATE admin_users SET password = ?, username = ? WHERE id = 1");
    // ID 1 varsayıyoruz, yoksa username ile buluruz
    $stmt->execute([$hashed, $username]);

    if ($stmt->rowCount() > 0) {
        echo "<h3 style='color:green'>✓ Şifre sıfırlandı: admin123</h3>";
    } else {
        // Belki ID 1 değildir, username ile deneyelim
        $stmt = $db->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
        $stmt->execute([$hashed, $username]);
        echo "<h3 style='color:green'>✓ Şifre güncellendi (veya zaten aynıydı)</h3>";
    }

} catch (Exception $e) {
    echo "<p>DB Hatası: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='admin/login.php' style='background:#ec4899;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Giriş Yapmayı Dene</a></p>";
?>