<?php
/**
 * İçindekiler Tasarımı Kurulum Betiği - v5
 * Bu dosya blog post sayfasını ve CSS dosyalarını sunucu üzerinde GÜVENLİCE günceller.
 * Sürükle-bırak yaparken oluşan "0 byte" hatasını aşmak için tasarlanmıştır.
 */

// BLOG POST İÇERİĞİ (TOC Tasarımı Dahil)
$blog_post_content = <<<'EOD'
<?php
require_once __DIR__ . '/../config.php';
$page = 'blog';
require_once '../database/db.php';

try {
    $db = getDB();

    // Slug veya ID ile blog yazısını getir
    $post = null;
    if (isset($_GET['slug']) && !empty($_GET['slug'])) {
        $slug = trim($_GET['slug']);
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = ?");
        $stmt->execute([$slug]);
        $post = $stmt->fetch();
    } elseif (isset($_GET['id']) && !empty($_GET['id'])) {
        $post_id = (int) $_GET['id'];
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch();

        if ($post && !empty($post['slug'])) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $new_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . BASE_URL . '/blog/' . $post['slug'];
            header('Location: ' . $new_url, true, 301);
            exit;
        }
    }

    if (!$post) {
        redirect(page_url('blog.php'));
    }
} catch (Exception $e) {
    error_log('Database error in blog-post.php: ' . $e->getMessage());
    redirect(page_url('blog.php'));
}

$page_title = $post['title'];
$page_description = $post['excerpt'];
$page_keywords = $post['category'] . ', ' . strtolower($post['title']) . ', psikoloji, mental sağlık, terapi';
$page_type = 'article';
$page_image = $post['image'] ?? '';
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$canonical_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . BASE_URL . '/blog/' . $post['slug'];

/**
 * Dinamik İçindekiler Tablosu (TOC) Oluşturucu
 */
