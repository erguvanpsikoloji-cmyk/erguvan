<?php
// Session header.php'de başlatıldı

// Config dosyasını yükle
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config.php';
}

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect(admin_url('login.php'));
    }
}

function login($username, $password) {
    require_once __DIR__ . '/../../database/db.php';
    $db = getDB();
    
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Session fixation koruması
        session_regenerate_id(true);
        
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        return true;
    }
    
    return false;
}

function logout() {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    redirect(admin_url('login.php'));
}
