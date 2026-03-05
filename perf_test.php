<?php
$start = microtime(true);

// 0. Session Test
$sess_start = microtime(true);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$sess_end = microtime(true);
$sess_time = ($sess_end - $sess_start) * 1000;

// 1. DB Connection Test
$db_start = microtime(true);
require_once 'config.php';
require_once 'database/db.php';
$db = getDB();
$db_end = microtime(true);
$db_time = ($db_end - $db_start) * 1000;

// 2. Query Test
$q_start = microtime(true);
$latest_posts = [];
try {
    if ($db) {
        $stmt = $db->query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 3");
        $latest_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
}
$q_end = microtime(true);
$q_time = ($q_end - $q_start) * 1000;

// 3. Asset function performance (20 iterations)
$asset_start = microtime(true);
for ($i = 0; $i < 20; $i++) {
    webp_url('assets/images/hero-psikolojik-destek-opt.jpg');
}
$asset_end = microtime(true);
$asset_time = ($asset_end - $asset_start) * 1000;

$total_time = (microtime(true) - $start) * 1000;

header('Content-Type: application/json');
echo json_encode([
    'total_php_ms' => round($total_time, 2),
    'session_ms' => round($sess_time, 2),
    'db_connect_ms' => round($db_time, 2),
    'query_ms' => round($q_time, 2),
    'asset_logic_20x_ms' => round($asset_time, 2),
    'php_version' => phpversion(),
    'memory_usage_mb' => round(memory_get_usage() / 1024 / 1024, 2)
], JSON_PRETTY_PRINT);
?>