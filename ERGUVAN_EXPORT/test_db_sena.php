<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Veritabanı Bağlantı Testi</h1>";

try {
    // Doğrudan bağlantı deniyoruz (db.php kullanmadan)
    $host = 'localhost';
    $dbname = 'uzma8531_ceren';
    $username = 'uzma8531_ceren';
    $password = 'Mihrimah0112';

    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p style='color:green; font-weight:bold;'>✅ BAĞLANTI BAŞARILI!</p>";

    // Tabloları listele
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<h3>Mevcut Tablolar:</h3>";
    if (empty($tables)) {
        echo "<p>⚠️ Veritabanı boş (Tablo yok).</p>";
    } else {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    }

} catch (PDOException $e) {
    echo "<p style='color:red; font-weight:bold;'>❌ BAĞLANTI HATASI:</p>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>