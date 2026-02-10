<?php
require_once __DIR__ . '/database/db.php';
$db = getDB();
$stmt = $db->query("DESCRIBE blog_posts");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($columns);
unlink(__FILE__);
?>