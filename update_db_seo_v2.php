<?php
require_once 'config.php';
require_once 'database/db.php';

try {
    $db = getDB();
    echo "<h1>Veritabanı SEO Güncellemesi</h1>";

    $columns = [
        'canonical_url' => "VARCHAR(255) DEFAULT NULL",
        'og_title' => "VARCHAR(255) DEFAULT NULL",
        'og_description' => "VARCHAR(255) DEFAULT NULL",
        'schema_type' => "VARCHAR(50) DEFAULT 'BlogPosting'"
    ];

    foreach ($columns as $col => $def) {
        try {
            // Kolon var mı kontrol et (Basitçe eklemeyi dene, hata verirse vardır)
            // MySQL'de IF NOT EXISTS yok ADD COLUMN için (MariaDB 10.2+ hariç)
            // Bu yüzden önce check edelim
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