<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>✅ Syntax Check OK</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test includes individually to find the breaker
$files = [
    '../../config.php',
    '../includes/auth.php',
    '../includes/csrf.php',
    '../../database/db.php',
    '../includes/upload-handler.php'
];

echo "<ul>";
foreach ($files as $f) {
    if (file_exists(__DIR__ . '/' . $f)) {
        echo "<li>Found: $f</li>";
        try {
            // Sadece sözdizimi kontrolü için include etmeyi dene (dikkat: side effect olabilir)
            // Ama fatal error varsa burada patlar
            // require_once __DIR__ . '/' . $f; 
            // echo " - Included OK"; 
        } catch (Throwable $e) {
            echo " - Error: " . $e->getMessage();
        }
    } else {
        echo "<li style='color:red'>MISSING: $f</li>";
    }
}
echo "</ul>";
?>