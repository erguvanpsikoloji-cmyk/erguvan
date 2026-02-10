<?php
session_start();
header('Content-Type: text/plain; charset=utf-8');

echo "--- Session Diagnostic ---\n";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? "ACTIVE" : "INACTIVE") . "\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Save Path: " . session_save_path() . "\n";
echo "Is Writable: " . (is_writable(session_save_path() ?: sys_get_temp_dir()) ? "YES" : "NO") . "\n";

if (!isset($_SESSION['diag_count'])) {
    $_SESSION['diag_count'] = 1;
    echo "First visit. Setting diag_count to 1.\n";
} else {
    $_SESSION['diag_count']++;
    echo "Visit count: " . $_SESSION['diag_count'] . "\n";
}

echo "\n--- Cookie Params ---\n";
print_r(session_get_cookie_params());

echo "\n--- Server Info ---\n";
echo "HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "\n";
echo "HTTPS: " . (isset($_SERVER['HTTPS']) ? "ON" : "OFF") . "\n";

echo "\n--- Diagnosis ---\n";
if ($_SESSION['diag_count'] > 1) {
    echo "SUCCESS: Session is persisting between refreshes.\n";
} else {
    echo "NOTE: Refresh this page once. If this message stays as 'First visit', sessions are broken.\n";
}
?>