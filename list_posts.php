<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/db.php';

try {
    $db = getDB();
    $stmt = $db->query("SELECT id, title, slug, image FROM blog_posts");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($posts, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>