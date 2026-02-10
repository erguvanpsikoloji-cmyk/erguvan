<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$source = __DIR__ . '/temp_index.php';
$dest = __DIR__ . '/../../public_html/index.php';

echo "<h2>Index Restore Script</h2>";
echo "Source: " . $source . "<br>";
echo "Dest: " . $dest . "<br>";

if (!file_exists($source)) {
    die("❌ Source fil (admin/temp_index.php) NOT FOUND!");
}

if (copy($source, $dest)) {
    echo "<h1>✅ SUCCESS! Index.php restored.</h1>";
} else {
    echo "<h1>❌ FAILED!</h1>";
    $error = error_get_last();
    echo "Error: " . ($error['message'] ?? 'Unknown error');
}
?>