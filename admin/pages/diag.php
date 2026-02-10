<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnostic Check</h1>";

$files = [
    '../../config.php',
    '../includes/auth.php',
    '../includes/csrf.php',
    '../../database/db.php',
    '../includes/upload-handler.php',
    '../includes/header.php'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    echo "Checking $file: ";
    if (file_exists($path)) {
        echo "<span style='color:green'>Found</span><br>";
        try {
            // Check if it's readable
            if (is_readable($path)) {
                echo " - Readable: <span style='color:green'>Yes</span><br>";
                // Try to check syntax without executing if possible, or just require it
                // but requiring might crash the diagnostic if there's a fatal error.
            } else {
                echo " - Readable: <span style='color:red'>No</span><br>";
            }
        } catch (Exception $e) {
            echo " - Error checking: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "<span style='color:red'>Not Found</span> (Path: $path)<br>";
    }
}

echo "<h2>Database Connection</h2>";
require_once __DIR__ . '/../../database/db.php';
try {
    $db = getDB();
    if ($db instanceof PDO) {
        echo "<span style='color:green'>PDO Connection Successful</span><br>";
        $stmt = $db->query("SELECT COUNT(*) FROM blog_posts");
        echo "Blog posts count: " . $stmt->fetchColumn() . "<br>";
    } else if (get_class($db) === 'MockPDO') {
        echo "<span style='color:orange'>Falling back to MockPDO (Connection Failed)</span><br>";
    } else {
        echo "<span style='color:red'>Unknown DB object type: " . get_class($db) . "</span><br>";
    }
} catch (Exception $e) {
    echo "<span style='color:red'>DB Error: " . $e->getMessage() . "</span><br>";
}

echo "<h2>Session Info</h2>";
session_start();
echo "Session Status: " . session_status() . "<br>";
echo "Admin Logged In: " . (isset($_SESSION['admin_logged_in']) ? 'Yes' : 'No') . "<br>";

echo "<h2>PHP Info</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Display Errors: " . ini_get('display_errors') . "<br>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
