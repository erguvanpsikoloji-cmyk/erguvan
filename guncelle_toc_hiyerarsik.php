<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Ayrılık Kaygısı Yazısı İçerik ve Başlık Güncelleme Scripti
 * Bu script veritabanındaki yazıyı düzenleyerek İçindekiler'in doğru oluşmasını sağlar.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/db.php';

$db = getDB();
$slug = 'ayrilik-kaygisi-nedir';

try {
    $stmt = $db->prepare("SELECT id, content FROM blog_posts WHERE slug = ?");
    $stmt->execute([$slug]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        die("HATA: Yazı bulunamadı ($slug).");
    }

    $content = $post['content'];

    // 1. Önce veritabanında daha önceden kalmış olabilecek hardcoded TOC (İçindekiler) kutusunu temizleyelim
    // Çünkü artık pages/blog-post.php bu kutuyu otomatik (dinamik) oluşturuyor. 
    // İki tane tablo olmaması için DB'dekini siliyoruz.
    $patterns_to_remove = [
        '/<div class="toc-container">.*?<\/div>/is',
        '/<div class="toc-container-fresh">.*?<\/div>/is'
    ];
    foreach ($patterns_to_remove as $p) {
        $content = preg_replace($p, '', $content);
    }

    // 2. Başlıkları kullanıcının istediği şekilde güncelleyelim (H2 ve H3 hiyerarşisi)
    // Bu başlıklar içerikte varsa onları doğru taglere çevireceğiz.

    // Değişim listesi: 'Aranan metin' => 'Yeni Tag yapısı'
    $replacements = [
        'Ayrılık Kaygısı Bozukluğu Neden Olur?' => '<h2>Ayrılık Kaygısı Bozukluğu Neden Olur?</h2>',
        'Erken Çocukluk Dönemi ve Bağlanma Sorunları' => '<h3>Erken Çocukluk Dönemi ve Bağlanma Sorunları</h3>',
        'Aile Tutumlarının Ayrılık Kaygısına Etkisi' => '<h3>Aile Tutumlarının Ayrılık Kaygısına Etkisi</h3>',
        'Travmatik Yaşantılar ve Çevresel Faktörler' => '<h3>Travmatik Yaşantılar ve Çevresel Faktörler</h3>'
    ];

    foreach ($replacements as $search => $replace) {
        // Eğer başlık zaten varsa ama tagi yanlışsa veya id eklenmişse diye esnek regex
        $pattern = '/<(h[2-6]|strong|p)[^>]*>\s*' . preg_quote($search, '/') . '\s*<\/\1>/ui';
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $replace, $content, 1);
        } else {
            // Hiç bulunamazsa metin olarak arayalım (HTML tagleri olmadan)
            $content = str_ireplace($search, $replace, $content);
        }
    }

    // 3. Veritabanını Güncelle
    $update = $db->prepare("UPDATE blog_posts SET content = ? WHERE id = ?");
    $update->execute([$content, $post['id']]);

    echo "<h2>BAŞARILI!</h2>";
    echo "<p><b>'$slug'</b> yazısındaki başlıklar H2/H3 olarak güncellendi ve eski tablolar temizlendi.</p>";
    echo "<p>Artık dinamik İçindekiler tablosu bu başlıkları hiyerarşik (iç içe) olarak gösterecektir.</p>";
    echo "<hr><p>Lütfen bu dosyayı sunucudan silin.</p>";

} catch (Exception $e) {
    die("HATA: " . $e->getMessage());
}
?>