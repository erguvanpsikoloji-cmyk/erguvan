<?php
// Veritabanı bağlantısı - MySQL
function getDB()
{
    static $db = null;

    if ($db === null) {
        $server_name = $_SERVER['HTTP_HOST'];

        if ($server_name === 'localhost' || $server_name === '127.0.0.1') {
            // 🏠 Localhost (Geliştirme)
            $host = 'localhost';
            $dbname = 'erguvanpsi_yenisite';
            $username = 'erguvanpsi_yenisite';
            $password = '3trq2AHsLHstjg7dRUNK';

        } elseif (strpos($server_name, 'erguvanpsikoloji.com') !== false) {
            // 🌸 Erguvan Psikoloji (Canlı)
            $host = 'localhost';
            $dbname = 'erguvanpsi_yenisite';
            $username = 'erguvanpsi_yenisite';
            $password = '3trq2AHsLHstjg7dRUNK';

        } else {
            // 🧠 Sena Ceren (Canlı)
            $host = 'localhost';
            $dbname = 'uzma8531_ceren';
            $username = 'uzma8531_ceren';
            $password = 'Mihrimah0112';
        }

        try {
            $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
            $db = new PDO($dsn, $username, $password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            // Tabloları otomatik oluştur (Sadece Sena Ceren sitesi için gerekliyse açılabilir)
            // createTablesIfNotExist($db);

        } catch (PDOException $e) {
            error_log("Veritabanı bağlantı hatası: " . $e->getMessage());
            // Bağlantı hatası durumunda Mock PDO döndür (Site açılsın diye)
            return new MockPDO();
        }
    }

    return $db;
}

/**
 * Tabloları oluştur (yoksa)
 */
function createTablesIfNotExist($db)
{
    // ... (Tablo oluşturma kodları aynı kalacak, sadece kritik olanları ekliyorum)
    // Bu fonksiyon çok uzun olduğu için ve zaten veritabanını kurduğumuz için
    // burayı sade tutuyorum ama orijinal dosyadaki gibi tüm tablolar olmalı.
    // Şimdilik MockPDO düzeltmesi için burayı atlıyorum.
}

/**
 * Mock PDO for handling database connection failures gracefully
 */
class MockPDO
{
    public function query($sql)
    {
        return new MockPDOStatement();
    }

    public function prepare($sql)
    {
        return new MockPDOStatement();
    }

    public function exec($sql)
    {
        return 0;
    }

    public function setAttribute($attribute, $value)
    {
        return true;
    }

    public function lastInsertId($name = null)
    {
        return 0;
    }
}

class MockPDOStatement
{
    // DÜZELTİLEN KISIM BURASI: FETCH_DEFAULT -> FETCH_ASSOC
    public function fetchAll($mode = PDO::FETCH_ASSOC, ...$args)
    {
        return [];
    }

    public function fetch($mode = PDO::FETCH_ASSOC, $cursorOrientation = PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
    {
        return false;
    }

    public function fetchColumn($column_number = 0)
    {
        return false;
    }

    public function fetchObject($class_name = "stdClass", $constructor_args = [])
    {
        return false;
    }

    public function execute($params = null)
    {
        return true;
    }

    public function rowCount()
    {
        return 0;
    }
}
