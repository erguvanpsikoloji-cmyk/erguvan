<?php
// Config ve Helper
require_once __DIR__ . '/config.php';

echo "<h1>İçindekiler Tablosu Ekleme Aracı</h1>";

// 1. Hedef blog yazısını bul
$target_slug = 'ayrilik-kaygisi-nedir'; // Veya başlığa göre: 'ayr-l-k-kayg-s-nedir'
// Başlık üzerinden aramak daha güvenli olabilir
$stmt = $db->prepare("SELECT * FROM blog_posts WHERE title LIKE ? OR slug LIKE ? LIMIT 1");
$title_search = "%Ayrılık Kaygısı Nedir%";
$stmt->execute([$title_search, $target_slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die("<div style='color:red'>❌ Hata: 'Ayrılık Kaygısı Nedir' başlıklı blog yazısı bulunamadı. Lütfen panelden yazının başlığını kontrol edin.</div>");
}

echo "<div style='color:green'>✅ Yazı bulundu: " . htmlspecialchars($post['title']) . " (ID: " . $post['id'] . ")</div>";

$content = $post['content'];
$original_content_length = strlen($content);

// 2. Başlıkları ID'lerle değiştir
// Hedeflenen başlıklar ve atanacak ID'ler
$headers_map = [
    'Ayrılma Kaygısı Bozukluğu Belirtileri' => 'belirtiler',
    'Ayrılık Kaygısı Bozukluğu Neden Olur?' => 'nedenleri',
    'Ayrılık Kaygısı Bozukluğu Neden Olur' => 'nedenleri', // Soru işaretsiz versiyon
    'Ayrılık Anksiyetesi Bozukluğu Tanı Kriterleri' => 'tani-kriterleri',
    'Ayrılma Kaygısı Bozukluğu Tedavisi' => 'tedavisi'
];

$found_headers = [];

foreach ($headers_map as $header_text => $id) {
    // <h2>...Header...</h2> veya <h3>...Header...</h3> gibi başlıkları bulur
    // İhtimal: Başlıklar <h2>, <h3>, <strong> veya düz metin olabilir. 
    // En güvenlisi içeriği replace etmektir.

    // Önce h2/h3 içinde mi diye bak
    $pattern = '/<(h[2-6])[^>]*>(\s*' . preg_quote($header_text, '/') . '\s*)<\/\1>/ui';

    if (preg_match($pattern, $content)) {
        // Zaten header etiketi var, ID ekle
        $content = preg_replace($pattern, '<$1 id="' . $id . '">$2</$1>', $content, 1);
        $found_headers[$header_text] = $id;
    } else {
        // Belki strong etiketi içindedir veya düz metindir, bu durumda h2'ye çevirip ID ekleyelim
        // Sadece metin olarak arayalım
        if (strpos($content, $header_text) !== false) {
            // Sadece ilk eşleşmeyi değiştir
            // Eğer zaten id="belirtiler" varsa dokunma
            if (strpos($content, 'id="' . $id . '"') === false) {
                // Basit string replace (dikkatli olmalı)
                // En iyisi: Mevcut yazıda bu başlıkların nasıl geçtiğini varsayarak <h2 id=...> ile sarmak
                // Eğer başlık zaten h tagindeyse yukarıdaki regex yakalamalıydı. Yakalamadıysa demek ki tag farklı.

                // Deneme 2: Strong etiketi
                $pattern_strong = '/<strong>(\s*' . preg_quote($header_text, '/') . '\s*)<\/strong>/ui';
                if (preg_match($pattern_strong, $content)) {
                    $content = preg_replace($pattern_strong, '<h2 id="' . $id . '">$1</h2>', $content, 1);
                    $found_headers[$header_text] = $id;
                } else {
                    // Düz metin (riskli ama deneyelim) - Genellikle yapmazlar ama..
                    // Pas geçelim, yanlış yeri değiştirmeyelim.
                }
            }
        }
    }
}

// 3. İçindekiler HTML'ini oluştur
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

// 4. TOC'u içeriğin başına ekle (Eğer zaten yoksa)
if (strpos($content, 'class="toc-container"') === false) {
    // İlk paragraftan sonra eklemek daha şık olabilir, ama şimdilik en başa ekleyelim.
    // Veya giriş cümlesinden sonraya...
    // En garantisi en başa eklemek.
    $content = $toc_html . "\n\n" . $content;
    echo "<div style='color:green'>✅ İçindekiler tablosu içeriğin başına eklendi.</div>";
} else {
    echo "<div style='color:orange'>⚠️ İçindekiler tablosu zaten var, tekrar eklenmedi.</div>";
}

// 5. Veritabanını güncelle
if ($content !== $post['content']) {
    try {
        $update_stmt = $db->prepare("UPDATE blog_posts SET content = ? WHERE id = ?");
        $update_stmt->execute([$content, $post['id']]);
        echo "<div style='color:green'>🎉 <b>BAŞARILI:</b> Blog yazısı güncellendi!</div>";
        echo "<p>Değiştirilen karakter sayısı: " . (strlen($content) - $original_content_length) . "</p>";
    } catch (PDOException $e) {
        echo "<div style='color:red'>❌ Veritabanı hatası: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div style='color:blue'>ℹ️ İçerikte değişiklik yapılmadı (Başlıklar bulunamamış veya zaten güncel olabilir).</div>";
}

// Linkleri göster
echo "<br><a href='" . page_url('blog.php') . "' target='_blank'>Blogu Görüntüle</a>";
?>