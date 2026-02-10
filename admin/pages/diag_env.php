<?php
// diag_env.php - Diagnostics Tool
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🛠️ Erguvan Psikoloji - Diagnostics</h1>";

// 1. PHP Info Minimal
echo "<h3>1. Environment</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
echo "Post Max Size: " . ini_get('post_max_size') . "<br>";
echo "Upload Max Filesize: " . ini_get('upload_max_filesize') . "<br>";

// 2. File Permissions
echo "<h3>2. File Permissions</h3>";
$dir = __DIR__;
echo "Current Directory: $dir<br>";
echo "Is Writable: " . (is_writable($dir) ? "<span style='color:green'>YES</span>" : "<span style='color:red'>NO</span>") . "<br>";

$traceFile = $dir . '/blog_debug_trace.txt';
if (file_exists($traceFile)) {
    echo "Trace File Exists. Size: " . filesize($traceFile) . " bytes.<br>";
    echo "Is Writable: " . (is_writable($traceFile) ? "<span style='color:green'>YES</span>" : "<span style='color:red'>NO</span>") . "<br>";
    echo "Last 5 lines:<pre>" . htmlspecialchars(file_get_contents($traceFile, false, null, max(0, filesize($traceFile) - 500))) . "</pre>";
} else {
    echo "Trace File Does NOT Exist. Attempting to create...<br>";
    try {
        if (file_put_contents($traceFile, "Test Log " . date('Y-m-d H:i:s') . "\n")) {
            echo "<span style='color:green'>Successfully created trace file.</span><br>";
        } else {
            echo "<span style='color:red'>Failed to create trace file.</span><br>";
        }
    } catch (Throwable $e) {
        echo "<span style='color:red'>Exception creating file: " . $e->getMessage() . "</span><br>";
    }
}

// 3. Session Check
echo "<h3>3. Session Check</h3>";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    echo "Session was inactive, started now.<br>";
} else {
    echo "Session already active.<br>";
}

echo "Session ID: " . session_id() . "<br>";
if (isset($_SESSION['user_id'])) {
    echo "User ID: " . $_SESSION['user_id'] . "<br>";
    echo "Logged In: <span style='color:green'>YES</span><br>";
} else {
    echo "Logged In: <span style='color:orange'>NO</span><br>";
}

// 4. GD Library Check
echo "<h3>4. Extensions</h3>";
echo "GD Library: " . (extension_loaded('gd') ? "<span style='color:green'>Loaded</span>" : "<span style='color:red'>MISSING</span>") . "<br>";
echo "PDO: " . (extension_loaded('pdo') ? "<span style='color:green'>Loaded</span>" : "<span style='color:red'>MISSING</span>") . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? "<span style='color:green'>Loaded</span>" : "<span style='color:red'>MISSING</span>") . "<br>";

echo "<hr><p>Diagnostics Complete.</p>";
?>