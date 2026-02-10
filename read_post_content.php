<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/db.php';
$db = getDB();
$stmt = $db->prepare("SELECT content FROM blog_posts WHERE slug = 'ayrilik-kaygisi-nedir' LIMIT 1");
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if ($post) {
    echo "CONTENT_START\n";
    echo $post['content'];
    echo "\nCONTENT_END";
} else {
    echo "Post not found.";
}
