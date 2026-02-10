<?php
/**
 * ERGUVAN PSİKOLOJİ - PARALEL TOC VE HİZA DÜZELTME (V6)
 * H1, H2 ve H3 başlıklarını aynı hizaya (paralel) getirir ve noktaları ortalar.
 */

require_once __DIR__ . '/config.php';

$path = 'pages/blog-post.php';
$content = <<<'EOD'
<?php
require_once __DIR__ . '/../config.php';
$page = 'blog';
require_once '../database/db.php';

try {
    $db = getDB();
    $post = null;
    $slug = $_GET['slug'] ?? '';
    if ($slug) {
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = ?");
        $stmt->execute([$slug]);
        $post = $stmt->fetch();
    }
    if (!$post) redirect(page_url('blog.php'));
} catch (Exception $e) { redirect(page_url('blog.php')); }

function injectTOC(&$content, $manual_toc_json = '') {
    // TAM PARALEL VE HİZALI CSS
    $style = '
    <style id="erguvan-toc-style">
        .toc-container-fresh {
            background-color: #fff8fb !important;
            border: 1px solid #fbcfe8 !important;
            border-left: 12px solid #ec4899 !important;
            border-radius: 20px !important;
            padding: 2rem !important;
            margin: 2.5rem 0 !important;
            box-shadow: 0 4px 20px rgba(236,72,153,0.08) !important;
            display: block !important;
        }
        .toc-title-fresh {
            color: #ec4899 !important;
            font-size: 1.6rem !important;
            font-weight: 800 !important;
            margin-bottom: 1.5rem !important;
            border: none !important;
        }
        .toc-list-fresh {
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .toc-list-fresh li {
            position: relative !important;
            padding-left: 1.8rem !important; /* TÜMÜ İÇİN AYNI SOL BOŞLUK (PARALEL) */
            margin-bottom: 10px !important;
            font-size: 1.1rem !important;
            line-height: 1.4 !important;
            color: #334155 !important;
            font-weight: 600 !important;
            display: block !important;
        }
        /* H3 için hizayı bozmuyoruz, sadece fontu biraz küçültüyoruz */
        .toc-list-fresh li.toc-h3 {
            font-size: 1rem !important;
            font-weight: 500 !important;
            color: #64748b !important;
            padding-left: 1.8rem !important; /* Hiza bozulmasın diye aynı bırakıldı */
        }
        /* NOKTALARIN TAM HİZALANMASI */
        .toc-list-fresh li::before {
            content: "•" !important;
            color: #ec4899 !important;
            font-size: 1.8rem !important;
            position: absolute !important;
            left: 0 !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            line-height: 1 !important;
        }
        .toc-list-fresh li.toc-h3::before {
            font-size: 1.5rem !important;
            opacity: 0.8;
            left: 0 !important; /* H3 noktası da en solda (paralel) */
        }
        .toc-list-fresh a {
            color: inherit !important;
            text-decoration: none !important;
            transition: color 0.3s ease;
        }
        .toc-list-fresh a:hover { color: #ec4899 !important; }
        h1, h2, h3 { scroll-margin-top: 100px !important; }
    </style>';

    if (strpos($content, 'toc-container') !== false) return;

    $toc_items = [];
    $manual_data = json_decode($manual_toc_json, true);

    if (!empty($manual_data) && is_array($manual_data)) {
        foreach ($manual_data as $i => $item) {
            $id = 'bolum-' . ($i + 1);
            $level = $item['level'] ?: 'h2';
            // İçerikteki başlığı bul ve ID ata
            $pattern = '/<(h1|h2|h3|h4|strong|p)[^>]*>\s*' . preg_quote($item['text'], '/') . '\s*<\/h\1>/ui';
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "<$level id=\"$id\">$item[text]</$level>", $content, 1);
            }
            $toc_items[] = ['text' => $item['text'], 'id' => $id, 'class' => 'toc-'.$level];
        }
    }

    if (empty($toc_items)) return;

    $toc = '<div class="toc-container-fresh"><div class="toc-title-fresh">İçindekiler</div><ul class="toc-list-fresh">';
    foreach ($toc_items as $item) {
        $toc .= "<li class=\"$item[class]\"><a href=\"#$item[id]\">$item[text]</a></li>";
    }
    $toc .= '</ul></div>';
    $content = $style . $toc . $content;
}

injectTOC($post['content'], $post['toc_data'] ?? '');
include '../includes/header.php';
?>
<article class="blog-post">
    <div class="blog-post-header"><div class="container">
        <a href="<?php echo page_url('blog.php'); ?>" class="back-link">← Blog'a Dön</a>
        <h1 class="blog-post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
    </div></div>
    <div class="blog-post-image"><img src="<?php echo webp_url($post['image']); ?>" alt=""></div>
    <div class="blog-post-content"><div class="container"><?php echo $post['content']; ?></div></div>
</article>
<?php include '../includes/footer.php'; ?>
EOD;

chmod($path, 0666); // İzinleri esnet
if (file_put_contents($path, $content)) {
    echo "<h1>BAŞARILI</h1><p>H2 ve H3 başlıkları artık tam paralel ve noktalar hizalı.</p>";
} else {
    echo "<h1>HATA</h1><p>Dosyaya yazılamadı. Lütfen sunucudan pages/blog-post.php dosyasını SİLİP bu sayfayı yenileyin.</p>";
}
unlink(__FILE__);
?>