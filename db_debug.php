<?php
require_once 'database/db.php';
try {
    $db = getDB();
    echo "--- SLIDERS ---\n";
    $sliders = $db->query("SELECT * FROM sliders")->fetchAll(PDO::FETCH_ASSOC);
    print_r($sliders);

    echo "\n--- SITE SETTINGS ---\n";
    $settings = $db->query("SELECT * FROM site_settings")->fetchAll(PDO::FETCH_ASSOC);
    print_r($settings);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
