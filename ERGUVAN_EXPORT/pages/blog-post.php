<?php
/**
 * ERGUVAN PSİKOLOJİ - PREMIUM BLOG TASARIMI (V8)
 * Yazı stilleri, fontlar, boşluklar ve genel blog görünümünü profesyonel seviyeye taşır.
 */

require_once __DIR__ . '/../config.php';
require_once '../database/db.php';

$db = getDB();
$slug = $_GET['slug'] ?? '';
$post = $db->prepare("SELECT * FROM blog_posts WHERE slug = ?");
$post->execute([$slug]);
$post = $post->fetch();
if (!$post)
    redirect(BASE_URL . '/blog');

$page_title = $post['title'];
$page_description = $post['meta_description'] ?: $post['excerpt'];

function injectPremiumDesign(&$content, $post)
{
    // PREMIUM CSS
    $style = '
    <style id="erguvan-premium-design">
        :root {
            --erguvan-pink: #ec4899;
            --erguvan-light-pink: #fff8fb;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        /* GENEL BLOG DÜZENİ */
        .blog-post-wrapper {
            max-width: 850px;
            margin: 0 auto;
            padding: 0 20px;
            font-family: "Inter", sans-serif;
        }

        .blog-post-content {
            font-size: 1.15rem !important; /* ~18px - Profesyonel okuma boyutu */
            line-height: 1.8 !important; /* Rahat satır aralığı */
            color: var(--text-dark) !important;
        }

        .blog-post-content p {
            margin-bottom: 2rem !important; /* Paragraflar arası temiz boşluk */
        }

        /* BAŞLIK STİLLERİ */
        .blog-post-content h2 {
            font-size: 2rem !important;
            font-weight: 800 !important;
            color: var(--text-dark) !important;
            margin: 3.5rem 0 1.5rem 0 !important;
            line-height: 1.3 !important;
            border-left: 6px solid var(--erguvan-pink);
            padding-left: 15px;
        }

        .blog-post-content h3 {
            font-size: 1.6rem !important;
            font-weight: 700 !important;
            color: var(--text-dark) !important;
            margin: 2.5rem 0 1.2rem 0 !important;
        }

        /* İÇİNDEKİLER (TOC) - TAM PARALEL VE ŞIK */
        .toc-container-premium {
            background: var(--erguvan-light-pink);
            border: 1px solid #fbcfe8;
            border-left: 12px solid var(--erguvan-pink);
            border-radius: 24px;
            padding: 2.5rem;
            margin: 3rem 0;
            box-shadow: 0 10px 30px rgba(236,72,153,0.05);
        }

        .toc-title-premium {
            color: var(--erguvan-pink);
            font-size: 1.7rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toc-list-premium {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .toc-list-premium li {
            position: relative;
            padding-left: 2.2rem; /* Tam paralellik için geniş boşluk */
            margin-bottom: 14px;
            transition: all 0.2s;
            line-height: 1.4;
            display: flex;
            align-items: flex-start;
        }

        .toc-list-premium li.toc-h1 {
            font-weight: 800;
            color: var(--text-dark);
            font-size: 1.2rem;
        }

        .toc-list-premium li.toc-h2 {
            font-weight: 700;
            color: var(--text-dark);
            font-size: 1.15rem;
        }

        .toc-list-premium li.toc-h3 {
            font-weight: 500;
            color: var(--text-muted);
            font-size: 1.05rem;
        }

        .toc-list-premium li::before {
            content: "•";
            color: var(--erguvan-pink);
            font-size: 2rem;
            position: absolute;
            left: 0;
            top: -4px; /* Bulleti yukari hizala */
            line-height: 1;
        }

        .toc-list-premium a {
            color: inherit;
            text-decoration: none;
        }

        .toc-list-premium a:hover {
            color: var(--erguvan-pink);
            padding-left: 5px;
        }

        /* SSS / FAQ STİLLERİ */
        .faq-premium {
            margin-top: 5rem;
            background: #f8fafc;
            border-radius: 30px;
            padding: 3rem;
        }

        .faq-item-premium {
            background: white;
            border-radius: 18px;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
        }

        .faq-question-premium {
            padding: 1.5rem;
            cursor: pointer;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-dark);
        }

        .faq-answer-premium {
            padding: 0 1.5rem;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s;
            color: var(--text-muted);
            line-height: 1.7;
        }

        .faq-item-premium.active .faq-answer-premium {
            padding-bottom: 1.5rem;
            max-height: 1000px;
        }

        /* GÖRSEL DÜZENLEME */
        .blog-post-content img {
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin: 2rem 0;
        }
    </style>
    ';

    // TOC INJECT
    $toc_items = [];
    $manual_toc = json_decode($post['toc_data'], true);
    if (!empty($manual_toc)) {
        foreach ($manual_toc as $i => $item) {
            $id = 'section-' . ($i + 1);
            $level = $item['level'] ?: 'h2';
            $toc_items[] = ['text' => $item['text'], 'id' => $id, 'class' => 'toc-' . $level];
            $content = preg_replace('/<h(1|2|3)[^>]*>\s*' . preg_quote($item['text'], '/') . '\s*<\/h\1>/ui', "<$level id=\"$id\">$item[text]</$level>", $content, 1);
        }
    }

    if (!empty($toc_items)) {
        $toc_html = '<div class="toc-container-premium"><div class="toc-title-premium">📍 İçindekiler</div><ul class="toc-list-premium">';
        foreach ($toc_items as $item)
            $toc_html .= "<li class=\"$item[class]\"><a href=\"#$item[id]\">$item[text]</a></li>";
        $toc_html .= '</ul></div>';
        $content = $toc_html . $content;
    }

    // FAQ INJECT
    $faq_data = json_decode($post['faq_data'], true);
    if (!empty($faq_data)) {
        $faq_html = '<div class="faq-premium"><h2 style="text-align:center; border:none; margin-top:0 !important;">Sıkça Sorulan Sorular</h2>';
        foreach ($faq_data as $f) {
            $faq_html .= '<div class="faq-item-premium">
                <div class="faq-question-premium"><span>' . $f['q'] . '</span><span style="color:var(--erguvan-pink)">+</span></div>
                <div class="faq-answer-premium">' . nl2br(htmlspecialchars($f['a'])) . '</div>
            </div>';
        }
        $faq_html .= '</div>
        <script>
            document.querySelectorAll(".faq-question-premium").forEach(q => {
                q.onclick = () => q.parentElement.classList.toggle("active");
            });
        </script>';
        $content .= $faq_html;
    }

    $content = $style . $content;
}

injectPremiumDesign($post['content'], $post);
include '../includes/header.php';
?>
<article class="blog-post-wrapper">
    <header style="margin-bottom: 3rem; text-align:center;">
        <nav style="margin-bottom: 2rem;"><a href="<?php echo page_url('blog.php'); ?>"
                style="color:var(--erguvan-pink); text-decoration:none; font-weight:600;">← Blog Listesine Dön</a></nav>
        <span
            style="background:var(--erguvan-light-pink); color:var(--erguvan-pink); padding: 8px 16px; border-radius:30px; font-weight:700; font-size: 0.9rem; text-transform:uppercase; letter-spacing:1px;"><?php echo htmlspecialchars($post['category']); ?></span>
        <h1
            style="font-size: 2.8rem; font-weight: 900; color: #1e293b; margin-top: 1.5rem; line-height: 1.2; font-family: \'Playfair Display\', serif;">
            <?php echo htmlspecialchars($post['title']); ?>
        </h1>
        <div style="margin-top: 1.5rem; color: #64748b; font-weight: 500;">
            <span>⏱ <?php echo htmlspecialchars($post['reading_time']); ?> okuma</span>
            <span style="margin: 0 15px;">•</span>
            <span>📅 <?php echo date('d.m.Y', strtotime($post['created_at'])); ?></span>
        </div>
    </header>

    <div style="margin-bottom: 4rem;">
        <img src="<?php echo webp_url($post['image']); ?>"
            style="width:100%; border-radius:30px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);"
            alt="<?php echo htmlspecialchars($post['title']); ?>">
    </div>

    <div class="blog-post-content">
        <?php echo $post['content']; ?>
    </div>
</article>
<?php include '../includes/footer.php'; ?>