<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/db.php';
header('Content-Type: text/plain; charset=utf-8');
try {
    $db = getDB();
    echo "Veritabanı güncelleniyor (Sena Ceren Site)...\n\n";
    $columns = [
        'meta_description' => "TEXT AFTER excerpt",
        'toc_data' => "TEXT AFTER meta_description",
        'faq_data' => "TEXT AFTER toc_data",
        'image_alt' => "VARCHAR(255) AFTER image",
        'instagram_share' => "TINYINT(1) DEFAULT 0 AFTER keywords"
    ];
    foreach ($columns as $column => $definition) {
        $stmt = $db->query("SHOW COLUMNS FROM blog_posts LIKE '$column'");
        if (!$stmt->fetch()) {
            $db->exec("ALTER TABLE blog_posts ADD COLUMN $column $definition");
            echo "✓ '$column' sütunu eklendi.\n";
        } else {
            echo "i '$column' sütunu zaten mevcut.\n";
        }
    }
    echo "\nİşlem başarıyla tamamlandı.";
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage();
}
?>