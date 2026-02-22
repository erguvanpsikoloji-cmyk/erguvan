<?php
require_once 'database/db.php';
try {
    $db = getDB();
    if ($db instanceof MockPDO) {
        echo "DATABASE_ERROR: Connection failed";
        exit;
    }
    $stmt = $db->query("SELECT slug, title FROM blog_posts ORDER BY created_at DESC");
    $posts = $stmt->fetchAll();
    foreach ($posts as $post) {
        echo "URL: https://erguvanpsikoloji.com/blog/" . $post['slug'] . " | TITLE: " . $post['title'] . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
