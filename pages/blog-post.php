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

if (!function_exists('slugify')) {
    function slugify($text)
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }
}

function injectPremiumDesign(&$content, $post)
{
    // PREMIUM CSS
    $style = '
    <style id="erguvan-premium-design">
        :root {
            --accent-color: var(--secondary);
            --accent-soft: rgba(139, 61, 72, 0.05);
            --text-dark: var(--text-dark);
            --text-muted: var(--text-medium);
        }

        /* GENEL BLOG DÜZENİ */
        .blog-post-wrapper {
            max-width: 850px;
            margin: 0 auto;
            padding: 0 20px;
            font-family: var(--font-body);
        }

        .blog-post-content {
            font-size: 1.15rem !important;
            line-height: 1.8 !important;
            color: var(--text-dark) !important;
        }

        .blog-post-content p {
            margin-bottom: 2rem !important;
        }

        /* BAŞLIK STİLLERİ */
        .blog-post-content h2 {
            font-size: 2rem !important;
            font-weight: 800 !important;
            color: var(--text-dark) !important;
            margin: 3.5rem 0 1.5rem 0 !important;
            line-height: 1.3 !important;
            border-left: 6px solid var(--accent-color);
            padding-left: 15px;
            font-family: var(--font-heading);
        }

        .blog-post-content h3 {
            font-size: 1.6rem !important;
            font-weight: 700 !important;
            color: var(--text-dark) !important;
            margin: 2.5rem 0 1.2rem 0 !important;
            font-family: var(--font-heading);
        }

        /* İÇİNDEKİLER (TOC) */
        .toc-container-premium {
            background: var(--bg-soft);
            border: 1px solid rgba(139, 61, 72, 0.1);
            border-left: 12px solid var(--accent-color);
            border-radius: 24px;
            padding: 1.5rem 2.5rem;
            margin: 3rem 0;
            box-shadow: 0 10px 30px rgba(29, 45, 80, 0.05);
            transition: all 0.3s;
        }

        .toc-title-premium {
            color: var(--accent-color);
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            font-family: var(--font-heading);
            user-select: none;
        }

        .toc-list-premium {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: all 0.4s ease-out;
        }

        .toc-container-premium.active .toc-list-premium {
            max-height: 1000px; /* Yeterince büyük bir değer */
            opacity: 1;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(139, 61, 72, 0.1);
        }

        .toc-toggle-icon {
            font-size: 1.2rem;
            transition: transform 0.3s;
        }

        .toc-container-premium.active .toc-toggle-icon {
            transform: rotate(180deg);
        }

        .toc-list-premium li {
            position: relative;
            padding-left: 2.2rem;
            margin-bottom: 14px;
            transition: all 0.2s;
            line-height: 1.4;
            display: flex;
            align-items: flex-start;
        }

        .toc-list-premium li.toc-h1 { font-weight: 800; color: var(--text-dark); font-size: 1.15rem; }
        .toc-list-premium li.toc-h2 { font-weight: 700; color: var(--text-dark); font-size: 1.1rem; }
        .toc-list-premium li.toc-h3 { font-weight: 500; color: var(--text-muted); font-size: 1rem; margin-left: 1rem; }

        .toc-list-premium li::before {
            content: "•";
            color: var(--accent-color);
            font-size: 2rem;
            position: absolute;
            left: 0;
            top: -4px;
            line-height: 1;
        }

        .toc-list-premium a { color: inherit; text-decoration: none; }
        .toc-list-premium a:hover { color: var(--accent-color); padding-left: 5px; }

        /* SSS / FAQ STİLLERİ */
        .faq-premium {
            margin-top: 5rem;
            background: var(--bg-soft);
            border-radius: 30px;
            padding: 3rem;
        }

        .faq-item-premium {
            background: white;
            border-radius: 18px;
            margin-bottom: 1rem;
            border: 1px solid rgba(29, 45, 80, 0.1);
            transition: all 0.3s;
            overflow: hidden;
        }

        .faq-question-premium {
            padding: 1.5rem;
            cursor: pointer;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-dark);
            background: #fff;
            transition: background 0.3s;
        }

        .faq-question-premium:hover {
            background: #fdfdfd;
        }
        
        .faq-item-premium.active .faq-question-premium {
            background: rgba(139, 61, 72, 0.03);
            color: var(--accent-color);
        }

        .faq-toggle-icon {
            font-size: 1.5rem;
            line-height: 1;
            color: var(--accent-color);
            transition: transform 0.3s;
        }

        .faq-item-premium.active .faq-toggle-icon {
            transform: rotate(45deg); /* + işaretini x yapar */
        }

        .faq-answer-premium {
            padding: 0 1.5rem;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease-out;
            color: var(--text-muted);
            line-height: 1.7;
            opacity: 0;
        }

        .faq-item-premium.active .faq-answer-premium {
            padding: 1.5rem;
            padding-top: 0;
            max-height: 500px;
            opacity: 1;
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
        $toc_html = '<div class="toc-container-premium" id="tocContainer">
            <div class="toc-title-premium" onclick="toggleTOC()">
                <span>📍 İçindekiler</span>
                <span class="toc-toggle-icon">▼</span>
            </div>
            <ul class="toc-list-premium">';
        foreach ($toc_items as $item)
            $toc_html .= "<li class=\"$item[class]\"><a href=\"#$item[id]\">$item[text]</a></li>";
        $toc_html .= '</ul></div>';
        $content = $toc_html . $content;
    }

    // FAQ INJECT
    $faq_data = json_decode($post['faq_data'], true);
    if (!empty($faq_data)) {
        $faq_html = '<div class="faq-premium"><h2 style="text-align:center; border:none; margin-top:0 !important; margin-bottom:2rem !important;">Sıkça Sorulan Sorular</h2>';
        foreach ($faq_data as $i => $f) {
            $faq_html .= '<div class="faq-item-premium" id="faq-item-' . $i . '">
                <div class="faq-question-premium" onclick="toggleFAQ(' . $i . ')">
                    <span>' . $f['q'] . '</span>
                    <span class="faq-toggle-icon">+</span>
                </div>
                <div class="faq-answer-premium">' . nl2br(htmlspecialchars($f['a'])) . '</div>
            </div>';
        }
        $faq_html .= '</div>
        <script>
            // TOC Toggle
            function toggleTOC() {
                document.getElementById("tocContainer").classList.toggle("active");
            }

            // FAQ Accordion
            function toggleFAQ(index) {
                const clickedItem = document.getElementById("faq-item-" + index);
                const isActive = clickedItem.classList.contains("active");
                
                // Close all items
                document.querySelectorAll(".faq-item-premium").forEach(item => {
                    item.classList.remove("active");
                });
                
                // If it wasn"t active before, open it
                if (!isActive) {
                    clickedItem.classList.add("active");
                }
            }
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

    <?php if (!empty($post['tags'])): ?>
        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #eee;">
            <h4
                style="font-size: 1.2rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem; font-family:var(--font-heading); display:flex; align-items:center; gap:10px;">
                <span style="color:var(--erguvan-pink);">#</span> Konu Etiketleri
            </h4>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <?php
                $tags = explode(',', $post['tags']);
                foreach ($tags as $tag):
                    $tag = trim($tag);
                    if (!$tag)
                        continue;
                    ?>
                    <a href="<?php echo url('etiket/' . slugify($tag)); ?>"
                        style="background: white; border: 1px solid #e2e8f0; color: #64748b; padding: 8px 20px; border-radius: 50px; text-decoration: none; font-size: 0.9rem; transition: all 0.2s; font-weight:500; display:inline-block;">
                        <?php echo htmlspecialchars($tag); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <style>
                a[href*="etiket"]:hover {
                    border-color: var(--erguvan-pink) !important;
                    color: var(--erguvan-pink) !important;
                    background: var(--erguvan-light-pink) !important;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(139, 61, 72, 0.1);
                }
            </style>
        </div>
    <?php endif; ?>
</article>
<?php include '../includes/footer.php'; ?>