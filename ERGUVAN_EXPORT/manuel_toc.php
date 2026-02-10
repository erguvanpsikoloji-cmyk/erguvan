<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Manuel olarak veritabanı bağlantısı ve güncelleme yapalım
// Config dosyasını yüklemeyi deneyelim, olmazsa manuel parametre girelim
$config_path = __DIR__ . '/config.php';

if (!file_exists($config_path)) {
    die("Config dosyası bulunamadı ($config_path).");
}

require_once $config_path;

echo "<h1>İçindekiler Tablosu - Manuel Güncelleme</h1>";

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
    // 1. Yazıyı Bul
    $stmt = $db->prepare("SELECT id, title, content FROM blog_posts WHERE title LIKE ? OR slug = ? LIMIT 1");
    $stmt->execute(['%Ayrılık Kaygısı Nedir%', $target_slug]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        die("Yazı bulunamadı.");
    }

    echo "Yazı bulundu: " . htmlspecialchars($post['title']) . "<br>";
    $content = $post['content'];

    // 2. Eğer zaten ekliyse dur
    if (strpos($content, 'class="toc-container"') !== false) {
        die("İçindekiler tablosu zaten ekli. İşlem yapılmadı.");
    }

    // 3. Başlıkları Bul ve ID Ekle (Basit String Replace ile - Daha güvenli)
    $replacements = [
        'Ayrılma Kaygısı Bozukluğu Belirtileri' => '<h2 id="belirtiler">Ayrılma Kaygısı Bozukluğu Belirtileri</h2>',
        'Ayrılık Kaygısı Bozukluğu Neden Olur?' => '<h2 id="nedenleri">Ayrılık Kaygısı Bozukluğu Neden Olur?</h2>',
        'Ayrılık Anksiyetesi Bozukluğu Tanı Kriterleri' => '<h2 id="tani-kriterleri">Ayrılık Anksiyetesi Bozukluğu Tanı Kriterleri</h2>',
        'Ayrılma Kaygısı Bozukluğu Tedavisi' => '<h2 id="tedavisi">Ayrılma Kaygısı Bozukluğu Tedavisi</h2>'
    ];

    // Mevcut başlıkları temizle (Eğer h2, h3, strong vb içindeyse)
    // Bu adım riskli olabilir, o yüzden sadece bilinen metinleri değiştirelim.
    // HTML taglerini temizleyip temiz bir h2 olarak eklemek yerine, 
    // doğrudan metni bulup değiştirmeyi deneyelim, çevreleyen tagleri koruyarak.

    foreach ($replacements as $search => $replace) {
        // Eğer metin h2, h3 veya strong içindeyse, onu id'li versiyona çevir
        // Basitçe: Metni bul, önceki tag'i önemseme, doğrudan replace et demek riskli.
        // Güvenli yöntem: İçeriğin en başına TOC eklemek. Başlıkları elle düzeltmek gerekebilir veya...

        // Şimdilik sadece TOC ekleyelim, linkler çalışmasa da görsel olarak görünsün.
        // Linklerin çalışması için metin içinde o ID'lerin olması lazım.

        // Basit replace deneyelim (Genelde çalışır)
        // Eğer metin <h2>...</h2> içindeyse
        $content = preg_replace('/<h[2-6]>(.*?)' . preg_quote($search, '/') . '(.*?)<\/h[2-6]>/iu', $replace, $content);

        // Eğer metin <strong>...</strong> içindeyse
        $content = preg_replace('/<strong>(.*?)' . preg_quote($search, '/') . '(.*?)<\/strong>/iu', $replace, $content);

        // Eğer düz metinse (p etiketi içinde olabilir)
        // $content = str_replace($search, $replace, $content); // Bu çok agresif olabilir
    }

    // 4. TOC Ekle
    // Yazının ilk paragrafı bitiminden sonra eklemek istersek:
    // </p> tagini ilk kez gördüğü yerden sonraya ekle
    $pos = strpos($content, '</p>');
    if ($pos !== false) {
        $content = substr_replace($content, '</p>' . $toc_html, $pos, 4);
    } else {
        $content = $toc_html . $content;
    }

    // 5. Kaydet
    $update = $db->prepare("UPDATE blog_posts SET content = ? WHERE id = ?");
    $update->execute([$content, $post['id']]);

    echo "<h2 style='color:green'>BAŞARILI! İçindekiler eklendi.</h2>";
    echo "<a href='/'>Ana Sayfaya Dön</a>";

} catch (PDOException $e) {
    die("Veritabanı Hatası: " . $e->getMessage());
}
?>