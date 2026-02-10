<?php
/**
 * BLOG ADD ERROR LOGGER (NO EXEC)
 * Captures detailed error information without using exec()
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Blog Add Debug Mode (No Exec)</h2>";

// Check file existence
$filesToCheck = [
    __DIR__ . '/admin/pages/blog-add.php',
    __DIR__ . '/admin/includes/upload-handler.php',
    __DIR__ . '/database/db.php',
    __DIR__ . '/config.php'
];

foreach ($filesToCheck as $file) {
    if (file_exists($file)) {
        echo "<p style='color:green'>✅ Found: " . basename($file) . "</p>";
    } else {
        echo "<p style='color:red'>❌ MISSING: " . basename($file) . " (Path: $file)</p>";
    }
}

// Check database connection
echo "<h3>Database Check:</h3>";
try {
    if (file_exists(__DIR__ . '/database/db.php')) {
        require_once __DIR__ . '/database/db.php';
        $db = getDB();
        echo "<p style='color:green'>✅ Database connection OK</p>";

        // Check columns again just to be sure
        $stmt = $db->query("SHOW COLUMNS FROM blog_posts");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>Columns: " . implode(', ', $cols) . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Database error: " . $e->getMessage() . "</p>";
}

// Check PHP settings
echo "<h3>PHP Settings:</h3>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "file_uploads: " . ini_get('file_uploads') . "<br>";

?>