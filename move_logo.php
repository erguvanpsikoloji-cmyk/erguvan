<?php
$source = 'assets/images/logo_data.tmp';
$dest = 'assets/images/logo_erguvan_2026.png';

if (file_exists($source)) {
    if (rename($source, $dest)) {
        echo "BASARILI: $source -> $dest olarak tasindi.";
    } else {
        echo "HATA: Dosya tasinamadi. Izinleri kontrol edin.";
    }
} else {
    echo "HATA: Kaynak dosya bulunamadi ($source).";
}
?>