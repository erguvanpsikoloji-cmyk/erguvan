<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once 'database/db.php';

echo "<h1>🔄 Veri Transferi: SQLite -> MySQL</h1>";

$sqliteFiles = [
    __DIR__ . '/erguvan.db',
    __DIR__ . '/database/erguvan.db'
];

$sqliteFile = null;
foreach ($sqliteFiles as $file) {
    if (file_exists($file)) {
        $sqliteFile = $file;
        break;
    }
}

if (!$sqliteFile) {
    die("❌ erguvan.db dosyası bulunamadı! Lütfen ana dizine veya database klasörüne yükleyin.");
}

try {
    // 1. SQLite Bağlantısı (Kaynak)
    $sqlite = new PDO("sqlite:" . $sqliteFile);
    $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ SQLite bağlantısı başarılı (Kaynak).</p>";

    // 2. MySQL Bağlantısı (Hedef) - Doğrudan Bağlantı Denemesi
    echo "<p>🔄 MySQL Bağlantısı Deneniyor (Veriler: uzma8531_ceren)...</p>";

    $host = 'localhost';
    $dbname = 'uzma8531_ceren';
    $username = 'uzma8531_ceren';
    $password = 'Mihrimah0112';

    try {
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
        $mysql = new PDO($dsn, $username, $password);
        $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p>✅ MySQL bağlantısı başarılı (Hedef).</p>";
    } catch (PDOException $e) {
        throw new Exception("MySQL Bağlantı Hatası: " . $e->getMessage());
    }

    // --- BLOG TRANSFERİ ---
    echo "<h2>📝 Blog Yazıları Aktarılıyor...</h2>";

    // SQLite'tan blogları çek
    try {
        $stmt = $sqlite->query("SELECT * FROM blog_posts");
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($posts)) {
            echo "<p>⚠️ SQLite veritabanında hiç blog yazısı bulunamadı.</p>";
        } else {
            echo "<p>📦 " . count($posts) . " adet blog yazısı bulundu.</p>";

            // MySQL'e ekle
            $insertStmt = $mysql->prepare("INSERT IGNORE INTO blog_posts 
                (title, slug, excerpt, content, image, category, reading_time, keywords, created_at) 
                VALUES (:title, :slug, :excerpt, :content, :image, :category, :reading_time, :keywords, :created_at)");

            $count = 0;
            foreach ($posts as $post) {
                // Eksik alanları tamamla
                $reading_time = $post['reading_time'] ?? '5 dk';
                $keywords = $post['keywords'] ?? '';

                $result = $insertStmt->execute([
                    ':title' => $post['title'],
                    ':slug' => $post['slug'],
                    ':excerpt' => $post['excerpt'],
                    ':content' => $post['content'],
                    ':image' => $post['image'],
                    ':category' => $post['category'],
                    ':reading_time' => $reading_time,
                    ':keywords' => $keywords,
                    ':created_at' => $post['created_at']
                ]);

                if ($result && $insertStmt->rowCount() > 0) {
                    echo "<div>✅ Eklendi: " . htmlspecialchars($post['title']) . "</div>";
                    $count++;
                } else {
                    echo "<div style='color:gray;'>⏭️ Atlandı (Zaten var): " . htmlspecialchars($post['title']) . "</div>";
                }
            }
            echo "<p><strong>Toplam $count yeni yazı aktarıldı.</strong></p>";
        }
    } catch (PDOException $e) {
        echo "<p>⚠️ Blog tablosu okunamadı: " . $e->getMessage() . "</p>";
    }

    echo "<h2>🎉 İşlem Tamamlandı!</h2>";
    echo "<p><a href='/admin/pages/blog.php'>Admin Paneline Git ve Kontrol Et</a></p>";

} catch (Exception $e) {
    echo "<h2 style='color:red;'>❌ HATA</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>