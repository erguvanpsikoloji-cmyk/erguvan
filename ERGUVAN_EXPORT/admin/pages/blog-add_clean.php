<?php
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
    /* Premium UI Styles */
    :root {
        --primary-pink: #ec4899;
        --secondary-blue: #3b82f6;
        --success-green: #10b981;
        --warning-orange: #f59e0b;
        --error-red: #ef4444;
        --border-color: #e2e8f0;
        --bg-glass: rgba(255, 255, 255, 0.7);
    }

    .blog-editor-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        align-items: start;
        margin-top: 20px;
    }

    .admin-card {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--border-color);
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }

    .card-title {
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #1e293b;
    }

    .seo-indicator {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .status-light {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #cbd5e1;
    }

    .status-light.green {
        background: var(--success-green);
        box-shadow: 0 0 8px var(--success-green);
    }

    .status-light.red {
        background: var(--error-red);
        box-shadow: 0 0 8px var(--error-red);
    }

    .status-light.orange {
        background: var(--warning-orange);
        box-shadow: 0 0 8px var(--warning-orange);
    }

    .toc-item-row,
    .faq-item-row {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 12px;
        position: relative;
    }

    .btn-auto {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-auto:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .special-box-btn {
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid #ddd;
        background: #fff;
        cursor: pointer;
        margin-right: 4px;
        font-size: 12px;
    }

    .form-actions {
        position: sticky;
        bottom: 0;
        background: rgba(255, 255, 255, 0.95);
        padding: 20px;
        border-top: 1px solid #ddd;
        display: flex;
        gap: 10px;
        z-index: 100;
        margin-top: 30px;
        border-radius: 12px 12px 0 0;
        backdrop-filter: blur(8px);
    }
</style>

<?php if ($error): ?>
    <div class="error-message"
        style="background: #fee; border: 1px solid #fcc; color: #c33; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        <strong>❌ Hata:</strong> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<form method="POST" action="" id="blogForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>

    <div class="blog-editor-grid">
        <!-- SOL KOLON: Ana İçerik -->
        <div class="editor-main">
            <div class="admin-card">
                <div class="card-title">✍️ Blog İçeriği</div>

                <div class="form-group">
                    <label for="title">Başlık *</label>
                    <input type="text" id="title" name="title" class="form-control" required
                        placeholder="Göz alıcı bir başlık yazın...">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="slug">Slug (URL) *</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="text" id="slug" name="slug" class="form-control" required
                                placeholder="url-adresi">
                            <button type="button" class="btn-auto" onclick="generateSlug()">🪄 Oto</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="excerpt">Özet (Meta Açıklama) *</label>
                    <textarea id="excerpt" name="excerpt" class="form-control" rows="2" maxlength="160"
                        placeholder="Okuyucuyu meraklandıracak bir giriş cümlesi..."></textarea>
                    <small>Kalan karakter: <span id="excerptCount">160</span></small>
                </div>

                <div class="form-group">
                    <label>İçerik Editörü *</label>
                    <div id="quill-toolbar">
                        <span class="ql-formats">
                            <select class="ql-header">
                                <option value="1"></option>
                                <option value="2"></option>
                                <option value="3"></option>
                                <option selected></option>
                            </select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-bold"></button>
                            <button class="ql-italic"></button>
                            <button class="ql-underline"></button>
                            <button class="ql-link"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-list" value="ordered"></button>
                            <button class="ql-list" value="bullet"></button>
                        </span>
                        <span class="ql-formats">
                            <button type="button" class="special-box-btn" onclick="insertSpecialBox('info')">ℹ️
                                Bilgi</button>
                            <button type="button" class="special-box-btn" onclick="insertSpecialBox('warning')">⚠️
                                Dikkat</button>
                            <button type="button" class="special-box-btn" onclick="insertSpecialBox('tip')">💡
                                İpucu</button>
                        </span>
                    </div>
                    <div id="content-editor"
                        style="min-height: 400px; background: white; border: 1px solid #e2e8f0; border-radius: 0 0 8px 8px;">
                    </div>
                    <textarea id="contentTextarea" name="content" style="display: none;"></textarea>
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">🔍 Otomatik Sistemler</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label>İçindekiler (TOC) <button type="button" class="btn-auto" onclick="autoGenerateTOC()">🪄
                                Yazıdan Tara</button></label>
                        <div id="tocContainer" style="margin-top: 10px;"></div>
                        <input type="hidden" name="toc_data" id="tocDataHidden">
                        <button type="button" class="btn-auto" onclick="addTocItem()">+ Manuel Ekle</button>
                    </div>
                    <div>
                        <label>SSS (FAQ) <button type="button" class="btn-auto" onclick="autoGenerateFAQ()">🪄 Yazıdan
                                Tara</button></label>
                        <div id="faqContainer" style="margin-top: 10px;"></div>
                        <input type="hidden" name="faq_data" id="faqDataHidden">
                        <button type="button" class="btn-auto" onclick="addFaqItem()">+ Manuel Ekle</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SAĞ KOLON: Ayarlar & SEO -->
        <div class="editor-sidebar">
            <div class="admin-card">
                <div class="card-title">🎯 SEO Analizi</div>
                <div id="seo-dashboard">
                    <div class="seo-indicator">
                        <div id="light-title" class="status-light"></div>
                        <span>Başlık Uzunluğu</span>
                    </div>
                    <div class="seo-indicator">
                        <div id="light-meta" class="status-light"></div>
                        <span>Meta Açıklama</span>
                    </div>
                    <div class="seo-indicator">
                        <div id="light-keyword" class="status-light"></div>
                        <span>Odak Kelime Kullanımı</span>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 15px;">
                    <label>Odak Anahtar Kelime</label>
                    <input type="text" id="focus_keyword" class="form-control" placeholder="Örn: sınav kaygısı">
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">📂 Yayınlama Ayarları</div>
                <div class="form-group">
                    <label>Kategori *</label>
                    <select name="category" class="form-control" required id="category">
                        <option value="">Seçin...</option>
                        <?php foreach ($common_categories as $cat): ?>
                            <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                        <?php endforeach; ?>
                        <?php foreach ($existing_categories as $cat): ?>
                            <?php if (!in_array($cat, $common_categories)): ?>
                                <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Okuma Süresi</label>
                    <input type="text" name="reading_time" class="form-control" value="5 dk">
                </div>
                <div class="form-group">
                    <label>Anahtar Kelimeler (Etiketler)</label>
                    <input type="text" name="keywords" id="keywords" class="form-control" placeholder="virgülle ayırın">
                </div>
                <hr>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="instagram_share" value="1">
                        📸 Instagram'da Paylaş
                    </label>
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">🖼️ Görsel & SEO</div>
                <div class="form-group">
                    <label>Görsel Seç *</label>
                    <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                </div>
                <div id="imgPreviewContainer" style="display:none; margin-bottom: 15px;">
                    <img id="imgPreview" src="" style="width:100%; border-radius:8px; border:1px solid #ddd;">
                </div>
                <div class="form-group">
                    <label>Görsel Alt Yazısı (SEO)</label>
                    <input type="text" name="image_alt" class="form-control" placeholder="Görsel neyi anlatıyor?">
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">👤 Yazar Bilgisi</div>
                <button type="button" class="btn btn-secondary w-100" onclick="fillSenaInfo()">🪄 Sena Hanım Verilerini
                    Doldur</button>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"
            style="background: var(--primary-pink); border:none; padding: 12px 30px; font-weight:700;">🚀 Yazıyı
            Yayınla</button>
        <button type="button" class="btn btn-secondary" onclick="previewOnSite()">👁️ Canlı Önizle</button>
        <a href="blog.php" class="btn btn-light">Vazgeç</a>
    </div>
</form>

<!-- Quill.js CSS & JS (Header'da yoksa buraya ekleyelim) -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
    let quill;
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Quill !== 'undefined') {
            quill = new Quill('#content-editor', {
                theme: 'snow',
                modules: {
                    toolbar: '#quill-toolbar'
                },
                placeholder: 'Anlatmaya başlayın...'
            });

            // Event Listeners for SEO and UI
            document.getElementById('title').addEventListener('input', updateSEO);
            document.getElementById('excerpt').addEventListener('input', updateSEO);
            document.getElementById('focus_keyword').addEventListener('input', updateSEO);
            quill.on('text-change', updateSEO);
        }
    });

    // 1. Slug Generator
    function generateSlug() {
        const title = document.getElementById('title').value;
        const slug = title.toLowerCase()
            .replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ş/g, 's')
            .replace(/ı/g, 'i').replace(/ö/g, 'o').replace(/ç/g, 'c')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
        updateSEO();
    }

    // 2. SEO Analysis Engine
    function updateSEO() {
        const title = document.getElementById('title').value;
        const excerpt = document.getElementById('excerpt').value;
        const keyword = document.getElementById('focus_keyword').value.toLowerCase();
        const content = quill ? quill.root.innerText.toLowerCase() : "";

        // Karakter Sayacı
        document.getElementById('excerptCount').innerText = 160 - excerpt.length;

        // Işık Kontrolleri
        // Başlık (50-70 karakter ideal)
        const lightTitle = document.getElementById('light-title');
        if (title.length >= 50 && title.length <= 70) lightTitle.className = 'status-light green';
        else if (title.length > 0) lightTitle.className = 'status-light orange';
        else lightTitle.className = 'status-light red';

        // Meta Açıklama (120-160 karakter ideal)
        const lightMeta = document.getElementById('light-meta');
        if (excerpt.length >= 120 && excerpt.length <= 160) lightMeta.className = 'status-light green';
        else if (excerpt.length > 0) lightMeta.className = 'status-light orange';
        else lightMeta.className = 'status-light red';

        // Anahtar Kelime
        const lightKey = document.getElementById('light-keyword');
        if (keyword && (title.toLowerCase().includes(keyword) || content.includes(keyword))) {
            lightKey.className = 'status-light green';
        } else {
            lightKey.className = 'status-light red';
        }
    }

    // 3. TOC Generator
    function autoGenerateTOC() {
        const container = document.getElementById('tocContainer');
        container.innerHTML = '';
        const headers = quill.root.querySelectorAll('h1, h2, h3');
        if (headers.length === 0) {
            alert('Yazıda H1, H2 veya H3 başlığı bulunamadı!');
            return;
        }
        headers.forEach((h, index) => {
            addTocItem(h.innerText, h.tagName.toLowerCase());
        });
    }

    function addTocItem(text = '', level = 'h2') {
        const div = document.createElement('div');
        div.className = 'toc-item-row';
        div.innerHTML = `
            <div style="display:flex; gap:10px; align-items:center;">
                <select class="form-control" style="width:80px;" onchange="updateTocHidden()">
                    <option value="h1" ${level == 'h1' ? 'selected' : ''}>H1</option>
                    <option value="h2" ${level == 'h2' ? 'selected' : ''}>H2</option>
                    <option value="h3" ${level == 'h3' ? 'selected' : ''}>H3</option>
                </select>
                <input type="text" class="form-control" value="${text}" placeholder="Başlık metni..." oninput="updateTocHidden()">
                <button type="button" class="btn btn-sm" style="color:red;" onclick="this.parentElement.parentElement.remove(); updateTocHidden();">✕</button>
            </div>
        `;
        document.getElementById('tocContainer').appendChild(div);
        updateTocHidden();
    }

    function updateTocHidden() {
        const items = [];
        document.querySelectorAll('.toc-item-row').forEach(row => {
            const level = row.querySelector('select').value;
            const text = row.querySelector('input').value;
            if (text.trim()) items.push({ level, text });
        });
        document.getElementById('tocDataHidden').value = JSON.stringify(items);
    }

    // 4. FAQ Generator
    function autoGenerateFAQ() {
        const container = document.getElementById('faqContainer');
        const text = quill.root.innerText;
        const matches = text.match(/[^.!?]+\?/g);
        if (!matches) {
            alert('Yazıda soru işareti (?) ile biten cümle bulunamadı!');
            return;
        }
        container.innerHTML = '';
        matches.forEach(q => addFaqItem(q.trim()));
    }

    function addFaqItem(q = '', a = '') {
        const div = document.createElement('div');
        div.className = 'faq-item-row';
        div.innerHTML = `
            <input type="text" class="form-control mb-1 faq-q" placeholder="Soru..." value="${q}" oninput="updateFaqHidden()">
            <textarea class="form-control faq-a" placeholder="Cevap..." oninput="updateFaqHidden()" rows="2">${a}</textarea>
            <button type="button" class="btn btn-sm" style="position:absolute; top:5px; right:5px; color:red;" onclick="this.parentElement.remove(); updateFaqHidden();">✕</button>
        `;
        document.getElementById('faqContainer').appendChild(div);
        updateFaqHidden();
    }

    function updateFaqHidden() {
        const items = [];
        document.querySelectorAll('.faq-item-row').forEach(row => {
            const q = row.querySelector('.faq-q').value;
            const a = row.querySelector('.faq-a').value;
            if (q.trim()) items.push({ q, a });
        });
        document.getElementById('faqDataHidden').value = JSON.stringify(items);
    }

    // 5. Special Boxes for Quill
    function insertSpecialBox(type) {
        const range = quill.getSelection();
        if (range) {
            let color = '#3b82f6', icon = 'ℹ️', title = 'BİLGİ';
            if (type === 'warning') { color = '#ef4444'; icon = '⚠️'; title = 'DİKKAT'; }
            if (type === 'tip') { color = '#10b981'; icon = '💡'; title = 'İPUCU'; }

            const html = `
                <div class="content-box" style="border-left: 5px solid ${color}; background: ${color}15; padding: 20px; margin: 20px 0; border-radius: 12px; font-style: normal;">
                    <strong style="color: ${color}; display: block; margin-bottom: 8px; font-size: 1.1em;">${icon} ${title}</strong>
                    <span>Buraya önemli açıklamayı yazın...</span>
                </div>
                <p><br></p>
            `;
            quill.clipboard.dangerouslyPasteHTML(range.index, html);
        }
    }

    // 6. Image Preview
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('imgPreview').src = e.target.result;
                document.getElementById('imgPreviewContainer').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // 7. Fill Sena Info
    function fillSenaInfo() {
        const senaBio = "<div style='margin-top: 30px; padding: 20px; background: #fdf2f8; border-radius: 15px; border: 1px solid #fbcfe8;'><strong>✍️ Yazar Hakkında:</strong><br>Bu içerik <strong>Uzm. Psk. Sena Ceren</strong> tarafından hazırlanmıştır. Kaynak gösterilmeden alıntılanamaz.</div>";
        quill.clipboard.dangerouslyPasteHTML(quill.getLength(), senaBio);
    }

    // 8. Form Submission
    document.getElementById('blogForm').onsubmit = function () {
        document.getElementById('contentTextarea').value = quill.root.innerHTML;
        return true;
    };

    function previewOnSite() {
        const data = {
            title: document.getElementById('title').value,
            content: quill.root.innerHTML,
            excerpt: document.getElementById('excerpt').value,
            category: document.getElementById('category').value,
            toc: document.getElementById('tocDataHidden').value,
            faq: document.getElementById('faqDataHidden').value
        };
        localStorage.setItem('preview_data', JSON.stringify(data));
        window.open('blog-preview.php', '_blank');
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
