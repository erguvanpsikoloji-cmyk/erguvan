<?php
echo "--- ENVIRONMENT INFO ---\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'NOT SET') . "\n";
echo "SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'NOT SET') . "\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";

echo "\n--- DB CONNECTION LOGIC SIMULATION ---\n";
$server_name = $_SERVER['HTTP_HOST'];
if (strpos($server_name, 'erguvanpsikoloji.com') !== false) {
    echo "LOGIC: Matches erguvanpsikoloji.com -> DB: erguvanpsi_yenisite\n";
} else {
    echo "LOGIC: Does NOT match -> DB: uzma8531_ceren (SENA CEREN!)\n";
}

echo "\n--- ACTUAL DATABASE CONTENT ---\n";
try {
    require_once __DIR__ . '/database/db.php';
    $db = getDB();

    // Hangi veritabanına bağlıyız?
    $dbname = $db->query("SELECT DATABASE()")->fetchColumn();
    echo "ACTUAL CONNECTED DB: $dbname\n";

    $title = $db->query("SELECT setting_value FROM site_settings WHERE setting_key = 'title' LIMIT 1")->fetchColumn();
    echo "SITE TITLE FROM DB: $title\n";

    $seo_title = $db->query("SELECT meta_title FROM seo_settings WHERE page_type = 'home' LIMIT 1")->fetchColumn();
    echo "SEO META TITLE FROM DB: $seo_title\n";

    echo "\n--- ALL SLIDERS ---\n";
    $sliders = $db->query("SELECT title, subtitle, description FROM sliders")->fetchAll();
    foreach ($sliders as $s) {
        echo "- " . $s['title'] . " | " . $s['subtitle'] . " | " . $s['description'] . "\n";
    }

    echo "\n--- ALL SERVICES ---\n";
    $services = $db->query("SELECT title FROM services")->fetchAll();
    foreach ($services as $s) {
        echo "- " . $s['title'] . "\n";
    }

} catch (Exception $e) {
    echo "DB ERROR: " . $e->getMessage() . "\n";
}
