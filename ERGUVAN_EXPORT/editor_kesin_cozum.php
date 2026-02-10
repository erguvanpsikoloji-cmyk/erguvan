<?php
/**
 * ERGUVAN PSİKOLOJİ - EDİTÖR VE TOC FİNAL ÇÖZÜM (V4)
 * Editörün yüklenmeme sorununu giderir ve TOC özelliklerini korur.
 */

$files = [
    // 1. ADMİN BLOG EKLEME (EDİTÖR FİX)
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
        if ($res['success']) $image = $res['url'];
    }
    $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, toc_data, content, image, category, reading_time, keywords, created_at) VALUES (?,?,?,?,?,?,?,?,?,NOW())");
    $stmt->execute([$_POST['title'], $_POST['slug'], $_POST['excerpt'], $_POST['toc_data'], $_POST['content'], $image, $_POST['category'], $_POST['reading_time'], $_POST['keywords']]);
    redirect(admin_url('pages/blog.php'));
}
require_once __DIR__ . '/../includes/header.php';
?>
<!-- Quill Style -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<form method="POST" action="" id="blogForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    <div class="form-group"><label>Başlık *</label><input type="text" id="title" name="title" class="form-control" required></div>
    <div class="form-group"><label>Slug *</label><input type="text" id="slug" name="slug" class="form-control" required></div>
    <div class="form-group"><label>Kategori</label><input type="text" name="category" class="form-control"></div>
    <div class="form-group"><label>Görsel</label><input type="file" name="image" class="form-control"></div>
    <div class="form-group"><label>Okuma Süresi</label><input type="text" name="reading_time" class="form-control" value="5 dk"></div>
    <div class="form-group"><label>Özet</label><textarea name="excerpt" class="form-control" required></textarea></div>

    <style>
        .admin-toc-builder { background: #fff8fb; border: 1px solid #fbcfe8; border-left: 10px solid #ec4899; border-radius: 15px; padding: 20px; margin: 20px 0; }
        .admin-toc-item { display: flex; gap: 10px; margin-bottom: 10px; background: white; padding: 10px; border-radius: 8px; border: 1px solid #eee; align-items: center; }
        .admin-toc-item select { width: 80px; padding: 5px; }
        .admin-toc-item input { flex: 1; padding: 5px; }
    </style>
    <div class="admin-toc-builder">
        <h4>İçindekiler Bölümü</h4>
        <div id="tocContainer"></div>
        <button type="button" onclick="addToc()" class="btn btn-sm" style="background:#ec4899; color:white; border-radius:20px;">+ Başlık Ekle</button>
        <input type="hidden" name="toc_data" id="tocDataHidden">
    </div>

    <div class="form-group">
        <label>İçerik</label>
        <div id="editor-container" style="height: 400px; background:white; border:1px solid #ddd;"></div>
        <textarea name="content" id="content_hidden" style="display:none;"></textarea>
    </div>
    
    <div class="form-group"><label>Anahtar Kelimeler</label><input type="text" name="keywords" class="form-control"></div>
    <button type="submit" class="btn btn-primary">Kaydet</button>
</form>

<!-- Quill Library -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
function addToc(text = '', level = 'h2') {
    const div = document.createElement('div');
    div.className = 'admin-toc-item';
    div.innerHTML = `<select onchange="updateTocData()"><option value="h2" ${level==='h2'?'selected':''}>H2</option><option value="h3" ${level==='h3'?'selected':''}>H3</option></select><input type="text" value="${text}" oninput="updateTocData()"><button type="button" onclick="this.parentElement.remove();updateTocData()">✕</button>`;
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
    const quill = new Quill('#editor-container', { theme: 'snow', modules: { toolbar: [[{header:[1,2,3,false]}], ['bold','italic','link','image'], [{list:'ordered'},{list:'bullet'}]] } });
    document.getElementById('blogForm').onsubmit = () => { document.getElementById('content_hidden').value = quill.root.innerHTML; };
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
EOD
    ,
    // 2. ADMİN BLOG DÜZENLEME (EDİTÖR FİX)
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
    $stmt = $db->prepare("UPDATE blog_posts SET title=?, slug=?, excerpt=?, toc_data=?, content=?, image=?, category=?, reading_time=?, keywords=? WHERE id=?");
    $stmt->execute([$_POST['title'], $_POST['slug'], $_POST['excerpt'], $_POST['toc_data'], $_POST['content'], $image, $_POST['category'], $_POST['reading_time'], $_POST['keywords'], $id]);
    redirect(admin_url('pages/blog.php'));
}
require_once __DIR__ . '/../includes/header.php';
?>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<form method="POST" action="" id="blogForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    <div class="form-group"><label>Başlık</label><input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>"></div>
    <div class="form-group"><label>Slug</label><input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($post['slug']); ?>"></div>
    <div class="form-group"><label>Kategori</label><input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($post['category']); ?>"></div>
    <div class="form-group"><label>Mevcut Görsel</label><br><img src="<?php echo webp_url($post['image']); ?>" style="max-height:100px;"><input type="file" name="image" class="form-control"></div>
    <div class="form-group"><label>Özet</label><textarea name="excerpt" class="form-control"><?php echo htmlspecialchars($post['excerpt']); ?></textarea></div>

    <style>
        .admin-toc-builder { background: #fff8fb; border: 1px solid #fbcfe8; border-left: 10px solid #ec4899; border-radius: 15px; padding: 20px; margin: 20px 0; }
        .admin-toc-item { display: flex; gap: 10px; margin-bottom: 10px; background: white; padding: 10px; border: 1px solid #eee; align-items: center; }
    </style>
    <div class="admin-toc-builder">
        <h4>İçindekiler</h4>
        <div id="tocContainer"></div>
        <button type="button" onclick="addToc()" class="btn btn-sm" style="background:#ec4899; color:white; border-radius:20px;">+ Başlık Ekle</button>
        <input type="hidden" name="toc_data" id="tocDataHidden" value='<?php echo htmlspecialchars($post['toc_data'] ?? ''); ?>'>
    </div>

    <div class="form-group">
        <label>İçerik</label>
        <div id="editor-container" style="height: 400px; background:white; border:1px solid #ddd;"><?php echo $post['content']; ?></div>
        <textarea name="content" id="content_hidden" style="display:none;"></textarea>
    </div>
    
    <div class="form-group"><label>Okuma Süresi</label><input type="text" name="reading_time" class="form-control" value="<?php echo htmlspecialchars($post['reading_time']); ?>"></div>
    <div class="form-group"><label>Anahtar Kelimeler</label><input type="text" name="keywords" class="form-control" value="<?php echo htmlspecialchars($post['keywords']); ?>"></div>
    <button type="submit" class="btn btn-primary">Güncelle</button>
</form>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
function addToc(text = '', level = 'h2') {
    const div = document.createElement('div');
    div.className = 'admin-toc-item';
    div.innerHTML = `<select onchange="updateTocData()"><option value="h2" ${level==='h2'?'selected':''}>H2</option><option value="h3" ${level==='h3'?'selected':''}>H3</option></select><input type="text" value="${text}" oninput="updateTocData()"><button type="button" onclick="this.parentElement.remove();updateTocData()">✕</button>`;
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
    const quill = new Quill('#editor-container', { theme: 'snow', modules: { toolbar: [[{header:[1,2,3,false]}], ['bold','italic','link','image'], [{list:'ordered'},{list:'bullet'}]] } });
    const data = JSON.parse(document.getElementById('tocDataHidden').value || '[]');
    data.forEach(i => addToc(i.text, i.level));
    document.getElementById('blogForm').onsubmit = () => { document.getElementById('content_hidden').value = quill.root.innerHTML; };
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
EOD
];

echo "<h2>Erguvan Editör ve TOC Düzenleyici</h2>";
foreach ($files as $path => $content) {
    if (file_put_contents($path, $content))
        echo "<p style='color:green;'>✓ $path güncellendi.</p>";
}
echo "<h3>İşlem başarılı. Sayfayı yenileyebilirsiniz.</h3>";
?>