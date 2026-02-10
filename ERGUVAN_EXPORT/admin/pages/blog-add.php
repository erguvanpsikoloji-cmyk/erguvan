<?php
// DEBUG: Script started
// echo "DEBUG: blog-add.php baslatildi"; 
// Hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
        $meta_description = trim($_POST['meta_description'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $reading_time = trim($_POST['reading_time'] ?? '5 dk');
        $keywords = trim($_POST['keywords'] ?? '');
        $image_alt = trim($_POST['image_alt'] ?? '');
        $instagram_share = isset($_POST['instagram_share']) ? 1 : 0;
        $toc_data = $_POST['toc_data'] ?? '';
        $faq_data = $_POST['faq_data'] ?? '';

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
                    $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, meta_description, toc_data, faq_data, content, image, image_alt, category, reading_time, keywords, instagram_share, created_at) 
                                           VALUES (:title, :slug, :excerpt, :meta_description, :toc_data, :faq_data, :content, :image, :image_alt, :category, :reading_time, :keywords, :instagram_share, NOW())");
                    $stmt->execute([
                        ':title' => $title,
                        ':slug' => $slug,
                        ':excerpt' => $excerpt,
                        ':meta_description' => $meta_description,
                        ':toc_data' => $toc_data,
                        ':faq_data' => $faq_data,
                        ':content' => $content,
                        ':image' => $image,
                        ':image_alt' => $image_alt,
                        ':category' => $category,
                        ':reading_time' => $reading_time,
                        ':keywords' => $keywords,
                        ':instagram_share' => $instagram_share
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

// Mevcut kategorileri getir
$existing_categories = $db->query("SELECT DISTINCT category FROM blog_posts ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
$common_categories = ['Anksiyete', 'Depresyon', 'İlişkiler', 'Stres Yönetimi', 'Çocuk Psikolojisi', 'Uyku', 'Kişisel Gelişim', 'Aile Danışmanlığı'];

$page = 'blog';
$page_title = 'Yeni Blog Ekle';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
    /* Professional UI Adjustments for Sena Ceren Admin */
    :root {
        --primary-accent: #ec4899;
        --secondary-accent: #3b82f6;
        --admin-bg: #f8fafc;
        --card-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .editor-container {
        display: grid;
        grid-template-columns: 1.8fr 1fr;
        gap: 25px;
        margin-top: 25px;
    }

    .admin-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: var(--card-shadow);
    }

    .card-title {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
    }

    /* SEO Lights */
    .seo-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .seo-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        padding: 8px;
        background: #f1f5f9;
        border-radius: 8px;
    }

    .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #cbd5e1;
    }

    .dot.green {
        background: #10b981;
        box-shadow: 0 0 8px #10b981;
    }

    .dot.orange {
        background: #f59e0b;
        box-shadow: 0 0 8px #f59e0b;
    }

    .dot.red {
        background: #ef4444;
        box-shadow: 0 0 8px #ef4444;
    }

    /* Buttons */
    .btn-magic {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-magic:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    /* Sticky Footer */
    .form-footer {
        position: sticky;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        padding: 20px;
        border-top: 1px solid #ddd;
        margin: 30px -24px -24px -24px;
        display: flex;
        gap: 15px;
        border-radius: 0 0 16px 16px;
    }

    .toc-item,
    .faq-item {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 15px;
        position: relative;
    }
</style>

<div class="admin-page-header">
    <h2 class="admin-page-title">🚀 Uzm. Psk. Sena Ceren - Akıllı Blog Asistanı</h2>
</div>

<form method="POST" id="blogForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>

    <div class="editor-container">
        <!-- SOL: İçerik Bölümü -->
        <div class="col-left">
            <div class="admin-card">
                <div class="card-title">✍️ İçerik ve Başlık</div>

                <div class="form-group">
                    <label>Başlık *</label>
                    <input type="text" id="title" name="title" class="form-control" required
                        placeholder="Göz alıcı bir başlık yazın...">
                </div>

                <div class="form-group">
                    <label>URL / Slug *</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="slug" name="slug" class="form-control" required placeholder="yazi-linki">
                        <button type="button" class="btn-magic" onclick="generateSlug()">🪄 Oto</button>
                    </div>
                </div>

                <div class="form-group">
                    <label>Özet (Ziyaretçiyi Yakalayan Cümle) *</label>
                    <textarea id="excerpt" name="excerpt" class="form-control" rows="2" maxlength="160"
                        placeholder="Okuyucuyu meraklandıracak bir giriş..."></textarea>
                    <small>Kalan: <span id="charCount">160</span></small>
                </div>

                <div class="form-group">
                    <label>Zengin Metin Editörü *</label>
                    <div id="quill-toolbar">
                        <span class="ql-formats">
                            <select class="ql-header">
                                <option value="2"></option>
                                <option value="3"></option>
                                <option selected></option>
                            </select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-bold"></button>
                            <button class="ql-italic"></button>
                            <button class="ql-link"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-list" value="ordered"></button>
                            <button class="ql-list" value="bullet"></button>
                        </span>
                        <span class="ql-formats">
                            <button type="button" class="btn-magic" onclick="insertBox('info')">ℹ️ Bilgi</button>
                            <button type="button" class="btn-magic" onclick="insertBox('warning')">⚠️ Dikkat</button>
                        </span>
                    </div>
                    <div id="editor"
                        style="min-height: 450px; border-radius: 0 0 10px 10px; border: 1px solid #e2e8f0; border-top:0;">
                    </div>
                    <textarea name="content" id="contentHidden" style="display:none;"></textarea>
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">🧠 Akıllı Tarayıcılar</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label>İçindekiler <button type="button" class="btn-magic" onclick="scanTOC()">🪄
                                Tara</button></label>
                        <div id="tocList" style="margin-top: 10px;"></div>
                        <input type="hidden" name="toc_data" id="tocHidden">
                        <button type="button" class="btn-magic" onclick="addTocManually()">+ Manuel</button>
                    </div>
                    <div>
                        <label>SSS / FAQ <button type="button" class="btn-magic" onclick="scanFAQ()">🪄
                                Tara</button></label>
                        <div id="faqList" style="margin-top: 10px;"></div>
                        <input type="hidden" name="faq_data" id="faqHidden">
                        <button type="button" class="btn-magic" onclick="addFaqManually()">+ Manuel</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SAĞ: SEO & Ayarlar -->
        <div class="col-right">
            <div class="admin-card">
                <div class="card-title">🎯 SEO Analizi</div>
                <div class="seo-list">
                    <div class="seo-item">
                        <div id="light-title" class="dot"></div>
                        <span>Başlık Uzunluğu</span>
                    </div>
                    <div class="seo-item">
                        <div id="light-meta" class="dot"></div>
                        <span>Meta Açıklama</span>
                    </div>
                    <div class="seo-item">
                        <div id="light-content" class="dot"></div>
                        <span>Anahtar Kelime Yoğunluğu</span>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 15px;">
                    <label>Odak Anahtar Kelime</label>
                    <input type="text" id="focus_keyword" class="form-control" placeholder="Örn: sınav kaygısı">
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">🖼️ Görsel ve SEO</div>
                <div class="form-group">
                    <label>Kapak Görseli *</label>
                    <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImg(this)">
                </div>
                <div id="imgPreview" style="display:none; margin-bottom: 10px;">
                    <img id="preview" src="" style="width:100%; border-radius: 10px;">
                </div>
                <div class="form-group">
                    <label>Görsel Alt Yazısı (SEO İçin)</label>
                    <input type="text" name="image_alt" class="form-control" placeholder="Resimde ne görünüyor?">
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">🚀 Yayın Bilgileri</div>
                <div class="form-group">
                    <label>Kategori *</label>
                    <select name="category" class="form-control" required>
                        <option value="">Seçin...</option>
                        <?php foreach ($common_categories as $ca)
                            echo "<option value='$ca'>$ca</option>"; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Okuma Süresi</label>
                    <input type="text" name="reading_time" class="form-control" value="5 dk">
                </div>
                <div class="form-group">
                    <label style="cursor:pointer;"><input type="checkbox" name="instagram_share" value="1"> 📸 Instagram
                        Paneline Düşsün</label>
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">👤 Yazar</div>
                <button type="button" class="btn btn-secondary w-100" onclick="fillSena()">🪄 Sena Hanım Verileriyle
                    Doldur</button>
            </div>
        </div>
    </div>

    <div class="form-footer">
        <button type="submit" class="btn btn-primary" style="background:#ec4899; border:0; padding:12px 25px;">🚀 Yazıyı
            Yayınla</button>
        <button type="button" class="btn btn-secondary" onclick="livePreview()">👁️ Canlı Önizle</button>
        <a href="blog.php" class="btn btn-light">İptal Et</a>
    </div>
</form>

<script>
    let quill;
    document.addEventListener('DOMContentLoaded', function () {
        quill = new Quill('#editor', {
            theme: 'snow',
            modules: { toolbar: '#quill-toolbar' },
            placeholder: 'Anlatmaya başlayın...'
        });

        // Event for SEO
        document.getElementById('title').oninput = runSEO;
        document.getElementById('excerpt').oninput = runSEO;
        document.getElementById('focus_keyword').oninput = runSEO;
        quill.on('text-change', runSEO);
    });

    function generateSlug() {
        const t = document.getElementById('title').value;
        const s = t.toLowerCase()
            .replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ş/g, 's').replace(/ı/g, 'i').replace(/ö/g, 'o').replace(/ç/g, 'c')
            .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = s;
        runSEO();
    }

    function runSEO() {
        const t = document.getElementById('title').value;
        const e = document.getElementById('excerpt').value;
        const k = document.getElementById('focus_keyword').value.toLowerCase();
        const c = quill.root.innerText.toLowerCase();

        document.getElementById('charCount').innerText = 160 - e.length;

        // Title
        const lt = document.getElementById('light-title');
        if (t.length > 50 && t.length < 70) lt.className = 'dot green';
        else if (t.length > 0) lt.className = 'dot orange';
        else lt.className = 'dot red';

        // Meta
        const lm = document.getElementById('light-meta');
        if (e.length > 120 && e.length <= 160) lm.className = 'dot green';
        else if (e.length > 0) lm.className = 'dot orange';
        else lm.className = 'dot red';

        // Keyword
        const lk = document.getElementById('light-content');
        if (k && (t.toLowerCase().includes(k) || c.includes(k))) lk.className = 'dot green';
        else lk.className = 'dot red';
    }

    function scanTOC() {
        const container = document.getElementById('tocList');
        container.innerHTML = '';
        quill.root.querySelectorAll('h2, h3').forEach(h => {
            addTocRow(h.innerText, h.tagName.toLowerCase());
        });
    }

    function addTocRow(text = '', level = 'h2') {
        const div = document.createElement('div');
        div.className = 'toc-item';
        div.innerHTML = `
            <div style="display:flex; gap:10px;">
                <select onchange="saveTOC()" class="form-control-sm"><option value="h2" ${level == 'h2' ? 'selected' : ''}>H2</option><option value="h3" ${level == 'h3' ? 'selected' : ''}>H3</option></select>
                <input type="text" value="${text}" oninput="saveTOC()" class="form-control-sm w-100">
                <button type="button" onclick="this.parentElement.parentElement.remove();saveTOC();" style="color:red;border:0;background:none;">✕</button>
            </div>`;
        document.getElementById('tocList').appendChild(div);
        saveTOC();
    }

    function saveTOC() {
        const items = [];
        document.querySelectorAll('.toc-item').forEach(row => {
            items.push({ level: row.querySelector('select').value, text: row.querySelector('input').value });
        });
        document.getElementById('tocHidden').value = JSON.stringify(items);
    }

    function scanFAQ() {
        const container = document.getElementById('faqList');
        const text = quill.root.innerText;
        const matches = text.match(/[^.!?]+\?/g);
        if (!matches) return alert('İçerikte soru işareti bulunamadı.');
        container.innerHTML = '';
        matches.forEach(q => addFaqRow(q.trim()));
    }

    function addFaqRow(q = '', a = '') {
        const div = document.createElement('div');
        div.className = 'faq-item';
        div.innerHTML = `
            <input type="text" value="${q}" oninput="saveFAQ()" class="form-control-sm w-100 mb-1" placeholder="Soru">
            <textarea oninput="saveFAQ()" class="form-control-sm w-100" placeholder="Cevap">${a}</textarea>
            <button type="button" onclick="this.parentElement.remove();saveFAQ();" style="position:absolute;right:10px;top:5px;color:red;border:0;background:none;">✕</button>`;
        document.getElementById('faqList').appendChild(div);
        saveFAQ();
    }

    function saveFAQ() {
        const items = [];
        document.querySelectorAll('.faq-item').forEach(row => {
            const q = row.querySelector('input').value;
            const a = row.querySelector('textarea').value;
            if (q) items.push({ q, a });
        });
        document.getElementById('faqHidden').value = JSON.stringify(items);
    }

    function insertBox(type) {
        let color = '#3b82f6', icon = 'ℹ️', title = 'BİLGİ';
        if (type === 'warning') { color = '#ef4444'; icon = '⚠️'; title = 'DİKKAT'; }
        const r = quill.getSelection();
        if (r) {
            quill.clipboard.dangerouslyPasteHTML(r.index, `
                <div style="border-left:5px solid ${color}; background:${color}15; padding:20px; margin:15px 0; border-radius:12px;">
                    <strong style="color:${color}; display:block; margin-bottom:10px;">${icon} ${title}</strong>
                    <span>Buraya açıklamanızı yazın...</span>
                </div><p><br></p>`);
        }
    }

    function previewImg(input) {
        if (input.files[0]) {
            const r = new FileReader();
            r.onload = e => { document.getElementById('preview').src = e.target.result; document.getElementById('imgPreview').style.display = 'block'; }
            r.readAsDataURL(input.files[0]);
        }
    }

    function fillSena() {
        const html = `<div style="margin-top:25px; padding:15px; background:#fdf2f8; border-radius:10px; border:1px solid #fbcfe8;">
            <strong>✍️ Uzm. Psk. Sena Ceren Notu:</strong><br>Bu makale mental sağlık farkındalığı oluşturmak adına hazırlanmıştır. Kaynak gösterilmeden alıntılanamaz.</div>`;
        quill.clipboard.dangerouslyPasteHTML(quill.getLength(), html);
    }

    document.getElementById('blogForm').onsubmit = function () {
        document.getElementById('contentHidden').value = quill.root.innerHTML;
        return true;
    };

    function livePreview() {
        const d = {
            title: document.getElementById('title').value,
            content: quill.root.innerHTML,
            excerpt: document.getElementById('excerpt').value,
            category: document.querySelector('select[name="category"]').value,
            toc: document.getElementById('tocHidden').value,
            faq: document.getElementById('faqHidden').value
        };
        localStorage.setItem('preview_data', JSON.stringify(d));
        window.open('blog-preview.php', '_blank');
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>