<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Config ve DB Dosyalarını Yükle
// manuel_toc.php ana dizinde olduğu için paths buna göre ayarlanmalı:

$config_path = __DIR__ . '/config.php';
$db_path = __DIR__ . '/database/db.php';

if (file_exists($config_path)) {
    require_once $config_path;
} else {
    echo "Uyarı: Config dosyası bulunamadı.<br>";
}

if (!file_exists($db_path)) {
    // Alternatif yol denemesi (Eğer dosya bir alt klasörde çalıştırılıyorsa)
    $db_path = __DIR__ . '/../database/db.php';
    if (!file_exists($db_path)) {
        die("HATA: Veritabanı dosyası ($db_path) bulunamadı.");
    }
}

require_once $db_path;

// 2. Veritabanı Bağlantısını Al
if (!function_exists('getDB')) {
    die("HATA: getDB fonksiyonu bulunamadı. db.php doğru yüklenmedi.");
}

$db = getDB();

if (!$db) {
    die("HATA: Veritabanına bağlanılamadı (\$db null döndü).");
}

echo "<h1>İçindekiler Tablosu - Manuel Güncelleme (V2)</h1>";

// Hedefleri tanımla
$target_slug = 'ayrilik-kaygisi-nedir';
$toc_html = '
<div class="toc-container">
    <div class="toc-title">İçindekiler</div>
    <ul class="toc-list">
        <li><a href="#belirtiler">Ayrılma Kaygısı Bozukluğu Belirtileri</a></li>
        <li><a href="#nedenleri">Ayrılık Kaygısı Bozukluğu Neden Olur?</a></li>
        <li><a href="#tani-kriterleri">Ayrılık Anksiyetesi Bozukluğu Tanı Kriterleri</a></li>
        <li><a href="#tedavisi">Ayrılma Kaygısı Bozukluğu Tedavisi</a></li>
    </ul>
</div>';

try {
    // 3. Yazıyı Bul
    $stmt = $db->prepare("SELECT id, title, content FROM blog_posts WHERE title LIKE ? OR slug = ? LIMIT 1");
    // $stmt null ise hata verir, ama getDB() PDO veya MockPDO döndüğü için method hatası vermez, 
    // ancak bağlantı success değilse MockPDO döner ve içi boştur.

    $stmt->execute(['%Ayrılık Kaygısı Nedir%', $target_slug]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        die("Yazı bulunamadı.");
    }

    echo "Yazı bulundu: " . htmlspecialchars($post['title']) . " (ID: " . $post['id'] . ")<br>";
    $content = $post['content'];

    // 4. Eğer zaten ekliyse dur
    if (strpos($content, 'class="toc-container"') !== false) {
        die("<span style='color:orange'>İçindekiler tablosu zaten ekli. İşlem yapılmadı.</span>");
    }

    // 5. Başlıkları Bul ve ID Ekle
    $replacements = [
        'Ayrılma Kaygısı Bozukluğu Belirtileri' => '<h2 id="belirtiler">Ayrılma Kaygısı Bozukluğu Belirtileri</h2>',
        'Ayrılık Kaygısı Bozukluğu Neden Olur?' => '<h2 id="nedenleri">Ayrılık Kaygısı Bozukluğu Neden Olur?</h2>',
        'Ayrılık Anksiyetesi Bozukluğu Tanı Kriterleri' => '<h2 id="tani-kriterleri">Ayrılık Anksiyetesi Bozukluğu Tanı Kriterleri</h2>',
        'Ayrılma Kaygısı Bozukluğu Tedavisi' => '<h2 id="tedavisi">Ayrılma Kaygısı Bozukluğu Tedavisi</h2>'
    ];

    $modified = false;
    foreach ($replacements as $search => $replace) {
        // h2-h6, strong veya p içinde tam eşleşme arıyoruz
        // Regex yerine str_replace daha güvenli olabilir ama html taglerini bozmamak lazım.
        // Basitçe metni bulup değiştirelim.

        // Önce h tagleri içindekini deneyelim
        $pattern = '/<(h[2-6]|strong|p)[^>]*>(\s*' . preg_quote($search, '/') . '\s*)<\/\1>/ui';

        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $replace, $content, 1);
            $modified = true;
        }
        // Eğer tag içinde bulamazsa, belki düz metindir ama replace yapmak riskli olabilir (örneğin TOC içindeki metni de değiştirir).
        // TOC'u en son eklediğimiz için sorun olmaz.
        // Yine de dikkatli olalım.
    }

    // 6. TOC Ekle
    // Yazının ilk paragrafı (</p>) bitiminden sonra ekle
    $pos = strpos($content, '</p>');
    if ($pos !== false) {
        $content = substr_replace($content, '</p>' . $toc_html, $pos, 4);
    } else {
        // </p> yoksa en başa ekle
        $content = $toc_html . "\n\n" . $content;
    }

    // 7. Kaydet
    $update = $db->prepare("UPDATE blog_posts SET content = ? WHERE id = ?");
    $update->execute([$content, $post['id']]);

    echo "<h2 style='color:green'>BAŞARILI! İçindekiler tablosu eklendi ve başlıklar güncellendi.</h2>";
    echo "<p>Artık dosyayı sunucudan silebilirsiniz.</p>";

} catch (PDOException $e) {
    die("Veritabanı Hatası: " . $e->getMessage());
} catch (Exception $e) {
    die("Genel Hata: " . $e->getMessage());
}
?>