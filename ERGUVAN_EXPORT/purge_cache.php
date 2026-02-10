<?php
/**
 * LiteSpeed Cache Purge Script
 * Bu dosya LiteSpeed sunucusundaki tüm önbelleği temizler.
 */

// Tüm önbelleği temizleme sinyali gönder
header("X-LiteSpeed-Purge: *");

echo "<h1>LiteSpeed Cache Purged!</h1>";
echo "<p>Sitenizin tüm önbelleği başarıyla temizlendi. Şimdi PageSpeed testini tekrar yapabilirsiniz.</p>";
echo "<br><a href='/'>Ana Sayfaya Git</a>";
?>