function injectTOC(&$content)
{
    // TASARIM GARANTİSİ: Şık pembe tasarım, PHP içinden her koşulda yüklenir.
    $style = '
    <style id="erguvan-toc-style">
        .toc-container, .toc-container-fresh, .blog-toc {
            background-color: #fff8fb !important;
            border: 1px solid #fbcfe8 !important;
            border-left: 12px solid #ec4899 !important;
            border-radius: 20px !important;
            padding: 2.5rem !important;
            margin: 2.5rem 0 !important;
            box-shadow: 0 4px 20px rgba(236,72,153,0.08) !important;
            font-family: inherit !important;
            display: block !important;
            box-sizing: border-box !important;
        }
        .toc-title, .toc-title-fresh, .blog-toc h2 {
            color: #ec4899 !important;
            font-size: 1.6rem !important;
            font-weight: 800 !important;
            margin-bottom: 1.5rem !important;
            margin-top: 0 !important;
            border: none !important;
            font-family: inherit !important;
        }
        .toc-list, .toc-list-fresh, .blog-toc ul {
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .toc-list li, .toc-list-fresh li, .blog-toc li {
            position: relative !important;
            padding-left: 1.8rem !important;
            margin-bottom: 0.8rem !important;
            font-size: 1.1rem !important;
            color: #334155 !important;
            font-weight: 600 !important;
        }
        .toc-list li::before, .toc-list-fresh li::before, .blog-toc li::before {
            content: "•" !important;
            color: #ec4899 !important;
            font-size: 1.8rem !important;
            position: absolute !important;
            left: 0 !important;
            top: -5px !important;
            font-weight: bold !important;
        }
        .toc-list a, .toc-list-fresh a, .blog-toc a {
            color: #475569 !important;
            text-decoration: none !important;
            transition: color 0.3s ease !important;
        }
        .toc-list a:hover { color: #ec4899 !important; }
        h2, h3 { scroll-margin-top: 100px !important; }
    </style>';

    if (strpos($content, 'toc-container') !== false || strpos($content, 'blog-toc') !== false) {
        $content = $style . $content;
        return;
    }

    $pattern = '/<(h2)(.*?)>(.*?)<\/h\1>/i';
    if (!preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
        return;
    }

    $toc = '<div class="toc-container-fresh">
        <div class="toc-title-fresh">İçindekiler</div>
        <ul class="toc-list-fresh">';

    foreach ($matches as $i => $match) {
        $text = strip_tags($match[3]);
        $id = 'bolum-' . ($i + 1);
        if (strpos($match[2], 'id=') === false) {
            $newHeading = "<h2 id=\"$id\"$match[2]>$match[3]</h2>";
            $content = preg_replace('/' . preg_quote($match[0], '/') . '/', $newHeading, $content, 1);
        }
        $toc .= "<li><a href=\"#$id\">$text</a></li>";
    }

    $toc .= '</ul></div>';
    $content = $style . $toc . $content;
}

injectTOC($post['content']);
include '../includes/header.php';
?>

<article class="blog-post">
    <div class="blog-post-header">
        <div class="container">
            <div class="blog-post-header-content">
                <a href="<?php echo page_url('blog.php'); ?>" class="back-link">← Blog'a Dön</a>
                <span class="blog-category large"><?php echo $post['category']; ?></span>
                <h1 class="blog-post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                <div class="blog-post-meta">
                    <span class="author">
                        <?php
                        $logoPngPath = __DIR__ . '/../assets/images/logo.png';
                        if (file_exists($logoPngPath)):
                            ?>
                            <img src="<?php echo asset_url('images/logo.png'); ?>" alt="<?php echo $post['author']; ?>"
                                class="author-avatar" width="100" height="100" loading="lazy" decoding="async">
                        <?php else: ?>
                            <div class="author-avatar"
                                style="width: 100px; height: 100px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 700;">
                                <?php echo mb_substr($post['author'], 0, 1); ?>
                            </div>
                        <?php endif; ?>
                        <?php echo $post['author']; ?>
                    </span>
                    <span class="separator">•</span>
                    <span class="date">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <?php echo date('d F Y', strtotime($post['created_at'])); ?>
                    </span>
                    <span class="separator">•</span>
                    <span class="reading-time">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <?php echo $post['reading_time']; ?> okuma
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="blog-post-image">
        <img src="<?php echo webp_url($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>"
            width="1200" height="600" loading="eager" fetchpriority="high" decoding="async">
    </div>

    <div class="blog-post-content">
        <div class="container">
            <div class="content-wrapper">
                <?php echo $post['content']; ?>
            </div>

            <div class="blog-post-footer">
                <div class="share-section">
                    <h4>Bu yazıyı paylaş:</h4>
                    <div class="share-buttons">
                        <a href="#" class="share-btn facebook">Facebook</a>
                        <a href="#" class="share-btn twitter">Twitter</a>
                        <a href="#" class="share-btn whatsapp">WhatsApp</a>
                        <a href="#" class="share-btn linkedin">LinkedIn</a>
                    </div>
                </div>
            </div>

            <div class="related-posts">
                <h3>İlgili Yazılar</h3>
                <div class="blog-grid">
                    <?php
                    $related_stmt = $db->prepare("SELECT * FROM blog_posts WHERE category = ? AND id != ? LIMIT 3");
                    $related_stmt->execute([$post['category'], $post['id']]);
                    $related_posts = $related_stmt->fetchAll();

                    foreach ($related_posts as $related):
                        ?>
                        <article class="blog-card">
                            <div class="blog-card-image">
                                <img src="<?php echo webp_url($related['image']); ?>"
                                    alt="<?php echo htmlspecialchars($related['title']); ?>" width="400" height="250"
                                    loading="lazy" decoding="async">
                                <span class="blog-category"><?php echo $related['category']; ?></span>
                            </div>
                            <div class="blog-card-content">
                                <div class="blog-meta">
                                    <span class="blog-date">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2"
                                            style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <?php echo date('d.m.Y', strtotime($related['created_at'])); ?>
                                    </span>
                                    <span class="blog-reading-time">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2"
                                            style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <?php echo $related['reading_time']; ?>
                                    </span>
                                </div>
                                <h3 class="blog-card-title"><?php echo htmlspecialchars($related['title']); ?></h3>
                                <p class="blog-card-excerpt"><?php echo htmlspecialchars($related['excerpt']); ?></p>
                                <a href="<?php echo url('blog/' . $related['slug']); ?>" class="blog-read-more">Devamını Oku
                                    →</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (!empty($post['keywords'])): ?>
                <div class="blog-keywords">
                    <div class="container">
                        <div class="keywords-wrapper">
                            <h4 class="keywords-title">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2"
                                    style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                Anahtar Kelimeler
                            </h4>
                            <div class="keywords-list">
                                <?php
                                $keywords_array = array_map('trim', explode(',', $post['keywords']));
                                foreach ($keywords_array as $keyword):
                                    if (!empty($keyword)):
                                        ?>
                                        <span class="keyword-tag"><?php echo htmlspecialchars($keyword); ?></span>
                                        <?php
                                    endif;
                                endforeach;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</article>

<?php include '../includes/footer.php'; ?>
EOD;

echo "<h2>Tasarım Güncelleme Aracı (v5)</h2>";

$target_file = 'pages/blog-post.php';

if (file_put_contents($target_file, $blog_post_content)) {
    echo "<p style='color:green;'>✓ <b>$target_file</b> başarıyla güncellendi.</p>";
    echo "<p>Artık blog sayfanızı yenileyip tasarımı kontrol edebilirsiniz.</p>";
} else {
    echo "<p style='color:red;'>✗ <b>$target_file</b> güncellenemedi! Yazma izni olmayabilir.</p>";
}

echo "<hr><p>Bu scripti çalıştırdıktan sonra lütfen dosyayı sunucudan silin.</p>";
EOD;
?>