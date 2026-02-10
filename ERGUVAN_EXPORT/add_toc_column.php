<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/db.php';

try {
    $db = getDB();
    // Check if column exists
    $stmt = $db->query("SHOW COLUMNS FROM blog_posts LIKE 'toc_data'");
    $exists = $stmt->fetch();

    if (!$exists) {
        $db->exec("ALTER TABLE blog_posts ADD COLUMN toc_data TEXT AFTER excerpt");
        echo "<h1>BAŞARILI</h1><p>'toc_data' sütunu blog_posts tablosuna eklendi.</p>";
    } else {
        echo "<h1>BİLGİ</h1><p>'toc_data' sütunu zaten mevcut.</p>";
    }
} catch (Exception $e) {
    echo "<h1>HATA</h1><p>" . $e->getMessage() . "</p>";
}
unlink(__FILE__);
?>