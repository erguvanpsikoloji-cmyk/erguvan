<?php
try {
    $host = 'localhost';
    $dbname = 'erguvanpsi_yenisite';
    $username = 'erguvanpsi_yenisite';
    $password = '3trq2AHsLHstjg7dRUNK';

    $mysql = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    echo "=== SITE SETTINGS ===\n";
    $stmt = $mysql->query("SELECT * FROM site_settings");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['setting_key'] . ": " . $row['setting_value'] . "\n";
    }

    echo "\n=== SEO SETTINGS (home) ===\n";
    $stmt = $mysql->query("SELECT * FROM seo_settings WHERE page_type = 'home'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        print_r($row);
    } else {
        echo "Home SEO settings not found.\n";
    }

} catch (Exception $e) {
    echo "HATA: " . $e->getMessage();
}
