<?php
$dbPaths = [
    'D:\\Erguvan antigravity hosting\\ERGUVAN_EXPORT\\erguvan.db',
    'D:\\Erguvan antigravity hosting\\database\\erguvan.db',
    'D:\\Erguvan antigravity hosting\\YEDEK_2026_02_02\\database\\erguvan.db',
    'D:\\ceren antigravity web site\\database\\ceren.db'
];

foreach ($dbPaths as $dbPath) {
    echo "========================================\n";
    echo "DB: $dbPath\n";
    echo "========================================\n";

    if (!file_exists($dbPath)) {
        echo "Dosya bulunamadı.\n\n";
        continue;
    }

    try {
        $db = new PDO("sqlite:" . $dbPath);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Tabloları listele
        $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
        echo "Tablolar: " . implode(", ", $tables) . "\n\n";

        if (in_array('blog_posts', $tables)) {
            echo "--- SON BLOG YAZILARI ---\n";
            $posts = $db->query("SELECT id, title, created_at FROM blog_posts ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($posts as $post) {
                echo "[ID: {$post['id']}] [Tarih: {$post['created_at']}] {$post['title']}\n";
            }
        }

    } catch (Exception $e) {
        echo "HATA: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
?>