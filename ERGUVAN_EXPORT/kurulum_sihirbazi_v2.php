<?php
/**
 * ERGUVAN PSİKOLOJİ - ADMİN TOC KURULUM SİHİRBAZI (V2)
 * Bu script, "0 Byte" yükleme hatasını önlemek için dosyaları sunucu üzerinde oluşturur.
 */

$files = [
    // 1. ADMİN BLOG EKLEME SAYFASI
    'admin/pages/blog-add.php' => <<<'EOD'
<?php
// Session'ı sadece bir kez başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../database/db.php';
require_once __DIR__ . '/../includes/upload-handler.php';

$db = getDB();
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Geçersiz istek! CSRF token doğrulanamadı.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $reading_time = trim($_POST['reading_time'] ?? '5 dk');
        $keywords = trim($_POST['keywords'] ?? '');

        // Görsel yükleme işlemi
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = handleImageUpload($_FILES['image'], 'blog');
            if ($uploadResult['success']) {
                $image = $uploadResult['url'];
            } else {
                $error = $uploadResult['message'];
            }
        }

        // Boş alan kontrolü
        $missing_fields = [];
        if (empty($title))
            $missing_fields[] = 'Başlık';
        if (empty($slug))
            $missing_fields[] = 'Slug';
        if (empty($excerpt))
            $missing_fields[] = 'Özet';
        if (empty($content) || $content === '<p><br></p>')
            $missing_fields[] = 'İçerik';
        if (empty($image))
            $missing_fields[] = 'Görsel';
        if (empty($category))
            $missing_fields[] = 'Kategori';

        if (!empty($missing_fields)) {
            $error = 'Lütfen şu alanları doldurun: ' . implode(', ', $missing_fields);
        } else {
            try {
                // Slug'un benzersiz olup olmadığını kontrol et
                $checkStmt = $db->prepare("SELECT id FROM blog_posts WHERE slug = :slug");
                $checkStmt->execute([':slug' => $slug]);
                if ($checkStmt->fetch()) {
                    $error = 'Bu slug zaten kullanılıyor! Lütfen farklı bir slug girin.';
                } else {
                    $toc_data = $_POST['toc_data'] ?? '';

                    $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, toc_data, content, image, category, reading_time, keywords, created_at) 
                                           VALUES (:title, :slug, :excerpt, :toc_data, :content, :image, :category, :reading_time, :keywords, NOW())");
                    $stmt->execute([
                        ':title' => $title,
                        ':slug' => $slug,
                        ':excerpt' => $excerpt,
                        ':toc_data' => $toc_data,
                        ':content' => $content,
                        ':image' => $image,
                        ':category' => $category,
                        ':reading_time' => $reading_time,
                        ':keywords' => $keywords
                    ]);
                    $success = true;
                    redirect(admin_url('pages/blog.php'));
                }
            } catch (PDOException $e) {
                error_log('Blog ekleme hatası: ' . $e->getMessage());
                $error = 'Veritabanı hatası: ' . $e->getMessage();
            }
        }
    }
}

