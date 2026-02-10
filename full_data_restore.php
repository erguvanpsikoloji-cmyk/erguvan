<?php
/**
 * ERGUVAN PSİKOLOJİ - TAM VERİ KURTARMA SCRİPTİ
 * Bu script SQLite yedeğindeki tüm verileri MySQL canlı veritabanına taşır.
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>🚀 Erguvan Psikoloji: Tam Veri Geri Yükleme</h1>";

$sqliteFile = __DIR__ . '/database/erguvan.db';
if (!file_exists($sqliteFile)) {
    $sqliteFile = __DIR__ . '/erguvan.db';
}

if (!file_exists($sqliteFile)) {
    die("<h2 style='color:red;'>❌ HATA: erguvan.db bulunamadı!</h2>");
}

try {
    // 1. Kaynak Bağlantısı (SQLite)
    $sqlite = new PDO("sqlite:" . $sqliteFile);
    $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ SQLite (Kaynak) bağlantısı hazır.<br>";

    // 2. Hedef Bağlantısı (MySQL - Erguvan Psikoloji)
    $host = 'localhost';
    $dbname = 'erguvanpsi_yenisite';
    $username = 'erguvanpsi_yenisite';
    $password = '3trq2AHsLHstjg7dRUNK';

    $mysql = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ MySQL (Hedef) bağlantısı hazır.<br><hr>";

    // 3. Tabloları Dinamik Olarak Al
    $tableStmt = $sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
    $tables = $tableStmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        echo "🔄 <strong>$table</strong> tablosu aktarılıyor... ";

        try {
            // Hedef tabloda böyle bir tablo var mı kontrol et (yoksa oluşturmayız, hata verir)
            // Ama genellikle aynı yapıdadırlar.

            // SQLite'dan verileri çek
            $stmt = $sqlite->query("SELECT * FROM $table");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($rows)) {
                echo "<span style='color:orange;'>⚠️ Boş</span><br>";
                continue;
            }

            // MySQL'deki mevcut verileri temizle
            $mysql->exec("DELETE FROM $table");

            // Sütun isimlerini al
            $columns = array_keys($rows[0]);
            $colNames = implode(', ', $columns);
            $placeholders = implode(', ', array_map(function ($c) {
                return ":$c";
            }, $columns));

            $insertSql = "INSERT INTO $table ($colNames) VALUES ($placeholders)";
            $insertStmt = $mysql->prepare($insertSql);

            $count = 0;
            foreach ($rows as $row) {
                if ($insertStmt->execute($row)) {
                    $count++;
                }
            }
            echo "✅ <span style='color:green;'>Başarılı! ($count satır)</span><br>";

        } catch (PDOException $e) {
            echo "❌ <span style='color:red;'>HATA: " . $e->getMessage() . "</span><br>";
        }
    }

    echo "<hr><h2>🎉 Tüm tablolar başarıyla geri yüklendi!</h2>";
    echo "<p><a href='/'>Ana Sayfaya Git</a></p>";

} catch (Exception $e) {
    echo "<h2 style='color:red;'>❌ KRİTİK HATA</h2><p>" . $e->getMessage() . "</p>";
}
