<?php
header('Content-Type: text/plain');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$zipFile = 'deploy_package.zip';
$extractPath = __DIR__;

echo "--- START DEBUG ---\n";
echo "User: " . get_current_user() . "\n";

if (file_exists($zipFile)) {
    echo "Zip Found. Size: " . filesize($zipFile) . " bytes\n";

    $zip = new ZipArchive;
    $res = $zip->open($zipFile);
    if ($res === TRUE) {
        if ($zip->extractTo($extractPath)) {
            echo "STATUS: SUCCESS\n";
            $zip->close();
            // Verify
            if (file_exists('index.php')) {
                echo "VERIFY: Index.php exists\n";
            }
            // unlink($zipFile); // Keep it for now in case we need to debug
        } else {
            echo "STATUS: FAILED_EXTRACT\n";
        }
    } else {
        echo "STATUS: FAILED_OPEN Error: " . $res . "\n";
    }
} else {
    echo "STATUS: NO_ZIP\n";
}
echo "--- END DEBUG ---\n";
?>