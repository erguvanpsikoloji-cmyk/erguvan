<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/db.php';

try {
    $db = getDB();

    // Target slugs
    $slugs = ["ayrilik-kaygisi-nedir", "ayrilma-kaygisi-bozuklugu"];

    $title_map = [
        "ayrilik-kaygisi-nedir" => "Ayrılık Kaygısı Nedir?",
        "ayrilma-kaygisi-bozuklugu" => "Ayrılma Kaygısı Bozukluğu"
    ];

    $category = "Çocuk Psikolojisi";
    $author = "Uzman Psikolog Sena Ceren Parmaksız";
    $image = "assets/images/hero-oyun-terapisi.jpg"; // Using a valid existing image as fallback
    $excerpt = "Ayrılma kaygısı yaşayan çocuklarda hem duygusal hem de fiziksel belirtiler görülebilir. Bu belirtiler çocuğun günlük yaşamını olumsuz etkileyebilir.";

    $content_body = '
<p><strong>Ayrılma Kaygısı Bozukluğu Belirtileri</strong></p>
<p>Ayrılma kaygısı yaşayan çocuklarda hem duygusal hem de fiziksel belirtiler görülebilir. Bu belirtiler çocuğun günlük yaşamını, okul uyumunu ve sosyal ilişkilerini olumsuz etkileyebilir.</p>

<p>En sık görülen belirtiler şunlardır:</p>
<ul>
    <li>Anne veya babadan ayrılmak istememe</li>
    <li>Okula gitmeyi reddetme veya yoğun ağlama krizleri</li>
    <li>Ayrılık anlarında mide bulantısı, baş ağrısı, karın ağrısı</li>
    <li>Yalnız uyuyamama, kabuslar görme</li>
    <li>Anne babaya bir şey olacağına dair aşırı endişe</li>
</ul>
<p>Bu belirtiler uzun süre devam ediyorsa bir çocuk psikoloğu, psikolog veya pedagog tarafından değerlendirilmesi önemlidir.</p>

<p><strong>Ayrılık Kaygısı Bozukluğu Neden Olur?</strong></p>
<p>Ayrılık kaygısının ortaya çıkmasında birden fazla etken rol oynayabilir. Çocuğun yaşadığı çevresel değişiklikler ve duygusal deneyimler bu süreci tetikleyebilir.</p>

<p>Başlıca nedenler:</p>
<ul>
    <li>Okula başlama veya okul değişikliği</li>
    <li>Taşınma, aile düzeninde değişiklik</li>
    <li>Ebeveyn ayrılığı veya kayıp</li>
    <li>Travmatik yaşantılar</li>
    <li>Aşırı koruyucu ebeveyn tutumları</li>
</ul>
<p>Bu noktada bir pedagog ve çocuk psikoloğu iş birliğiyle çocuğun ihtiyaçlarının doğru şekilde belirlenmesi, sorunun sağlıklı biçimde ele alınmasını sağlar.</p>

<p><strong>Ayrılık Anksiyetesi Bozukluğu Tanı Kriterleri</strong></p>
<p>Ayrılık kaygısının klinik bir sorun olarak değerlendirilmesi için bazı tanı kriterlerinin karşılanması gerekir. Bu belirtilerin çocuğun yaşına uygun olmayan düzeyde ve en az 4 hafta boyunca devam etmesi önemlidir.</p>

<p>Tanı sürecinde:</p>
<ul>
    <li>Çocuğun gelişim düzeyi</li>
    <li>Kaygının süresi ve şiddeti</li>
    <li>Günlük yaşam üzerindeki etkileri</li>
</ul>
<p>bir psikolog veya çocuk psikoloğu tarafından ayrıntılı şekilde değerlendirilir. Gerekli görüldüğünde aile görüşmeleri de sürece dahil edilir.</p>

<p><strong>Ayrılma Kaygısı Bozukluğu Tedavisi</strong></p>
<p>Ayrılık kaygısı bozukluğunun tedavisinde çocuğun yaşına ve ihtiyaçlarına uygun terapi yöntemleri uygulanır. En etkili yaklaşımlardan biri oyun terapisidir. Oyun terapisi sayesinde çocuk, duygularını sözel olarak ifade edemediği durumlarda oyun yoluyla kendini güvenli bir şekilde anlatabilir.</p>

<p>Tedavi sürecinde:</p>
<ul>
    <li>Oyun terapisi</li>
    <li>Bilişsel davranışçı yaklaşımlar</li>
    <li>Ebeveyn danışmanlığı</li>
    <li>Pedagog desteği</li>
</ul>
<p>birlikte uygulanabilir. Alanında uzman bir çocuk psikoloğu, psikolog ve pedagog ile yürütülen süreç, çocuğun ayrılık durumlarıyla başa çıkma becerisini güçlendirir.</p>

<p>Erken dönemde alınan profesyonel destek, çocuğun özgüvenini artırır ve duygusal gelişimini sağlıklı şekilde sürdürmesine yardımcı olur.</p>';

    // Add anchors and TOC
    $headings = [
        'Ayrılma Kaygısı Bozukluğu Belirtileri' => 'belirtiler',
        'Ayrılık Kaygısı Bozukluğu Neden Olur?' => 'nedenler',
        'Ayrılık Anksiyetesi Bozukluğu Tanı Kriterleri' => 'tani',
        'Ayrılma Kaygısı Bozukluğu Tedavisi' => 'tedavi'
    ];

    $processed_content = $content_body;
    foreach ($headings as $text => $anchor) {
        $processed_content = str_replace('<p><strong>' . $text . '</strong></p>', '<h3 id="' . $anchor . '" style="color: #ec4899; margin-top: 30px; margin-bottom: 15px;">' . $text . '</h3>', $processed_content);
    }

    $toc_html = '
<div class="blog-toc" style="background-color: #fdf2f8; border: 1px solid #fbcfe8; border-radius: 12px; padding: 25px; margin-bottom: 30px; border-left: 5px solid #ec4899;">
    <h3 style="margin-top: 0; margin-bottom: 15px; color: #ec4899; font-size: 1.25rem; font-weight: 700;">İçindekiler</h3>
    <ul style="list-style: none; padding-left: 0; margin-bottom: 0;">
        <li style="margin-bottom: 10px; display: flex; align-items: flex-start;">
            <span style="color: #ec4899; margin-right: 10px;">•</span>
            <a href="#belirtiler" style="color: #475569; text-decoration: none; font-weight: 600; transition: all 0.3s; border-bottom: 1px dashed transparent;">Ayrılma Kaygısı Bozukluğu Belirtileri</a>
        </li>
        <li style="margin-bottom: 10px; display: flex; align-items: flex-start;">
            <span style="color: #ec4899; margin-right: 10px;">•</span>
            <a href="#nedenler" style="color: #475569; text-decoration: none; font-weight: 600; transition: all 0.3s; border-bottom: 1px dashed transparent;">Ayrılık Kaygısı Bozukluğu Neden Olur?</a>
        </li>
        <li style="margin-bottom: 10px; display: flex; align-items: flex-start;">
            <span style="color: #ec4899; margin-right: 10px;">•</span>
            <a href="#tani" style="color: #475569; text-decoration: none; font-weight: 600; transition: all 0.3s; border-bottom: 1px dashed transparent;">Ayrılık Anksiyetesi Bozukluğu Tanı Kriterleri</a>
        </li>
        <li style="margin-bottom: 0; display: flex; align-items: flex-start;">
            <span style="color: #ec4899; margin-right: 10px;">•</span>
            <a href="#tedavi" style="color: #475569; text-decoration: none; font-weight: 600; transition: all 0.3s; border-bottom: 1px dashed transparent;">Ayrılma Kaygısı Bozukluğu Tedavisi</a>
        </li>
    </ul>
</div>
<style>
.blog-toc a:hover { color: #ec4899 !important; border-bottom-color: #ec4899 !important; padding-left: 5px; }
.blog-post-content h3 { border-bottom: 1px solid #fce7f3; padding-bottom: 10px; }
</style>';

    $final_content = $toc_html . $processed_content;

    foreach ($slugs as $s) {
        $title = $title_map[$s] ?? "Blog Yazısı";
        $stmt = $db->prepare("SELECT id FROM blog_posts WHERE slug = ?");
        $stmt->execute([$s]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $db->prepare("UPDATE blog_posts SET title = ?, content = ?, excerpt = ?, category = ?, author = ?, image = ? WHERE id = ?");
            $stmt->execute([$title, $final_content, $excerpt, $category, $author, $image, $existing['id']]);
            echo "Blog yazısı güncellendi: $s <br>";
        } else {
            $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, content, category, author, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $s, $excerpt, $final_content, $category, $author, $image]);
            echo "Blog yazısı oluşturuldu: $s <br>";
        }
    }

} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>