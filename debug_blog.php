<?php
/**
 * BLOG ADD ERROR LOGGER
 * Captures detailed error information when blog post submission fails
 */

// Enable ALL error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../blog_add_errors.log');

// Start output buffering to catch any output
ob_start();

echo "<h2>Blog Add Debug Mode</h2>";
echo "<p>Checking blog-add.php for errors...</p>";

// Try to include and execute the actual blog-add.php
$blogAddPath = __DIR__ . '/admin/pages/blog-add.php';
if (!file_exists($blogAddPath)) {
    echo "<p style='color:red'>❌ blog-add.php not found at: $blogAddPath</p>";
} else {
    echo "<p style='color:green'>✅ blog-add.php found</p>";

    // Check syntax
    $output = [];
    exec("php -l " . escapeshellarg($blogAddPath) . " 2>&1", $output, $return);

    if ($return === 0) {
        echo "<p style='color:green'>✅ Syntax check passed</p>";
    } else {
        echo "<p style='color:red'>❌ SYNTAX ERROR:</p>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    }
}

// Check database connection
echo "<h3>Database Check:</h3>";
try {
    require_once __DIR__ . '/database/db.php';
    $db = getDB();
    echo "<p style='color:green'>✅ Database connection OK</p>";

    // Check if blog_posts table exists
    $tables = $db->query("SHOW TABLES LIKE 'blog_posts'")->fetchAll();
    if (count($tables) > 0) {
        echo "<p style='color:green'>✅ blog_posts table exists</p>";
    } else {
        echo "<p style='color:red'>❌ blog_posts table NOT FOUND!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Database error: " . $e->getMessage() . "</p>";
}

// Display any captured errors
$output = ob_get_clean();
echo $output;

// Check error log
$logPath = __DIR__ . '/blog_add_errors.log';
if (file_exists($logPath)) {
    echo "<h3>Recent Errors:</h3>";
    echo "<pre>" . htmlspecialchars(file_get_contents($logPath)) . "</pre>";
}
?>