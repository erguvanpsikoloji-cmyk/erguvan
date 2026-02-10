<?php
try {
    $dbPath = 'D:\\Erguvan antigravity hosting\\ERGUVAN_EXPORT\\erguvan.db';
    if (!file_exists($dbPath)) {
        die("Veritabanı bulunamadı: " . $dbPath);
    }

    $db = new PDO("sqlite:" . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "--- BLOG YAZILARI ---\n";
    $posts = $db->query("SELECT id, title, created_at FROM blog_posts ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($posts as $post) {
        echo "[ID: {$post['id']}] [Tarih: {$post['created_at']}] {$post['title']}\n";
    }

    echo "\n--- HİZMETLER ---\n";
    $services = $db->query("SELECT id, title FROM services")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($services as $s) {
        echo "[ID: {$s['id']}] {$s['title']}\n";
    }

} catch (Exception $e) {
    echo "HATA: " . $e->getMessage();
}
?>