<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "--- DB CONFIG TEST ---\n";
$db_content = file_get_contents(__DIR__ . '/database/db.php');
if (strpos($db_content, 'erguvanpsi_yenisite') !== false) {
    echo "DB.PHP: contains erguvanpsi_yenisite (OK)\n";
} else {
    echo "DB.PHP: DOES NOT CONTAIN erguvanpsi_yenisite (ERROR!)\n";
}

echo "\n--- INDEX.PHP TITLE TEST ---\n";
$index_content = file_get_contents(__DIR__ . '/index.php');
if (strpos($index_content, 'Erguvan Psikoloji') !== false) {
    echo "INDEX.PHP: contains Erguvan Psikoloji (OK)\n";
} else {
    echo "INDEX.PHP: DOES NOT CONTAIN Erguvan Psikoloji (ERROR!)\n";
}

echo "\n--- LIVE DB DATA TEST ---\n";
try {
    $server_name = $_SERVER['SERVER_NAME'];
    echo "SERVER_NAME: $server_name\n";

    require_once __DIR__ . '/database/db.php';
    $db = getDB();
    $stmt = $db->query("SELECT setting_value FROM site_settings WHERE setting_key = 'email'");
    $email = $stmt->fetchColumn();
    echo "DB EMAIL SETTING: $email\n";
} catch (Exception $e) {
    echo "DB ERROR: " . $e->getMessage() . "\n";
}
