<?php
require_once 'config.php';
require_once 'database/db.php';

try {
    $db = getDB();
    echo "<h1>SEO Başlık Güncellemesi</h1>";

    $columns = [
        'meta_title' => "VARCHAR(255) DEFAULT NULL",
        'tags' => "TEXT DEFAULT NULL"
    ];

    foreach ($columns as $col => $def) {
        try {
            $check = $db->query("SHOW COLUMNS FROM blog_posts LIKE '$col'");
            if ($check->rowCount() == 0) {
                $db->exec("ALTER TABLE blog_posts ADD COLUMN $col $def");
                echo "<p style='color:green'>✅ Eklendi: $col</p>";
            } else {
                echo "<p style='color:orange'>ℹ️ Zaten var: $col</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red'>❌ Hata ($col): " . $e->getMessage() . "</p>";
        }
    }

    echo "<p>İşlem tamamlandı.</p>";

} catch (Exception $e) {
    echo "Genel Hata: " . $e->getMessage();
}
?>