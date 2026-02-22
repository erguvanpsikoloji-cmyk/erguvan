<?php
require_once 'config.php';
require_once 'database/db.php';

try {
    $db = getDB();

    // Adres güncelleme
    $stmt = $db->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = 'address'");
    $stmt->execute(['Şehremini, Millet Cd. 34098 Fatih/İstanbul']);

    // Telefon güncelleme (boşluksuz format)
    $stmt = $db->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = 'phone'");
    $stmt->execute(['05511765285']);

    echo "Veritabanı başarıyla güncellendi.";
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>