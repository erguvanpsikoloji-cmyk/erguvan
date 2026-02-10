<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$zipFile = 'deploy_package.zip';
$extractPath = __DIR__;

echo "Script User: " . get_current_user() . "<br>";
echo "Script UID: " . getmyuid() . "<br>";
echo "File Exists: " . (file_exists($zipFile) ? 'YES' : 'NO') . "<br>";
if (file_exists($zipFile)) {
    echo "File Size: " . filesize($zipFile) . "<br>";
    echo "File Perms: " . substr(sprintf('%o', fileperms($zipFile)), -4) . "<br>";
}

if (file_exists($zipFile)) {
    $zip = new ZipArchive;
    $res = $zip->open($zipFile);
    if ($res === TRUE) {
        $extractRes = $zip->extractTo($extractPath);
        $zip->close();
        if ($extractRes) {
            echo "Başarıyla çıkartıldı: $zipFile";
            // Check index.php
            if (file_exists('index.php')) {
                echo "<br>Index.php mevcut.";
            }
            unlink($zipFile);
        } else {
            echo "Extract failed. Write permissions?";
            echo "Dir Perms: " . substr(sprintf('%o', fileperms($extractPath)), -4);
        }
    } else {
        echo "Zip dosyası açılamadı. Hata Kodu: " . $res;
        // Error codes:
        // 19 = ZIPER_NOZIP (Not a zip archive)
        // 5 = ZIPER_READ (Read error)
        // 21 = ZIPER_INCONS (Inconsistent archive)
    }
} else {
    echo "Zip dosyası bulunamadı: $zipFile";
}
?>