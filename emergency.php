<?php
/**
 * EMERGENCY SITE RECOVERY
 * Checks and fixes critical issues
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🚨 Emergency Site Recovery</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .ok{color:green;} .error{color:red;}</style>";

// 1. Check if index.php has syntax errors
echo "<h2>1. Checking index.php</h2>";
$indexPath = __DIR__ . '/index.php';
if (file_exists($indexPath)) {
    $output = [];
    $return = 0;
    exec("php -l " . escapeshellarg($indexPath) . " 2>&1", $output, $return);

    if ($return === 0) {
        echo "<p class='ok'>✅ index.php syntax is OK</p>";
    } else {
        echo "<p class='error'>❌ SYNTAX ERROR in index.php:</p>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    }

    // Check file size
    $size = filesize($indexPath);
    echo "<p>File size: " . round($size / 1024, 2) . " KB</p>";
} else {
    echo "<p class='error'>❌ index.php NOT FOUND!</p>";
}

// 2. Check .htaccess
echo "<h2>2. Checking .htaccess</h2>";
$htaccessPath = __DIR__ . '/.htaccess';
if (file_exists($htaccessPath)) {
    echo "<p class='ok'>✅ .htaccess exists</p>";
    $htaccess = file_get_contents($htaccessPath);
    echo "<pre>" . htmlspecialchars($htaccess) . "</pre>";
} else {
    echo "<p class='error'>⚠️ .htaccess not found</p>";
}

// 3. PHP Info
echo "<h2>3. PHP Environment</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Memory Limit: " . ini_get('memory_limit') . "</p>";
echo "<p>Display Errors: " . ini_get('display_errors') . "</p>";

echo "<hr><p><strong>Recovery Complete. Check errors above.</strong></p>";
?>