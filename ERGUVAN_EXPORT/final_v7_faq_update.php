<?php
/**
 * ERGUVAN PSİKOLOJİ - BLOG FAQ (SSS) GÜNCELLEMESİ (V7)
 * Blog yazılarına SSS (FAQ) ekleme, düzenleme ve görüntüleme özelliği ekler.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/db.php';

echo "<h2>Erguvan Blog SSS (FAQ) Kurulumu</h2>";

// 1. VERİTABANI: faq_data sütununu ekle
try {
    $db = getDB();
    $stmt = $db->query("SHOW COLUMNS FROM blog_posts LIKE 'faq_data'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE blog_posts ADD COLUMN faq_data TEXT AFTER toc_data");
        echo "<p style='color:green;'>✓ 'faq_data' sütunu veritabanına eklendi.</p>";
    } else {
        echo "<p style='color:blue;'>i 'faq_data' sütunu zaten mevcut.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:orange;'>! Veritabanı hatası/notu: " . $e->getMessage() . "</p>";
}

$files = [
    // --- ADMIN BLOG ADD (SSS EKLEMELİ) ---
    'admin/pages/blog-add.php' => <<<'EOD'
<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../../database/db.php';
require_once __DIR__ . '/../includes/upload-handler.php';
require_once __DIR__ . '/../includes/csrf.php';

$db = getDB();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $res = handleImageUpload($_FILES['image'], 'blog');
        if($res['success']) $image = $res['url'];
    }
    $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, meta_description, toc_data, faq_data, content, image, category, reading_time, keywords, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW())");
    $stmt->execute([
        $_POST['title'], $_POST['slug'], $_POST['excerpt'], $_POST['meta_description'], 
        $_POST['toc_data'], $_POST['faq_data'], $_POST['content'], $image, 
        $_POST['category'], $_POST['reading_time'], $_POST['keywords']
    ]);
    redirect(admin_url('pages/blog.php'));
}
require_once __DIR__ . '/../includes/header.php';
?>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<form method="POST" action="" id="blogForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    <div class="form-group"><label>Başlık *</label><input type="text" id="title" name="title" class="form-control" required></div>
    <div class="form-group"><label>Slug *</label><input type="text" id="slug" name="slug" class="form-control" required></div>
    <div class="form-group"><label>Meta Açıklama (SEO)</label><textarea name="meta_description" class="form-control"></textarea></div>
    <div class="form-group"><label>Görsel</label><input type="file" name="image" class="form-control"></div>
    <div class="form-group"><label>Kategori</label><input type="text" name="category" class="form-control"></div>
    <div class="form-group"><label>Okuma Süresi</label><input type="text" name="reading_time" class="form-control" value="5 dk"></div>
    <div class="form-group"><label>Özet</label><textarea name="excerpt" class="form-control" required></textarea></div>

    <!-- TOC BUILDER (İÇİNDEKİLER) -->
    <style>
        .admin-section-box { background: #fff8fb; border: 1px solid #fbcfe8; border-left: 10px solid #ec4899; border-radius: 15px; padding: 20px; margin: 20px 0; }
        .item-row { display: flex; gap: 10px; margin-bottom: 10px; background: white; padding: 10px; border-radius: 8px; border: 1px solid #eee; align-items: center; }
        .faq-item-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 15px; position: relative; }
        .remove-btn { background: #ff4757; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; }
    </style>

    <div class="admin-section-box">
        <h4>İçindekiler Bölümü</h4>
        <div id="tocContainer"></div>
        <button type="button" onclick="addToc()" class="btn btn-sm" style="background:#ec4899; color:white; border-radius:20px;">+ Başlık Ekle</button>
        <input type="hidden" name="toc_data" id="tocDataHidden">
    </div>

    <!-- FAQ BUILDER (SSS) -->
    <div class="admin-section-box" style="border-left-color: #3b82f6; background: #f0f7ff; border-color: #bfdbfe;">
        <h4 style="color: #2563eb;">Sıkça Sorulan Sorular (SSS)</h4>
        <div id="faqContainer"></div>
        <button type="button" onclick="addFaq()" class="btn btn-sm" style="background:#3b82f6; color:white; border-radius:20px;">+ Soru Ekle</button>
        <input type="hidden" name="faq_data" id="faqDataHidden">
    </div>

    <div class="form-group"><label>İçerik</label><div id="editor" style="height:400px; background:white;"></div><textarea name="content" style="display:none;"></textarea></div>
    <div class="form-group"><label>Anahtar Kelimeler</label><input type="text" name="keywords" class="form-control"></div>
    <button type="submit" class="btn btn-primary">Kaydet</button>
</form>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
function addToc(text = '', level = 'h2') {
    const div = document.createElement('div');
    div.className = 'item-row';
    div.innerHTML = `<select onchange="updateToc()"><option value="h1" ${level==='h1'?'selected':''}>H1</option><option value="h2" ${level==='h2'?'selected':''}>H2</option><option value="h3" ${level==='h3'?'selected':''}>H3</option></select><input type="text" value="${text}" placeholder="Başlık..." oninput="updateToc()"><button type="button" class="remove-btn" onclick="this.parentElement.remove();updateToc()">✕</button>`;
    document.getElementById('tocContainer').appendChild(div);
}
function updateToc() {
    const items = [];
    document.querySelectorAll('.item-row').forEach(row => {
        items.push({ level: row.querySelector('select').value, text: row.querySelector('input').value });
    });
    document.getElementById('tocDataHidden').value = JSON.stringify(items);
}

function addFaq(q = '', a = '') {
    const div = document.createElement('div');
    div.className = 'faq-item-box';
    div.innerHTML = `
        <button type="button" class="remove-btn" style="position:absolute; right:10px; top:10px;" onclick="this.parentElement.remove();updateFaq()">✕</button>
        <div style="margin-bottom:10px;"><label>Soru:</label><input type="text" class="form-control faq-q" value="${q}" oninput="updateFaq()"></div>
        <div><label>Cevap:</label><textarea class="form-control faq-a" oninput="updateFaq()">${a}</textarea></div>
    `;
    document.getElementById('faqContainer').appendChild(div);
}
function updateFaq() {
    const items = [];
    document.querySelectorAll('.faq-item-box').forEach(box => {
        const q = box.querySelector('.faq-q').value;
        const a = box.querySelector('.faq-a').value;
        if(q.trim()) items.push({ q, a });
    });
    document.getElementById('faqDataHidden').value = JSON.stringify(items);
}

document.addEventListener('DOMContentLoaded', () => {
    const quill = new Quill('#editor', { theme: 'snow', modules: { toolbar: [[{header:[1,2,3,false]}], ['bold','italic','link','image'], [{list:'ordered'},{list:'bullet'}]] } });
    document.getElementById('title').onblur = function() { document.getElementById('slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-'); };
    document.getElementById('blogForm').onsubmit = () => { document.querySelector('textarea[name="content"]').value = quill.root.innerHTML; };
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
EOD
    ,
    // --- ADMIN BLOG EDIT (SSS EKLEMELİ) ---
    'admin/pages/blog-edit.php' => <<<'EOD'
<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../../database/db.php';
require_once __DIR__ . '/../includes/upload-handler.php';
require_once __DIR__ . '/../includes/csrf.php';

$db = getDB();
$id = (int)($_GET['id'] ?? 0);
$post = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
$post->execute([$id]);
$post = $post->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = $post['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $res = handleImageUpload($_FILES['image'], 'blog');
        if($res['success']) $image = $res['url'];
    }
    $stmt = $db->prepare("UPDATE blog_posts SET title=?, slug=?, excerpt=?, meta_description=?, toc_data=?, faq_data=?, content=?, image=?, category=?, reading_time=?, keywords=? WHERE id=?");
    $stmt->execute([
        $_POST['title'], $_POST['slug'], $_POST['excerpt'], $_POST['meta_description'], 
        $_POST['toc_data'], $_POST['faq_data'], $_POST['content'], $image, 
        $_POST['category'], $_POST['reading_time'], $_POST['keywords'], $id
    ]);
    redirect(admin_url('pages/blog.php'));
}
require_once __DIR__ . '/../includes/header.php';
?>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<form method="POST" action="" id="blogForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    <div class="form-group"><label>Başlık</label><input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>"></div>
    <div class="form-group"><label>Slug</label><input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($post['slug']); ?>"></div>
    <div class="form-group"><label>Meta Açıklama (SEO)</label><textarea name="meta_description" class="form-control"><?php echo htmlspecialchars($post['meta_description'] ?? ''); ?></textarea></div>
    <div class="form-group"><label>Görsel</label><br><img src="<?php echo webp_url($post['image']); ?>" style="max-height:100px; border-radius:8px;"><input type="file" name="image" class="form-control"></div>
    <div class="form-group"><label>Kategori</label><input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($post['category']); ?>"></div>

    <style>
        .admin-section-box { background: #fff8fb; border: 1px solid #fbcfe8; border-left: 10px solid #ec4899; border-radius: 15px; padding: 20px; margin: 20px 0; }
        .item-row { display: flex; gap: 10px; margin-bottom: 10px; background: white; padding: 10px; border-radius: 8px; border: 1px solid #eee; align-items: center; }
        .faq-item-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 15px; position: relative; }
        .remove-btn { background: #ff4757; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; }
    </style>

    <div class="admin-section-box">
        <h4>İçindekiler Bölümü</h4>
        <div id="tocContainer"></div>
        <button type="button" onclick="addToc()" class="btn btn-sm" style="background:#ec4899; color:white; border-radius:20px;">+ Başlık Ekle</button>
        <input type="hidden" name="toc_data" id="tocDataHidden" value='<?php echo htmlspecialchars($post['toc_data'] ?? ''); ?>'>
    </div>

    <div class="admin-section-box" style="border-left-color: #3b82f6; background: #f0f7ff; border-color: #bfdbfe;">
        <h4 style="color: #2563eb;">Sıkça Sorulan Sorular (SSS)</h4>
        <div id="faqContainer"></div>
        <button type="button" onclick="addFaq()" class="btn btn-sm" style="background:#3b82f6; color:white; border-radius:20px;">+ Soru Ekle</button>
        <input type="hidden" name="faq_data" id="faqDataHidden" value='<?php echo htmlspecialchars($post['faq_data'] ?? ''); ?>'>
    </div>

    <div class="form-group"><label>İçerik</label><div id="editor" style="height:400px; background:white;"><?php echo $post['content']; ?></div><textarea name="content" style="display:none;"></textarea></div>
    <div class="form-group"><label>Okuma Süresi</label><input type="text" name="reading_time" class="form-control" value="<?php echo htmlspecialchars($post['reading_time']); ?>"></div>
    <div class="form-group"><label>Anahtar Kelimeler</label><input type="text" name="keywords" class="form-control" value="<?php echo htmlspecialchars($post['keywords']); ?>"></div>
    <button type="submit" class="btn btn-primary">Güncelle</button>
</form>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
function addToc(text = '', level = 'h2') {
    const div = document.createElement('div');
    div.className = 'item-row';
    div.innerHTML = `<select onchange="updateToc()"><option value="h1" ${level==='h1'?'selected':''}>H1</option><option value="h2" ${level==='h2'?'selected':''}>H2</option><option value="h3" ${level==='h3'?'selected':''}>H3</option></select><input type="text" value="${text}" oninput="updateToc()"><button type="button" class="remove-btn" onclick="this.parentElement.remove();updateToc()">✕</button>`;
    document.getElementById('tocContainer').appendChild(div);
}
function updateToc() {
    const items = [];
    document.querySelectorAll('.item-row').forEach(row => {
        items.push({ level: row.querySelector('select').value, text: row.querySelector('input').value });
    });
    document.getElementById('tocDataHidden').value = JSON.stringify(items);
}

function addFaq(q = '', a = '') {
    const div = document.createElement('div');
    div.className = 'faq-item-box';
    div.innerHTML = `
        <button type="button" class="remove-btn" style="position:absolute; right:10px; top:10px;" onclick="this.parentElement.remove();updateFaq()">✕</button>
        <div style="margin-bottom:10px;"><label>Soru:</label><input type="text" class="form-control faq-q" value="${q}" oninput="updateFaq()"></div>
        <div><label>Cevap:</label><textarea class="form-control faq-a" oninput="updateFaq()">${a}</textarea></div>
    `;
    document.getElementById('faqContainer').appendChild(div);
}
function updateFaq() {
    const items = [];
    document.querySelectorAll('.faq-item-box').forEach(box => {
        const q = box.querySelector('.faq-q').value;
        const a = box.querySelector('.faq-a').value;
        if(q.trim()) items.push({ q, a });
    });
    document.getElementById('faqDataHidden').value = JSON.stringify(items);
}

document.addEventListener('DOMContentLoaded', () => {
    const quill = new Quill('#editor', { theme: 'snow', modules: { toolbar: [[{header:[1,2,3,false]}], ['bold','italic','link','image'], [{list:'ordered'},{list:'bullet'}]] } });
    const toc = JSON.parse(document.getElementById('tocDataHidden').value || '[]');
    toc.forEach(t => addToc(t.text, t.level));
    const faq = JSON.parse(document.getElementById('faqDataHidden').value || '[]');
    faq.forEach(f => addFaq(f.q, f.a));
    document.getElementById('blogForm').onsubmit = () => { document.querySelector('textarea[name="content"]').value = quill.root.innerHTML; };
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
EOD
    ,
    // --- BLOG POST (FRONT-END FAQ GÖSTERİMİ) ---
    'pages/blog-post.php' => <<<'EOD'
<?php
require_once __DIR__ . '/../config.php';
require_once '../database/db.php';
$db = getDB();
$slug = $_GET['slug'] ?? '';
$post = $db->prepare("SELECT * FROM blog_posts WHERE slug = ?");
$post->execute([$slug]);
$post = $post->fetch();
if (!$post) redirect(BASE_URL . '/blog');

$page_title = $post['title'];
$page_description = $post['meta_description'] ?: $post['excerpt'];

function injectContent(&$content, $post) {
    // TOC STYLE
    $style = '
    <style>
        .toc-container-fresh { background:#fff8fb; border:1px solid #fbcfe8; border-left:12px solid #ec4899; border-radius:20px; padding:2rem; margin:2rem 0; box-shadow:0 4px 20px rgba(236,72,153,0.08); }
        .toc-title-fresh { color:#ec4899; font-size:1.6rem; font-weight:800; margin-bottom:1.5rem; }
        .toc-list-fresh { list-style:none; padding:0; margin:0; }
        .toc-list-fresh li { position:relative; padding-left:1.8rem; margin-bottom:0.8rem; font-size:1.1rem; font-weight:600; color:#334155; line-height:1.4; }
        .toc-list-fresh li.toc-h3 { padding-left:1.8rem; font-size:1rem; font-weight:500; color:#64748b; }
        .toc-list-fresh li::before { content:"•"; color:#ec4899; font-size:2rem; position:absolute; left:0; top:50%; transform:translateY(-50%); }
        .toc-list-fresh a { color:inherit; text-decoration:none; transition:0.3s; }
        .toc-list-fresh a:hover { color:#ec4899; }
        
        .faq-section { margin-top:3.5rem; border-top:2px solid #f1f5f9; padding-top:3rem; }
        .faq-title { color:#1e293b; font-size:2rem; font-weight:800; margin-bottom:2rem; text-align:center; }
        .faq-item { background:white; border:1px solid #e2e8f0; border-radius:15px; margin-bottom:1rem; overflow:hidden; transition:0.3s; }
        .faq-item:hover { border-color:#ec4899; box-shadow:0 4px 15px rgba(236,72,153,0.05); }
        .faq-question { padding:1.2rem 1.5rem; cursor:pointer; font-weight:700; color:#334155; display:flex; justify-content:space-between; align-items:center; font-size:1.15rem; }
        .faq-answer { padding:0 1.5rem; max-height:0; overflow:hidden; transition:0.4s ease-out; color:#64748b; font-size:1.05rem; line-height:1.6; }
        .faq-item.active .faq-answer { padding-bottom:1.5rem; max-height:500px; }
        .faq-icon { transition:0.3s; color:#ec4899; font-size:1.5rem; }
        .faq-item.active .faq-icon { transform:rotate(45deg); }
        h1,h2,h3 { scroll-margin-top:100px; }
    </style>';

    // TOC INJECT
    $toc_items = [];
    $manual_toc = json_decode($post['toc_data'], true);
    if (!empty($manual_toc)) {
        foreach ($manual_toc as $i => $item) {
            $id = 'section-'.($i+1);
            $level = $item['level'] ?: 'h2';
            $toc_items[] = ['text'=>$item['text'], 'id'=>$id, 'class'=>'toc-'.$level];
            $content = preg_replace('/<h(1|2|3)[^>]*>\s*'.preg_quote($item['text'], '/').'\s*<\/h\1>/ui', "<$level id=\"$id\">$item[text]</$level>", $content, 1);
        }
    }
    
    if (!empty($toc_items)) {
        $toc_html = '<div class="toc-container-fresh"><div class="toc-title-fresh">İçindekiler</div><ul class="toc-list-fresh">';
        foreach ($toc_items as $item) $toc_html .= "<li class=\"$item[class]\"><a href=\"#$item[id]\">$item[text]</a></li>";
        $toc_html .= '</ul></div>';
        $content = $style . $toc_html . $content;
    }

    // FAQ INJECT
    $faq_data = json_decode($post['faq_data'], true);
    if (!empty($faq_data)) {
        $faq_html = '<div class="faq-section"><div class="faq-title">Sıkça Sorulan Sorular</div>';
        foreach ($faq_data as $f) {
            $faq_html .= '<div class="faq-item">
                <div class="faq-question"><span>'.$f['q'].'</span><span class="faq-icon">+</span></div>
                <div class="faq-answer">'.nl2br(htmlspecialchars($f['a'])).'</div>
            </div>';
        }
        $faq_html .= '</div>
        <script>
            document.querySelectorAll(".faq-question").forEach(q => {
                q.onclick = () => q.parentElement.classList.toggle("active");
            });
        </script>';
        $content .= $faq_html;

        // SCHEMA.ORG JSON-LD (SEO için)
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "FAQPage",
            "mainEntity" => []
        ];
        foreach ($faq_data as $f) {
            $schema['mainEntity'][] = [
                "@type" => "Question",
                "name" => $f['q'],
                "acceptedAnswer" => ["@type" => "Answer", "text" => $f['a']]
            ];
        }
        $content .= '<script type="application/ld+json">'.json_encode($schema).'</script>';
    }
}

injectContent($post['content'], $post);
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
EOD
];

foreach ($files as $path => $content) {
    if (file_put_contents($path, $content))
        echo "<p style='color:green;'>✓ <b>$path</b> güncellendi.</p>";
}
echo "<h3>Kurulum tamamlandı! Admin panelinde mavi renkli SSS bölümünü görebilirsiniz.</h3>";
?>