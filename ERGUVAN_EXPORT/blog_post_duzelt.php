<?php
/**
 * blog-post.php Dosyası Zorlamalı Güncelleyici
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
    if (isset($_GET['slug']) && !empty($_GET['slug'])) {
        $slug = trim($_GET['slug']);
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = ?");
        $stmt->execute([$slug]);
        $post = $stmt->fetch();
    }
    if (!$post) redirect(page_url('blog.php'));
} catch (Exception $e) { redirect(page_url('blog.php')); }

function injectTOC(&$content, $manual_toc_json = '') {
    $style = '<style id="erguvan-toc-style">.toc-container-fresh { background-color: #fff8fb !important; border: 1px solid #fbcfe8 !important; border-left: 12px solid #ec4899 !important; border-radius: 20px !important; padding: 2.5rem !important; margin: 2.5rem 0 !important; box-shadow: 0 4px 20px rgba(236,72,153,0.08) !important; display: block !important; } .toc-title-fresh { color: #ec4899 !important; font-size: 1.6rem !important; font-weight: 800 !important; margin-bottom: 1.5rem !important; } .toc-list-fresh { list-style: none !important; padding: 0 !important; } .toc-list-fresh li { position: relative !important; padding-left: 1.8rem !important; margin-bottom: 0.8rem !important; font-size: 1.1rem !important; font-weight: 600 !important; color: #334155 !important; } .toc-list-fresh li.toc-h3 { padding-left: 3.5rem !important; font-size: 1rem !important; font-weight: 500 !important; color: #64748b !important; } .toc-list-fresh li::before { content: "•" !important; color: #ec4899 !important; position: absolute; left: 0; font-size: 1.8rem !important; top: -5px; } .toc-list-fresh li.toc-h3::before { left: 1.8rem !important; } .toc-list-fresh a { color: inherit !important; text-decoration: none !important; } h2, h3 { scroll-margin-top: 100px !important; }</style>';
    if (strpos($content, 'toc-container') !== false) return;

    $toc_items = [];
    $manual_data = json_decode($manual_toc_json, true);

    if (!empty($manual_data) && is_array($manual_data)) {
        foreach ($manual_data as $i => $item) {
            $id = 'bolum-' . ($i + 1);
            $pattern = '/<(h2|h3|h4|strong|p)[^>]*>\s*' . preg_quote($item['text'], '/') . '\s*<\/\1>/ui';
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "<$item[level] id=\"$id\">$item[text]</$item[level]>", $content, 1);
            }
            $toc_items[] = ['text' => $item['text'], 'id' => $id, 'class' => ($item['level'] === 'h3' ? 'toc-h3' : 'toc-h2')];
        }
    } else {
        $pattern = '/<(h2|h3)(.*?)>(.*?)<\/h\1>/i';
        if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $i => $match) {
                $id = 'bolum-' . ($i + 1);
                $content = preg_replace('/' . preg_quote($match[0], '/') . '/', "<$match[1] id=\"$id\"$match[2]>$match[3]</$match[1]>", $content, 1);
                $toc_items[] = ['text' => strip_tags($match[3]), 'id' => $id, 'class' => ($match[1] === 'h3' ? 'toc-h3' : 'toc-h2')];
            }
        }
    }

    if (empty($toc_items)) return;
    $toc = '<div class="toc-container-fresh"><div class="toc-title-fresh">İçindekiler</div><ul class="toc-list-fresh">';
    foreach ($toc_items as $item) { $toc .= "<li class=\"$item[class]\"><a href=\"#$item[id]\">$item[text]</a></li>"; }
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

chmod($path, 0644); // Yazma izni vermeyi dene
if (file_put_contents($path, $content)) {
    echo "<h1>BAŞARILI</h1><p>Blog yazısı şablonu güncellendi.</p>";
} else {
    echo "<h1>HATA</h1><p>Dosyaya yazılamadı. Lütfen FTP üzerinden pages/blog-post.php dosyasını silip tekrar deneyin.</p>";
}
unlink(__FILE__);
?>