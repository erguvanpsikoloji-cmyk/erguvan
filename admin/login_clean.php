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
        $error = 'Kullanıcı adı veya şifre hatalı! <br><small>Şifreyi sıfırlamak için <a href="?reset_pass=yes" style="color:#ec4899;">buraya tıklayın</a></small>';
    }
}

// ACİL DURUM DOĞRUDAN GİRİŞ
if (isset($_GET['force_login']) && $_GET['force_login'] === 'yes') {
    try {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM admin_users LIMIT 1");
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Giriş Başarılı</title>";
            echo "<style>body{font-family:Arial;text-align:center;padding:50px;background:#f5f5f5;} .box{background:white;padding:40px;border-radius:10px;max-width:400px;margin:0 auto;box-shadow:0 2px 10px rgba(0,0,0,0.1);} h1{color:#28a745;}</style></head><body>";
            echo "<div class='box'><h1>✅ Giriş Başarılı!</h1>";
            echo "<p>Hoş geldiniz <strong>" . htmlspecialchars($user['username']) . "</strong></p>";
            echo "<p>2 saniye içinde yönetim paneline yönlendiriliyorsunuz...</p>";
            echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 2000);</script></div></body></html>";
            exit;
        }
    } catch (Exception $e) {
        echo "HATA: " . $e->getMessage();
        exit;
    }
}

// ACİL DURUM ŞİFRE SIFIRLAMA
if (isset($_GET['reset_pass']) && $_GET['reset_pass'] === 'yes') {
    try {
        $db = getDB();
        $stmt = $db->query("SELECT id, username FROM admin_users LIMIT 1");
        $user = $stmt->fetch();

        if ($user) {
            $new_password = 'admin123';
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $db->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            $update->execute([$hashed, $user['id']]);

            echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Şifre Sıfırlandı</title>";
            echo "<style>body{font-family:Arial;padding:40px;background:#f5f5f5;} .box{background:white;padding:30px;border-radius:10px;max-width:500px;margin:0 auto;box-shadow:0 2px 10px rgba(0,0,0,0.1);} .success{background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:15px;border-radius:5px;margin:20px 0;} code{background:#f8f9fa;padding:4px 8px;border-radius:3px;color:#e83e8c;} a{display:inline-block;background:#ec4899;color:white;padding:12px 24px;text-decoration:none;border-radius:5px;margin-top:20px;}</style></head><body>";
            echo "<div class='box'><h2>✅ Şifre Başarıyla Sıfırlandı!</h2>";
            echo "<div class='success'><p><strong>Kullanıcı Adı:</strong> <code>" . htmlspecialchars($user['username']) . "</code></p>";
            echo "<p><strong>Yeni Şifre:</strong> <code>admin123</code></p></div>";
            echo "<a href='login.php?force_login=yes'>Doğrudan Giriş Yap</a></div></body></html>";
            exit;
        }
    } catch (Exception $e) {
        echo "HATA: " . $e->getMessage();
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi - Uzm. Psk. Sena Ceren</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fce7f3;
            /* pink-100 */
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(236, 72, 153, 0.15);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .logo-area {
            margin-bottom: 2rem;
        }

        .logo-icon {
            font-size: 3rem;
            color: #ec4899;
            /* pink-500 */
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #374151;
            /* gray-700 */
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #ec4899;
            outline: none;
            background-color: #fdf2f8;
        }

        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            background-color: #ec4899;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-primary:hover {
            background-color: #db2777;
        }

        .error-message {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 1px solid #fecaca;
            font-size: 0.9rem;
        }

        .site-title {
            color: #1f2937;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .site-subtitle {
            color: #6b7280;
            font-size: 0.875rem;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="logo-area">
            <div class="logo-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="site-title">Uzm. Psk. Sena Ceren</div>
            <div class="site-subtitle">Admin Paneli</div>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
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
</body>

</html>