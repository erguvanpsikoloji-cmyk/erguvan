<?php
/**
 * ERGUVAN DEEP CLEANER
 * Deletes files with backslashes and specific garbage folders/files.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Derin Temizlik Başladı</h2>";

function deleteRecursive($path)
{
    if (!file_exists($path))
        return;
    if (is_file($path)) {
        if (unlink($path)) {
            echo "Silindi: $path <br>";
        } else {
            echo "HATA: $path silinemedi.<br>";
        }
        return;
    }
    $files = array_diff(scandir($path), array('.', '..'));
    foreach ($files as $file) {
        deleteRecursive("$path/$file");
    }
    if (rmdir($path)) {
        echo "Klasör silindi: $path <br>";
    }
}

// 1. Scan and delete backslash files in Root and Public_HTML
$dirsToScan = [__DIR__, __DIR__ . '/public_html'];
foreach ($dirsToScan as $dir) {
    if (is_dir($dir)) {
        $items = scandir($dir);
        foreach ($items as $item) {
            if (strpos($item, '\\') !== false) {
                $p = "$dir/$item";
                echo "Bulundu (Backslash): $item ... ";
                if (unlink($p))
                    echo "SİLİNDİ.<br>";
                else
                    echo "HATA.<br>";
            }
        }
    }
}

// 2. Target specific garbage folders/files
$targets = [
    'dist_fcp_v6',
    'dist_final',
    'dist_hiz_90',
    'dist_toc',
    'dist_toc_fixed',
    'dist_v22',
    'dist_cls_v10',
    'dist_cls_v11',
    'dist_cls_v12',
    'dist_cls_v13',
    'dist_cls_v7',
    'dist_cls_v8',
    'dist_cls_v9',
    'ERGUVAN PSİKOTERAPİ MERKEZİ',
    'erguvan psikoloji web',
    'ERGUVAN_ARSIV',
    'YEDEK_2026_02_02',
    'public_html/erguvan psikoloji web',
    'public_html/temp_sena_check',
    'public_html/temp_sena_kurtarma'
];

foreach ($targets as $t) {
    $p = __DIR__ . '/' . $t;
    if (file_exists($p)) {
        echo "Hedef siliniyor: $t ... <br>";
        deleteRecursive($p);
    }
}

echo "<h3>Temizlik Tamamlandı. Lütfen bu dosyayı silin.</h3>";
