<?php
echo "=== database/db.php CONTENT ===\n";
echo file_get_contents(__DIR__ . '/database/db.php');
echo "\n\n=== index.php FIRST 50 LINES ===\n";
$lines = file(__DIR__ . '/index.php');
for ($i = 0; $i < 50 && $i < count($lines); $i++) {
    echo $lines[$i];
}
echo "\n\n=== site_settings content from DB ===\n";
try {
    require_once __DIR__ . '/database/db.php';
    $db = getDB();
    $stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['setting_key'] . ": " . $row['setting_value'] . "\n";
    }
} catch (Exception $e) {
    echo "DB HATA: " . $e->getMessage();
}
