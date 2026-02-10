<?php
header('Content-Type: text/plain');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$zipFile = 'erguvan_archive_deploy.zip';
$extractPath = __DIR__; // Extract to root, so ERGUVAN_ARSIV folder appears in root

echo "--- START ARCHIVE RESTORE ---\n";

if (file_exists($zipFile)) {
    echo "Archive Found. Size: " . filesize($zipFile) . " bytes\n";

    $zip = new ZipArchive;
    $res = $zip->open($zipFile);
    if ($res === TRUE) {
        if ($zip->extractTo($extractPath)) {
            echo "STATUS: SUCCESS\n";
            $zip->close();
            // unlink($zipFile);
        } else {
            echo "STATUS: FAILED_EXTRACT\n";
        }
    } else {
        echo "STATUS: FAILED_OPEN Error: " . $res . "\n";
    }
} else {
    echo "STATUS: NO_ZIP\n";
}
echo "--- END ARCHIVE RESTORE ---\n";
?>