$existing_categories = $db->query("SELECT DISTINCT category FROM blog_posts ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
$common_categories = ['Anksiyete', 'Depresyon', 'İlişkiler', 'Stres Yönetimi', 'Çocuk Psikolojisi', 'Uyku', 'Kişisel Gelişim', 'Aile Danışmanlığı'];

$page = 'blog';
$page_title = 'Yeni Blog Ekle';
require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($error): ?>
    <div class="error-message" style="background: #fee; border: 1px solid #fcc; color: #c33; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        <strong>❌ Hata:</strong> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<form method="POST" action="" id="blogForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>

    <div class="form-row">
        <div class="form-group" style="flex: 2;">
            <label for="title">Başlık *</label>
            <input type="text" id="title" name="title" class="form-control" required placeholder="Blog yazısının başlığı">
        </div>
        <div class="form-group" style="flex: 1;">
            <label for="slug">Slug (URL) * <button type="button" class="btn-link" onclick="generateSlug()" title="Başlıktan otomatik oluştur">🔄</button></label>
            <input type="text" id="slug" name="slug" class="form-control" required placeholder="url-friendly-slug">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="category">Kategori *</label>
            <select id="category" name="category" class="form-control" required>
                <option value="">Kategori Seçin</option>
                <?php foreach ($common_categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="reading_time">Okuma Süresi</label>
            <input type="text" id="reading_time" name="reading_time" class="form-control" value="5 dk">
        </div>
    </div>

    <div class="form-group">
        <label for="image">Görsel *</label>
        <input type="file" id="image" name="image" class="form-control" accept="image/*" required>
    </div>

    <div class="form-group">
        <label for="excerpt">Özet *</label>
        <textarea id="excerpt" name="excerpt" class="form-control" required rows="3" maxlength="300"></textarea>
    </div>

    <!-- İÇİNDEKİLER (TOC) BUILDER -->
    <style>
        .admin-toc-builder { background-color: #fff8fb; border: 1px solid #fbcfe8; border-left: 12px solid #ec4899; border-radius: 20px; padding: 2rem; margin: 2rem 0; box-shadow: 0 4px 20px rgba(236, 72, 153, 0.06); }
        .admin-toc-title { color: #ec4899; font-size: 1.4rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; }
        .admin-toc-item { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; padding: 10px; background: rgba(255, 255, 255, 0.5); border-radius: 10px; border: 1px dashed #fbcfe8; }
        .admin-toc-item.level-h3 { margin-left: 30px; border-color: #e2e8f0; }
        .admin-toc-item input { flex: 1; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; }
        .admin-toc-item select { width: 100px; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px; }
        .remove-toc-item { background: #ff4757; color: white; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .add-toc-btn { background: #ec4899; color: white; border: none; padding: 10px 20px; border-radius: 30px; font-weight: 600; cursor: pointer; margin-top: 15px; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; }
    </style>

    <div class="form-group">
        <label>İçindekiler Bölümü</label>
        <div class="admin-toc-builder">
            <div class="admin-toc-title"><span>İçindekiler Önizleme</span></div>
            <div id="tocItemsContainer"></div>
            <button type="button" class="add-toc-btn" onclick="addTocItem()"><span>➕</span> Yeni Başlık Ekle</button>
            <input type="hidden" name="toc_data" id="tocDataHidden">
        </div>
    </div>

    <div class="form-group">
        <label for="content">İçerik *</label>
        <div id="content" style="min-height: 400px; background: white; border: 1px solid #e2e8f0; border-radius: 8px;"></div>
        <textarea id="contentTextarea" name="content" style="display: none;"></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Kaydet</button>
        <a href="<?php echo admin_url('pages/blog.php'); ?>" class="btn btn-secondary">❌ İptal</a>
    </div>
</form>

<script>
function generateSlug() {
    const title = document.getElementById('title').value;
    if (!title) return;
    let slug = title.toLowerCase().replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ş/g, 's').replace(/ı/g, 'i').replace(/ö/g, 'o').replace(/ç/g, 'c').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    document.getElementById('slug').value = slug;
}

function addTocItem(text = '', level = 'h2') {
    const container = document.getElementById('tocItemsContainer');
    const itemDiv = document.createElement('div');
    itemDiv.className = `admin-toc-item level-${level}`;
    itemDiv.innerHTML = `<select onchange="updateTocLevel(this)"><option value="h2" ${level==='h2'?'selected':''}>H2</option><option value="h3" ${level==='h3'?'selected':''}>H3</option></select><input type="text" value="${text}" placeholder="Başlık metni..." oninput="updateTocData()"><button type="button" class="remove-toc-item" onclick="this.parentElement.remove();updateTocData()">✕</button>`;
    container.appendChild(itemDiv);
    updateTocData();
}

function updateTocLevel(select) { select.parentElement.className = `admin-toc-item level-${select.value}`; updateTocData(); }

function updateTocData() {
    const items = [];
    document.querySelectorAll('.admin-toc-item').forEach(item => {
        const level = item.querySelector('select').value;
        const text = item.querySelector('input').value;
        if (text.trim()) items.push({ level, text });
    });
    document.getElementById('tocDataHidden').value = JSON.stringify(items);
}

let quillInstance = null;
function initQuill() {
    if (typeof Quill !== 'undefined') {
        quillInstance = new Quill('#content', { theme: 'snow', modules: { toolbar: [[{ 'header': [1, 2, 3, 4, 5, 6, false] }], ['bold', 'italic', 'underline'], [{ 'list': 'ordered' }, { 'list': 'bullet' }], ['link', 'image'], ['clean']] } });
    } else { setTimeout(initQuill, 100); }
}

document.addEventListener('DOMContentLoaded', function () {
    initQuill();
    document.getElementById('blogForm').addEventListener('submit', function (e) {
        if (quillInstance) document.getElementById('contentTextarea').value = quillInstance.root.innerHTML;
    });
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
EOD
    ,
    // 2. ADMİN BLOG DÜZENLEME SAYFASI
    'admin/pages/blog-edit.php' => <<<'EOD'
<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../../database/db.php';
$db = getDB();
$id = (int)($_GET['id'] ?? 0);
$post = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
$post->execute([$id]);
$post = $post->fetch();
if (!$post) redirect(admin_url('pages/blog.php'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $slug = $_POST['slug'];
    $excerpt = $_POST['excerpt'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $toc_data = $_POST['toc_data'] ?? '';
    
    $stmt = $db->prepare("UPDATE blog_posts SET title=?, slug=?, excerpt=?, toc_data=?, content=?, category=?, updated_at=NOW() WHERE id=?");
    $stmt->execute([$title, $slug, $excerpt, $toc_data, $content, $category, $id]);
    redirect(admin_url('pages/blog.php'));
}

require_once __DIR__ . '/../includes/header.php';
?>
<form method="POST" action="" id="blogForm">
    <div class="form-group"><label>Başlık</label><input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($post['title']); ?>"></div>
    <div class="form-group"><label>Slug</label><input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($post['slug']); ?>"></div>
    <div class="form-group"><label>Kategori</label><input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($post['category']); ?>"></div>
    <div class="form-group"><label>Özet</label><textarea name="excerpt" class="form-control"><?php echo htmlspecialchars($post['excerpt']); ?></textarea></div>
    
    <!-- TOC BUILDER -->
    <style>
        .admin-toc-builder { background-color: #fff8fb; border: 1px solid #fbcfe8; border-left: 12px solid #ec4899; border-radius: 20px; padding: 2rem; margin: 2rem 0; }
        .admin-toc-item { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; padding: 10px; background: white; border-radius: 10px; border: 1px dashed #fbcfe8; }
        .admin-toc-item.level-h3 { margin-left: 30px; }
        .admin-toc-item input { flex: 1; padding: 8px; border: 1px solid #ddd; }
    </style>
    <div class="admin-toc-builder">
        <h3>İçindekiler</h3>
        <div id="tocItemsContainer"></div>
        <button type="button" onclick="addTocItem()" class="btn btn-sm btn-outline-primary">+ Ekle</button>
        <input type="hidden" name="toc_data" id="tocDataHidden" value='<?php echo htmlspecialchars($post['toc_data'] ?? ''); ?>'>
    </div>

    <div class="form-group"><label>İçerik</label><div id="content" style="min-height:400px;background:white;"><?php echo $post['content']; ?></div><textarea name="content" style="display:none;"></textarea></div>
    <button type="submit" class="btn btn-primary">Güncelle</button>
</form>

<script>
function addTocItem(text = '', level = 'h2') {
    const container = document.getElementById('tocItemsContainer');
    const div = document.createElement('div');
    div.className = `admin-toc-item level-${level}`;
    div.innerHTML = `<select onchange="this.parentElement.className='admin-toc-item level-'+this.value;updateTocData()"><option value="h2" ${level==='h2'?'selected':''}>H2</option><option value="h3" ${level==='h3'?'selected':''}>H3</option></select><input type="text" value="${text}" oninput="updateTocData()"><button type="button" onclick="this.parentElement.remove();updateTocData()">✕</button>`;
    container.appendChild(div);
}
function updateTocData() {
    const items = [];
    document.querySelectorAll('.admin-toc-item').forEach(item => {
        items.push({ level: item.querySelector('select').value, text: item.querySelector('input').value });
    });
    document.getElementById('tocDataHidden').value = JSON.stringify(items);
}
document.addEventListener('DOMContentLoaded', () => {
    const quill = new Quill('#content', { theme: 'snow' });
    const data = JSON.parse(document.getElementById('tocDataHidden').value || '[]');
    data.forEach(i => addTocItem(i.text, i.level));
    document.getElementById('blogForm').onsubmit = () => { document.querySelector('textarea[name="content"]').value = quill.root.innerHTML; };
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
EOD
    ,
    // 3. BLOG POST GÖSTERİM SAYFASI
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

function injectTOC(&$content, $manual_toc_json = '') {
    $style = '<style>.toc-container-fresh{background:#fff8fb;border:1px solid #fbcfe8;border-left:12px solid #ec4899;border-radius:20px;padding:2rem;margin:2rem 0;}.toc-title-fresh{color:#ec4899;font-size:1.5rem;font-weight:800;margin-bottom:1rem;}.toc-list-fresh{list-style:none;padding:0;}.toc-list-fresh li{position:relative;padding-left:1.5rem;margin-bottom:.5rem;font-weight:600;}.toc-list-fresh li.toc-h3{padding-left:3rem;font-weight:400;color:#666;}.toc-list-fresh li::before{content:"•";color:#ec4899;position:absolute;left:0;} h2,h3{scroll-margin-top:100px;}</style>';
    if (strpos($content, 'toc-container') !== false) return;
    $toc_items = [];
    $manual_data = json_decode($manual_toc_json, true);
    if (!empty($manual_data)) {
        foreach ($manual_data as $i => $item) {
            $id = 'section-'.($i+1);
            $toc_items[] = ['text'=>$item['text'], 'id'=>$id, 'class'=>($item['level']=='h3'?'toc-h3':'toc-h2')];
            $content = preg_replace('/<h(2|3)[^>]*>\s*'.preg_quote($item['text'], '/').'\s*<\/h\1>/ui', "<h$1 id=\"$id\">$item[text]</h$1>", $content, 1);
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
<article class="container my-5">
    <h1><?php echo $post['title']; ?></h1>
    <img src="<?php echo webp_url($post['image']); ?>" class="img-fluid rounded mb-4">
    <div class="blog-content"><?php echo $post['content']; ?></div>
</article>
<?php include '../includes/footer.php'; ?>
EOD
];

echo "<h2>Erguvan Kurulum Sihirbazı</h2>";

// 4. VERİTABANI GÜNCELLEME SORGUSU
try {
    require_once __DIR__ . '/database/db.php';
    $db = getDB();
    $db->exec("ALTER TABLE blog_posts ADD COLUMN IF NOT EXISTS toc_data TEXT AFTER excerpt");
    echo "<p style='color:green;'>✓ Veritabanı sütunu kontrol edildi/eklendi.</p>";
} catch (Exception $e) {
    echo "<p style='color:orange;'>! Veritabanı notu: " . $e->getMessage() . "</p>";
}

// DOSYALARI OLUŞTUR
foreach ($files as $path => $content) {
    $dir = dirname($path);
    if (!is_dir($dir))
        mkdir($dir, 0755, true);

    if (file_put_contents($path, $content)) {
        echo "<p style='color:green;'>✓ Dosya oluşturuldu: <b>$path</b></p>";
    } else {
        echo "<p style='color:red;'>❌ Hata: <b>$path</b> oluşturulamadı!</p>";
    }
}

echo "<h3>Kurulum tamamlandı!</h3>";
echo "<p>Artık admin paneline girip İçindekiler'i yönetebilirsiniz.</p>";
echo "<p style='color:red;'>GÜVENLİK İÇİN BU DOSYAYI SİLMEYİ UNUTMAYIN.</p>";
?>