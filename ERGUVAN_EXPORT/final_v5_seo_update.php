<?php
/**
 * ERGUVAN PSİKOLOJİ - SEO VE HİZALAMA GÜNCELLEMESİ (V5)
 * H1 Desteği, Meta Description ve Kayma Sorunu Çözümü
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/db.php';

echo "<h2>Erguvan SEO ve Görsel Düzenleme Sihirbazı</h2>";

// 1. VERİTABANI: meta_description sütununu ekle
try {
    $db = getDB();
    $stmt = $db->query("SHOW COLUMNS FROM blog_posts LIKE 'meta_description'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE blog_posts ADD COLUMN meta_description TEXT AFTER excerpt");
        echo "<p style='color:green;'>✓ 'meta_description' sütunu veritabanına eklendi.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:orange;'>! Veritabanı notu: " . $e->getMessage() . "</p>";
}

$files = [
    // --- ADMIN BLOG ADD ---
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
    $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, meta_description, toc_data, content, image, category, reading_time, keywords, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,NOW())");
    $stmt->execute([$_POST['title'], $_POST['slug'], $_POST['excerpt'], $_POST['meta_description'], $_POST['toc_data'], $_POST['content'], $image, $_POST['category'], $_POST['reading_time'], $_POST['keywords']]);
    redirect(admin_url('pages/blog.php'));
}
require_once __DIR__ . '/../includes/header.php';
?>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<form method="POST" action="" id="blogForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    <div class="form-group"><label>Başlık *</label><input type="text" id="title" name="title" class="form-control" required></div>
    <div class="form-group"><label>Slug *</label><input type="text" id="slug" name="slug" class="form-control" required></div>
    <div class="form-group"><label>Meta Açıklama (SEO)</label><textarea name="meta_description" class="form-control" placeholder="Google arama sonuçlarında görünecek kısa açıklama..."></textarea></div>
    <div class="form-group"><label>Kategori</label><input type="text" name="category" class="form-control"></div>
    <div class="form-group"><label>Görsel</label><input type="file" name="image" class="form-control"></div>
    <div class="form-group"><label>Okuma Süresi</label><input type="text" name="reading_time" class="form-control" value="5 dk"></div>
    <div class="form-group"><label>Özet</label><textarea name="excerpt" class="form-control" required></textarea></div>

    <style>
        .admin-toc-builder { background: #fff8fb; border: 1px solid #fbcfe8; border-left: 10px solid #ec4899; border-radius: 15px; padding: 20px; margin: 20px 0; }
        .admin-toc-item { display: flex; gap: 10px; margin-bottom: 10px; background: white; padding: 10px; border: 1px solid #eee; align-items: center; border-radius:8px; }
        .admin-toc-item select { width: 80px; padding: 5px; }
        .admin-toc-item input { flex: 1; padding: 5px; }
    </style>
    <div class="admin-toc-builder">
        <h4>İçindekiler Bölümü (H1, H2, H3)</h4>
        <div id="tocContainer"></div>
        <button type="button" onclick="addToc()" class="btn btn-sm" style="background:#ec4899; color:white; border-radius:20px;">+ Başlık Ekle</button>
        <input type="hidden" name="toc_data" id="tocDataHidden">
    </div>

    <div class="form-group"><label>İçerik</label><div id="editor" style="height:400px; background:white;"></div><textarea name="content" style="display:none;"></textarea></div>
    <div class="form-group"><label>Anahtar Kelimeler</label><input type="text" name="keywords" class="form-control"></div>
    <button type="submit" class="btn btn-primary">Kaydet</button>
</form>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
function addToc(text = '', level = 'h2') {
    const div = document.createElement('div');
    div.className = 'admin-toc-item';
    div.innerHTML = `<select onchange="updateTocData()"><option value="h1" ${level==='h1'?'selected':''}>H1</option><option value="h2" ${level==='h2'?'selected':''}>H2</option><option value="h3" ${level==='h3'?'selected':''}>H3</option></select><input type="text" value="${text}" placeholder="Başlık metni..." oninput="updateTocData()"><button type="button" onclick="this.parentElement.remove();updateTocData()" style="background:#ff4757; color:white; border:none; border-radius:50%; width:25px; height:25px; cursor:pointer;">✕</button>`;
    document.getElementById('tocContainer').appendChild(div);
}
function updateTocData() {
    const items = [];
    document.querySelectorAll('.admin-toc-item').forEach(item => {
        items.push({ level: item.querySelector('select').value, text: item.querySelector('input').value });
    });
    document.getElementById('tocDataHidden').value = JSON.stringify(items);
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
    // --- ADMIN BLOG EDIT ---
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
if (!$post) redirect(admin_url('pages/blog.php'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = $post['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $res = handleImageUpload($_FILES['image'], 'blog');
        if($res['success']) $image = $res['url'];
    }
    $stmt = $db->prepare("UPDATE blog_posts SET title=?, slug=?, excerpt=?, meta_description=?, toc_data=?, content=?, image=?, category=?, reading_time=?, keywords=? WHERE id=?");
    $stmt->execute([$_POST['title'], $_POST['slug'], $_POST['excerpt'], $_POST['meta_description'], $_POST['toc_data'], $_POST['content'], $image, $_POST['category'], $_POST['reading_time'], $_POST['keywords'], $id]);
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
    <div class="form-group"><label>Kategori</label><input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($post['category']); ?>"></div>
    <div class="form-group"><label>Görsel</label><br><img src="<?php echo webp_url($post['image']); ?>" style="max-height:100px; border-radius:8px;"><input type="file" name="image" class="form-control"></div>
    <div class="form-group"><label>Özet</label><textarea name="excerpt" class="form-control"><?php echo htmlspecialchars($post['excerpt']); ?></textarea></div>

    <style>
        .admin-toc-builder { background: #fff8fb; border: 1px solid #fbcfe8; border-left: 10px solid #ec4899; border-radius: 15px; padding: 20px; margin: 20px 0; }
        .admin-toc-item { display: flex; gap: 10px; margin-bottom: 10px; background: white; padding: 10px; border: 1px solid #eee; align-items: center; border-radius:8px; }
    </style>
    <div class="admin-toc-builder">
        <h4>İçindekiler (H1, H2, H3 Hizalı)</h4>
        <div id="tocContainer"></div>
        <button type="button" onclick="addToc()" class="btn btn-sm" style="background:#ec4899; color:white; border-radius:20px;">+ Başlık Ekle</button>
        <input type="hidden" name="toc_data" id="tocDataHidden" value='<?php echo htmlspecialchars($post['toc_data'] ?? ''); ?>'>
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
    div.className = 'admin-toc-item';
    div.innerHTML = `<select onchange="updateTocData()"><option value="h1" ${level==='h1'?'selected':''}>H1</option><option value="h2" ${level==='h2'?'selected':''}>H2</option><option value="h3" ${level==='h3'?'selected':''}>H3</option></select><input type="text" value="${text}" oninput="updateTocData()"><button type="button" onclick="this.parentElement.remove();updateTocData()" style="background:#ff4757; color:white; border:none; border-radius:50%; width:25px; height:25px; cursor:pointer;">✕</button>`;
    document.getElementById('tocContainer').appendChild(div);
}
function updateTocData() {
    const items = [];
    document.querySelectorAll('.admin-toc-item').forEach(item => {
        items.push({ level: item.querySelector('select').value, text: item.querySelector('input').value });
    });
    document.getElementById('tocDataHidden').value = JSON.stringify(items);
}
document.addEventListener('DOMContentLoaded', () => {
    const quill = new Quill('#editor', { theme: 'snow', modules: { toolbar: [[{header:[1,2,3,false]}], ['bold','italic','link','image'], [{list:'ordered'},{list:'bullet'}]] } });
    const data = JSON.parse(document.getElementById('tocDataHidden').value || '[]');
    data.forEach(i => addToc(i.text, i.level));
    document.getElementById('blogForm').onsubmit = () => { document.querySelector('textarea[name="content"]').value = quill.root.innerHTML; };
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
EOD
    ,
    // --- BLOG POST (FRONT-END ALIGNMENT FIX) ---
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

// HTML Title ve Meta Description Ayarı
$page_title = $post['title'];
$page_description = $post['meta_description'] ?: $post['excerpt'];

function injectTOC(&$content, $manual_toc_json = '') {
    $style = '
    <style id="erguvan-toc-style">
        .toc-container-fresh {
            background-color: #fff8fb !important;
            border: 1px solid #fbcfe8 !important;
            border-left: 12px solid #ec4899 !important;
            border-radius: 20px !important;
            padding: 2.5rem !important;
            margin: 2.5rem 0 !important;
            box-shadow: 0 4px 20px rgba(236,72,153,0.08) !important;
        }
        .toc-title-fresh { color: #ec4899 !important; font-size: 1.6rem !important; font-weight: 800 !important; margin-bottom: 1.5rem !important; }
        .toc-list-fresh { list-style: none !important; padding: 0 !important; margin: 0 !important; }
        .toc-list-fresh li {
            position: relative !important;
            padding-left: 2rem !important;
            margin-bottom: 0.8rem !important;
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            color: #334155 !important;
            line-height: 1.4 !important;
        }
        .toc-list-fresh li.toc-h3 { padding-left: 3.5rem !important; font-size: 1rem !important; font-weight: 500; color: #64748b !important; }
        .toc-list-fresh li::before {
            content: "•" !important;
            color: #ec4899 !important;
            font-size: 2rem !important;
            position: absolute !important;
            left: 0.2rem !important;
            top: 50% !important;
            transform: translateY(-55%) !important; /* NOKTA KAYMASINI ÖNLEYEN KRİTİK AYAR */
            line-height: 1 !important;
        }
        .toc-list-fresh li.toc-h3::before { left: 1.8rem !important; font-size: 1.5rem !important; opacity: 0.7; }
        .toc-list-fresh a { color: inherit !important; text-decoration: none !important; transition: color 0.3s; }
        .toc-list-fresh a:hover { color: #ec4899 !important; }
        h1, h2, h3 { scroll-margin-top: 100px !important; }
    </style>';
    
    if (strpos($content, 'toc-container') !== false) return;
    $toc_items = [];
    $manual_data = json_decode($manual_toc_json, true);
    if (!empty($manual_data)) {
        foreach ($manual_data as $i => $item) {
            $id = 'section-'.($i+1);
            $level = $item['level'] ?: 'h2';
            $toc_items[] = ['text'=>$item['text'], 'id'=>$id, 'class'=>'toc-'.$level];
            $content = preg_replace('/<h(1|2|3)[^>]*>\s*'.preg_quote($item['text'], '/').'\s*<\/h\1>/ui', "<$level id=\"$id\">$item[text]</$level>", $content, 1);
        }
    }
    if (empty($toc_items)) return;
    $toc = '<div class="toc-container-fresh"><div class="toc-title-fresh">İçindekiler</div><ul class="toc-list-fresh">';
    foreach ($toc_items as $item) $toc .= "<li class=\"$item[class]\"><a href=\"#$item[id]\">$item[text]</a></li>";
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
    <div class="blog-post-image"><img src="<?php echo webp_url($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>"></div>
    <div class="blog-post-content"><div class="container"><?php echo $post['content']; ?></div></div>
</article>
<?php include '../includes/footer.php'; ?>
EOD
];

foreach ($files as $path => $content) {
    if (file_put_contents($path, $content))
        echo "<p style='color:green;'>✓ <b>$path</b> güncellendi.</p>";
    else
        echo "<p style='color:red;'>❌ <b>$path</b> güncellenemedi!</p>";
}
echo "<h3>Tüm güncellemeler tamamlandı. Meta Description ve H1 seçenekleri aktif!</h3>";
?>