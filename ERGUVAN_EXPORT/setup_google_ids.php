<?php
/**
 * Google ID Kurulum Betiği
 * Bu betik, Google Tag Manager ve Google Ads ID'lerini veritabanına otomatik olarak işler.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/db.php';

try {
    $db = getDB();

    $settings = [
        'google_tag_manager' => 'GTM-523L2BT',
        'google_ads_id' => 'AW-10987534428',
        'google_analytics_id' => 'G-XXXXXXXX' // Henüz net değil, gerekirse manuel girilebilir
    ];

    $stmt = $db->prepare("UPDATE google_settings SET setting_value = :value WHERE setting_key = :key");

    foreach ($settings as $key => $value) {
        if (!empty($value) && $value !== 'G-XXXXXXXX') {
            $stmt->execute([
                ':key' => $key,
                ':value' => $value
            ]);
            echo "Güncellendi: $key => $value <br>";
        }
    }

    echo "<strong>İşlem tamamlandı!</strong> Lütfen bu dosyayı sunucunuzdan silmeyi unutmayın.";

} catch (Exception $e) {
    echo "Hata oluştu: " . $e->getMessage();
}
