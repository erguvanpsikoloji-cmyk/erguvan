<?php
// DB bağlantısı için config ve db dosyalarını dahil et
require_once 'config.php';
require_once 'database/db.php';

try {
    $db = getDB();

    $new_address = 'Şehremini, Millet Cd. 34098 Fatih/İstanbul';

    // Site ayarlarını güncelle
    // Önce var mı diye kontrol et
    $stmt = $db->prepare("SELECT * FROM site_settings WHERE setting_key = 'address'");
    $stmt->execute();
    $exists = $stmt->fetch();

    if ($exists) {
        $update = $db->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = 'address'");
        $update->execute([$new_address]);
        echo "<h1 style='color:green'>Adres Güncellendi (UPDATE): $new_address</h1>";
    } else {
        $insert = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES ('address', ?)");
        $insert->execute([$new_address]);
        echo "<h1 style='color:green'>Adres Eklendi (INSERT): $new_address</h1>";
    }

} catch (Exception $e) {
    echo "<h1 style='color:red'>Hata Oluştu: " . $e->getMessage() . "</h1>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>