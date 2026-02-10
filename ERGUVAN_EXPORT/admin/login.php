<?php
// Session'ı sadece bir kez başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';

// Zaten giriş yapmışsa dashboard'a yönlendir
if (isLoggedIn()) {
    redirect(admin_url());
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Geçersiz istek!';
    } else {
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
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi - Uzm. Psk. Sena Ceren</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(admin_asset_url('admin.css')); ?>">
</head>

<body>
    <div class="login-wrapper">
        <div class="login-box">
            <div class="login-logo">
                <!-- Admin Icon -->
                <div class="login-icon">
                    <?php
                    $adminIcon = admin_asset_url('admin-icon.png');
                    $adminIconPath = __DIR__ . '/assets/admin-icon.png';
                    ?>
                    <?php if (file_exists($adminIconPath)): ?>
                        <img src="<?php echo $adminIcon; ?>" alt="Admin" class="admin-icon-image">
                    <?php else: ?>
                        <!-- Fallback SVG Icon -->
                        <svg class="admin-icon-svg" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="brainGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#ec4899;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#db2777;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <!-- Head silhouette -->
                            <path
                                d="M100,180 C80,180 70,170 70,150 L70,100 C70,80 80,70 100,70 C120,70 130,80 130,100 L130,150 C130,170 120,180 100,180 Z"
                                fill="#1e293b" />
                            <!-- Brain/Tree motif -->
                            <circle cx="100" cy="60" r="25" fill="url(#brainGradient)" />
                            <path d="M90,50 Q85,40 80,35 M95,45 Q92,35 90,30 M105,45 Q108,35 110,30 M110,50 Q115,40 120,35"
                                stroke="url(#brainGradient)" stroke-width="3" fill="none" />
                            <ellipse cx="95" cy="55" rx="8" ry="10" fill="url(#brainGradient)" opacity="0.8" />
                            <ellipse cx="105" cy="55" rx="8" ry="10" fill="url(#brainGradient)" opacity="0.8" />
                        </svg>
                    <?php endif; ?>
                </div>
                <h1>Uzm. Psk. Sena Ceren</h1>
                <p>Admin Paneli</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <?php echo csrfField(); ?>
                <div class="form-group">
                    <label for="username">Kullanıcı Adı</label>
                    <input type="text" id="username" name="username" class="form-control" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Şifre</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Giriş Yap</button>
            </form>
        </div>
    </div>
</body>

</html>