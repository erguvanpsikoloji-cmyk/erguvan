<?php
// check.php - Environment Check
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🛠️ System Check</h1>";
echo "PHP: " . phpversion() . "<br>";
echo "Dir: " . __DIR__ . "<br>";
echo "Writable: " . (is_writable(__DIR__) ? 'YES' : 'NO') . "<br>";

if (session_status() == PHP_SESSION_NONE)
    session_start();
echo "Session ID: " . session_id() . "<br>";
echo "Logged In: " . (isset($_SESSION['admin_logged_in']) ? 'YES' : 'NO') . "<br>";
if (isset($_SESSION)) {
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
}
?>