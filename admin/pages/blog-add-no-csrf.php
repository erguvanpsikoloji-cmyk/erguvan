<?php
// DEBUG: Script started
ini_set('memory_limit', '256M');
// FATAL ERROR CATCHER
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
        echo "<div style='background:red;color:white;padding:20px;z-index:99999;position:fixed;top:0;left:0;width:100%;'>";
        echo "<h3>🛑 FATAL ERROR DETECTED</h3>";
        echo "<p><strong>Message:</strong> " . $error['message'] . "</p>";
        echo "<p><strong>File:</strong> " . $error['file'] . " (" . $error['line'] . ")</p>";
        echo "</div>";
    }
});

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
// require_once __DIR__ . '/../includes/csrf.php'; // DISABLED FOR DEBUG
require_once __DIR__ . '/../../database/db.php';
require_once __DIR__ . '/../includes/upload-handler.php';

// Dummy CSRF functions to prevent errors
if (!function_exists('csrfField')) {
    function csrfField() { return '<input type="hidden" name="csrf_token" value="debug_bypass">'; }
}

$db = getDB();
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents(__DIR__ . '/blog_debug_trace.txt', "POST Started at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

    // 1. GÖRSEL BOYUTU KONTROLÜ (KRİTİK)
    if (empty($_POST) && empty($_FILES)) {
        file_put_contents(__DIR__ . '/blog_debug_trace.txt', "❌ POST data empty (size limit exceeded)\n", FILE_APPEND);
        $error = '<strong>Hata:</strong> Yüklenen dosya çok büyük! Lütfen görsel boyutunu küçültün (Örn: Max 2MB).';
    } else {
        try {
            file_put_contents(__DIR__ . '/blog_debug_trace.txt', "CSRF Skipped for Debug...\n", FILE_APPEND);
            // CSRF CHECK DISABLED
            
            $title = trim($_POST['title'] ?? '');
