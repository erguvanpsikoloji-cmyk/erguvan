<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/database/db.php';
$db = getDB();
$slug = 'ayrilik-kaygisi-nedir';
$stmt = $db->prepare("SELECT title, content FROM blog_posts WHERE slug = ?");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if ($post) {
    echo "<h1>" . $post['title'] . "</h1>";
    echo "<div style='border:1px solid #ccc; padding:10px;'>" . htmlspecialchars($post['content']) . "</div>";
} else {
    echo "Yazı bulunamadı: " . $slug;
}
unlink(__FILE__);
?>