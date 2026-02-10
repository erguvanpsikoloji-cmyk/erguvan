<?php
// Session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/db.php';
require_once 'includes/auth.php';

// Hata mesajı
$error = '';

/**
 * GÜVENLİK VE GİRİŞ İŞLEMLERİ
 */

// 1. Zaten giriş yapmışsa yönlendir
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Ancak force_login veya çıkış parametresi varsa yönlendirme
    if (!isset($_GET['force_login']) && !isset($_GET['logout'])) {
        header("Location: index.php");
        exit;
    }
}

// 2. ACİL DURUM: Force Login (Şifresiz Giriş)
if (isset($_GET['force_login']) && $_GET['force_login'] === 'yes') {
    try {
        $db = getDB();
        $user = $db->query("SELECT * FROM admin_users LIMIT 1")->fetch();

        if ($user) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];

            // Başarılı giriş mesajı ve yönlendirme
            echo "<div style='font-family:sans-serif;text-align:center;padding:50px;background:#dcfce7;'>
                    <h1 style='color:#166534'>✅ Giriş Başarılı</h1>
                    <p>Hoş geldiniz, <strong>" . htmlspecialchars($user['username']) . "</strong></p>
                    <p>Yönlendiriliyorsunuz...</p>
                    <script>setTimeout(function(){ window.location.href = 'index.php'; }, 1000);</script>
                  </div>";
            exit;
        }
    } catch (Exception $e) {
        $error = "DB Hatası: " . $e->getMessage();
    }
}

// 3. Normal Giriş İşlemi (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Basit login kontrolü (CSRF yok)
    if (login($username, $password)) {
        header("Location: index.php");
        exit;
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
    <title>Admin Giriş - Erguvan Psikoloji</title>
    <style>
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background-color: #fce7f3;
            /* pink-100 */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-card {
            background: white;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(236, 72, 153, 0.15);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .logo {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        h2 {
            color: #831843;
            margin-bottom: 0.5rem;
        }

        p {
            color: #6b7280;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            box-sizing: border-box;
            font-size: 1rem;
        }

        input:focus {
            border-color: #ec4899;
            outline: none;
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background-color: #ec4899;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        button:hover {
            background-color: #db2777;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .help-link {
            display: block;
            margin-top: 1.5rem;
            color: #ec4899;
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="logo">🌸</div>
        <h2>Erguvan Psikoloji</h2>
        <p>Yönetim Paneli Girişi</p>

        <?php if ($error): ?>
            <div class="error">
                <strong>Hata:</strong> <?php echo htmlspecialchars($error); ?>
                <br><br>
                <a href="?force_login=yes" style="color:#b91c1c;text-decoration:underline;">Şifremi Unuttum / Acil Giriş</a>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Kullanıcı Adı</label>
                <input type="text" name="username" required autofocus>
            </div>

            <div class="form-group">
                <label>Şifre</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit">Giriş Yap</button>
        </form>

        <a href="?force_login=yes" class="help-link">Erişim Sorunu mu Yaşıyorsunuz?</a>
    </div>
</body>

</html>