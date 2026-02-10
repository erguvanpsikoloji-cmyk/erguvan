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

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    header("Location: " . admin_url('pages/blog.php'));
    exit;
}

// Blog yazısını getir
$stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    header("Location: " . admin_url('pages/blog.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            throw new Exception('Geçersiz istek! CSRF token doğrulanamadı.');
        }

        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $reading_time = ''; // Okuma süresi kaldırıldı
        $keywords = trim($_POST['keywords'] ?? '');
        $image_alt = trim($_POST['image_alt'] ?? '');
        $instagram_share = isset($_POST['instagram_share']) ? 1 : 0;
        $meta_title = trim($_POST['meta_title'] ?? '');
        $meta_description = trim($_POST['meta_description'] ?? '');
        $tags = trim($_POST['tags'] ?? '');
        $toc_data = $_POST['toc_data'] ?? '';
        $faq_data = $_POST['faq_data'] ?? '';

        // Görsel işleme
        $image = $_POST['current_image'] ?? '';

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = handleImageUpload($_FILES['image'], 'blog');
            if ($uploadResult['success']) {
                $image = $uploadResult['url'];
            } else {
                throw new Exception('Görsel yükleme hatası: ' . $uploadResult['message']);
            }
        }

        if (empty($title) || empty($slug) || empty($excerpt) || empty($content) || empty($image) || empty($category)) {
            throw new Exception('Lütfen tüm yıldızlı (*) alanları doldurun!');
        }

        // Auto-Canonical Logic
        $canonical_url = trim($_POST['canonical_url'] ?? '');
        if (empty($canonical_url) && !empty($slug)) {
            $canonical_url = url('blog/' . $slug);
        }

        $stmt = $db->prepare("UPDATE blog_posts SET 
                           title = :title, 
                           slug = :slug, 
                           excerpt = :excerpt, 
                           toc_data = :toc_data,
                           faq_data = :faq_data,
                           content = :content, 
                           image = :image, 
                           image_alt = :image_alt,
                           category = :category, 
                           reading_time = :reading_time,
                           keywords = :keywords,
                           meta_title = :meta_title,
                           meta_description = :meta_description,
                           tags = :tags,
                           instagram_share = :instagram_share,
                           canonical_url = :canonical_url,
                           og_title = :og_title,
                           og_description = :og_description,
                           schema_type = :schema_type,
                           updated_at = NOW()
                           WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':slug' => $slug,
            ':excerpt' => $excerpt,
            ':toc_data' => $toc_data,
            ':faq_data' => $faq_data,
            ':content' => $content,
            ':image' => $image,
            ':image_alt' => $image_alt,
            ':category' => $category,
            ':reading_time' => $reading_time,
            ':keywords' => $keywords,
            ':meta_title' => $meta_title,
            ':meta_description' => $meta_description,
            ':tags' => $tags,
            ':instagram_share' => $instagram_share,
            ':canonical_url' => $canonical_url,
            ':og_title' => $_POST['og_title'] ?? null,
            ':og_description' => $_POST['og_description'] ?? null,
            ':schema_type' => $_POST['schema_type'] ?? 'BlogPosting',
            ':id' => $id
        ]);

        $success = true;
        // Güncel veriyi tekrar çekelim
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch();

    } catch (\Throwable $e) {
        $error = '<strong>Hata:</strong> ' . $e->getMessage() . '<br>';
        $error .= '<small>Dosya: ' . $e->getFile() . ' (' . $e->getLine() . ')</small>';
        error_log('Blog Edit Error: ' . $e->getMessage());
    }
}

$common_categories = ['Anksiyete', 'Depresyon', 'İlişkiler', 'Stres Yönetimi', 'Çocuk Psikolojisi', 'Uyku', 'Kişisel Gelişim', 'Aile Danışmanlığı'];
$page = 'blog';
$page_title = 'Blog Yazısını Düzenle';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
    /* Premium UI Styles Sync with blog-add.php */
    :root {
        --primary-pink: #ec4899;
        --primary-hover: #db2777;
        --secondary-blue: #3b82f6;
        --bg-light: #f8fafc;
        --card-border: #e2e8f0;
        --text-main: #1e293b;
        --text-muted: #64748b;
    }

    .admin-page-header {
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .admin-page-title {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .editor-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 30px;
        margin-bottom: 100px;
        align-items: start;
    }

    .admin-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid var(--card-border);
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .card-title {
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.2rem;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--text-main);
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--card-border);
        border-radius: 12px;
        font-size: 15px;
        transition: all 0.3s;
        background: var(--bg-light);
    }

    .form-control:focus {
        border-color: var(--primary-pink);
        background: #fff;
        outline: none;
        box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.1);
    }

    /* SEO Dots */
    .seo-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        background: var(--bg-light);
        padding: 12px;
        border-radius: 12px;
    }

    .dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #cbd5e1;
        flex-shrink: 0;
    }

    .dot.green {
        background: #10b981;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
    }

    .dot.orange {
        background: #f59e0b;
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.4);
    }

    .dot.red {
        background: #ef4444;
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.4);
    }

    /* Sticky Footer */
    .sticky-footer {
        position: fixed;
        bottom: 0;
        left: 260px;
        right: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        padding: 20px 40px;
        border-top: 1px solid var(--card-border);
        display: flex;
        align-items: center;
        gap: 20px;
        z-index: 1000;
    }

    .btn-pink {
        background: var(--primary-pink);
        color: #fff;
        padding: 14px 40px;
        border-radius: 14px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
    }

    .btn-pink:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(236, 72, 153, 0.3);
    }

    .btn-secondary {
        background: #64748b;
        color: #fff;
        padding: 14px 25px;
        border-radius: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-magic {
        background: #fff;
        border: 1px solid var(--card-border);
        color: var(--text-main);
        padding: 8px 15px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-link {
        color: var(--primary-pink);
        font-weight: 600;
        text-decoration: none;
    }

    /* Scanners */
    .scanner-panel {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }

    .scanner-box {
        background: #fff;
        border: 1px solid var(--card-border);
        border-radius: 15px;
        padding: 20px;
    }

    .scanner-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .toc-item,
    .faq-item {
        background: var(--bg-light);
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 10px;
        position: relative;
    }

    @media (max-width: 1024px) {
        .editor-grid {
            grid-template-columns: 1fr;
        }

        .sticky-footer {
            left: 0;
        }
    }
</style>

<div class="admin-page-header">
    <h2 class="admin-page-title">🚀 Blog Yazısını Düzenle</h2>
    <div style="color: var(--text-muted);">ID: #<?php echo $post['id']; ?></div>
</div>

<?php if ($success): ?>
    <div style="background: #10b981; color: #fff; padding: 15px; border-radius: 12px; margin-bottom:30px;">
        ✨ <strong>Başarılı!</strong> Değişiklikler başarıyla kaydedildi.
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div style="background: #ef4444; color: #fff; padding: 15px; border-radius: 12px; margin-bottom:30px;">
        ⚠️ <strong>Hata:</strong> <?php echo $error; ?>
    </div>
<?php endif; ?>

<form method="POST" id="blogForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>

    <div class="editor-grid">
        <!-- SOL: Ana İçerik -->
        <div class="editor-main">
            <div class="admin-card">
                <div class="card-title">✍️ İçerik ve Başlık</div>

                <div class="form-group">
                    <label>Başlık *</label>
                    <input type="text" id="title" name="title" class="form-control" required
                        value="<?php echo htmlspecialchars($post['title']); ?>">
                </div>

                <div class="form-group">
                    <label>URL / Slug *</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="slug" name="slug" class="form-control" required
                            value="<?php echo htmlspecialchars($post['slug']); ?>">
                        <button type="button" class="btn-magic" onclick="generateSlug()">🪄 Yenile</button>
                    </div>
                </div>

                <div class="form-group">
                    <label>Özet (Ziyaretçiyi Yakalayan Cümle) *</label>
                    <textarea id="excerpt" name="excerpt" class="form-control" rows="3"
                        maxlength="160"><?php echo htmlspecialchars($post['excerpt']); ?></textarea>
                    <div style="text-align: right; margin-top: 5px;">
                        <small style="color: var(--text-muted);">Kalan: <span id="charCount">160</span></small>
                    </div>
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
                        style="min-height: 500px; border-radius: 0 0 16px 16px; border: 2px solid var(--card-border); border-top: 0;">
                        <?php echo $post['content']; ?>
                    </div>
                    <textarea name="content" id="contentHidden" style="display:none;"></textarea>
                </div>
            </div>

            <!-- Akıllı Tarayıcılar -->
            <div class="admin-card">
                <div class="card-title">🧠 Akıllı Tarayıcılar</div>
                <div class="scanner-panel">
                    <div class="scanner-box">
                        <div class="scanner-header">
                            <label style="margin:0;">İçindekiler</label>
                            <button type="button" class="btn-magic" onclick="scanTOC()">🪄 Tara</button>
                        </div>
                        <div id="tocList"></div>
                        <button type="button" class="btn-magic" style="width:100%;" onclick="addTocManually()">+ Manuel
                            Ekle</button>
                        <input type="hidden" name="toc_data" id="tocHidden"
                            value='<?php echo htmlspecialchars($post['toc_data'] ?? ''); ?>'>
                    </div>
                    <div class="scanner-box">
                        <div class="scanner-header">
                            <label style="margin:0;">SSS / FAQ</label>
                            <button type="button" class="btn-magic" onclick="scanFAQ()">🪄 Tara</button>
                        </div>
                        <div id="faqList"></div>
                        <button type="button" class="btn-magic" style="width:100%;" onclick="addFaqManually()">+ Manuel
                            Ekle</button>
                        <input type="hidden" name="faq_data" id="faqHidden"
                            value='<?php echo htmlspecialchars($post['faq_data'] ?? ''); ?>'>
                    </div>
                </div>
            </div>
        </div>

        <!-- SAĞ: SEO & Ayarlar -->
        <div class="editor-sidebar">
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
                <div class="form-group" style="margin-top: 20px;">
                    <label>Odak Anahtar Kelime</label>
                    <input type="text" id="focus_keyword" name="keywords" class="form-control"
                        value="<?php echo htmlspecialchars($post['keywords']); ?>">
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-title">🔍 Arama Motoru (SERP) Görünümü</div>
            <div class="form-group">
                <label>SEO Başlığı (Meta Title)</label>
                <input type="text" id="meta_title" name="meta_title" class="form-control" maxlength="60"
                    value="<?php echo htmlspecialchars($post['meta_title'] ?? ''); ?>"
                    placeholder="Boş bırakılırsa yazı başlığı kullanılır">
                <div style="display:flex; justify-content:space-between; margin-top:5px;">
                    <small style="color:#666">Google'da görünecek mavi başlık.</small>
                    <small><span id="metaTitleCount">0</span>/60</small>
                </div>
            </div>
            <div class="form-group">
                <label>SEO Açıklaması (Meta Description)</label>
                <textarea id="meta_description" name="meta_description" class="form-control" rows="3" maxlength="160"
                    placeholder="Boş bırakılırsa özet kullanılır"><?php echo htmlspecialchars($post['meta_description'] ?? ''); ?></textarea>
                <div style="display:flex; justify-content:space-between; margin-top:5px;">
                    <small style="color:#666">Google'da başlığın altında çıkan gri yazı.</small>
                    <small><span id="metaDescCount">0</span>/160</small>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-title">🖼️ Görsel ve SEO</div>
            <div class="form-group">
                <label>Kapak Görseli</label>
                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImg(this)">
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($post['image']); ?>">
            </div>
            <div id="imgPreview" style="margin-bottom: 15px;">
                <img id="preview" src="<?php echo webp_url($post['image']); ?>"
                    style="width:100%; border-radius: 12px; border: 1px solid var(--card-border);">
            </div>
            <div class="form-group">
                <label>Görsel Alt Yazısı (SEO)</label>
                <input type="text" name="image_alt" class="form-control"
                    value="<?php echo htmlspecialchars($post['image_alt'] ?? ''); ?>">
            </div>
        </div>

        <div class="admin-card">
            <div class="card-title">🏷️ Etiketler & Ayarlar</div>
            <div class="form-group">
                <label>Kategori *</label>
                <select name="category" class="form-control" required>
                    <option value="">Seçin...</option>
                    <?php foreach ($common_categories as $ca): ?>
                        <option value="<?php echo $ca; ?>" <?php echo $post['category'] === $ca ? 'selected' : ''; ?>>
                            <?php echo $ca; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Etiketler (Tags)</label>
                <input type="text" name="tags" class="form-control"
                    value="<?php echo htmlspecialchars($post['tags'] ?? ''); ?>"
                    placeholder="Örn: depresyon, anksiyete, terapi (Virgülle ayırın)">
                <small style="color:#666">Her etiket için otomatik sayfa oluşturulur.</small>
            </div>
            <!-- Okuma süresi input kaldırıldı -->
            <input type="hidden" name="reading_time" value="<?php echo htmlspecialchars($post['reading_time']); ?>">
            <div class="form-group">
                <label style="cursor:pointer;"><input type="checkbox" name="instagram_share" value="1" <?php echo ($post['instagram_share'] ?? 0) ? 'checked' : ''; ?>> 📸 Instagram
                    Paneline Düşsün</label>
            </div>
            <div class="form-group" style="margin-top:15px; border-top:1px solid #eee; padding-top:10px;">
                <label>Canonical URL (İsteğe Bağlı)</label>
                <input type="url" name="canonical_url"
                    value="<?php echo htmlspecialchars($post['canonical_url'] ?? ''); ?>" class="form-control"
                    placeholder="https://...">
            </div>
        </div>

        <div class="admin-card">
            <div class="card-title">👤 Yazar Notu Ekle</div>
            <div style="display:grid; gap:10px;">
                <button type="button" class="btn btn-secondary" style="width:100%; justify-content:center;"
                    onclick="fillSena()">👩‍⚕️ Uzm. Psk. Sena Ceren</button>
                <button type="button" class="btn btn-secondary"
                    style="width:100%; justify-content:center; background:#3b82f6; color:white;"
                    onclick="fillSedat()">👨‍⚕️ Uzm. Psk. Sedat Parmaksız</button>
            </div>
        </div>
    </div>
    </div>

    <!-- Sticky Footer -->
    <div class="sticky-footer">
        <button type="submit" class="btn-pink">💾 Değişiklikleri Kaydet</button>
        <button type="button" class="btn-secondary" onclick="livePreview()">🏠 Canlı Önizle</button>
        <a href="blog.php" class="btn-link" style="margin-left: auto;">İptal Et</a>
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

        // Load existing data for scanners
        loadScanners();
        runSEO();

        document.getElementById('title').oninput = runSEO;
        document.getElementById('excerpt').oninput = runSEO;
        document.getElementById('focus_keyword').oninput = runSEO;

        // Counter Events
        document.getElementById('meta_title').addEventListener('input', function () {
            document.getElementById('metaTitleCount').innerText = this.value.length;
        });
        document.getElementById('meta_description').addEventListener('input', function () {
            document.getElementById('metaDescCount').innerText = this.value.length;
        });

        // Init counters
        document.getElementById('metaTitleCount').innerText = document.getElementById('meta_title').value.length;
        document.getElementById('metaDescCount').innerText = document.getElementById('meta_description').value.length;

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

        // Content Length
        const wordCount = c.split(/\s+/).length;

        // Advanced Checks
        const slug = document.getElementById('slug').value;
        const setLight = (id, status) => { document.getElementById(id).className = 'dot ' + status; }

        if (t.length >= 40 && t.length <= 60) setLight('light-title', 'green');
        else if (t.length > 0) setLight('light-title', 'orange');
        else setLight('light-title', 'red');

        if (e.length >= 120 && e.length <= 160) setLight('light-meta', 'green');
        else if (e.length > 0) setLight('light-meta', 'orange');
        else setLight('light-meta', 'red');

        const lk = document.getElementById('light-content');
        if (!k) {
            lk.className = 'dot red';
        } else {
            let score = 0;
            if (t.toLowerCase().includes(k)) score++;
            if (slug.includes(k.replace(/ /g, '-'))) score++;
            if (c.includes(k)) score++;
            if (wordCount > 300) score++;

            if (score >= 3) lk.className = 'dot green';
            else if (score >= 1) lk.className = 'dot orange';
            else lk.className = 'dot red';
        }
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
                <select onchange="saveTOC()" class="form-control" style="width:80px; padding:5px;"><option value="h2" ${level == 'h2' ? 'selected' : ''}>H2</option><option value="h3" ${level == 'h3' ? 'selected' : ''}>H3</option></select>
                <input type="text" value="${text}" oninput="saveTOC()" class="form-control" style="padding:5px;">
                <button type="button" onclick="this.parentElement.parentElement.remove();saveTOC();" style="color:#ef4444; border:0; background:none; font-size:1.2rem; cursor:pointer;">✕</button>
            </div>`;
        document.getElementById('tocList').appendChild(div);
        saveTOC();
    }

    function saveTOC() {
        const items = [];
        document.querySelectorAll('.toc-item').forEach(row => {
            const level = row.querySelector('select').value;
            const text = row.querySelector('input').value;
            if (text) items.push({ level, text });
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
            <input type="text" value="${q}" oninput="saveFAQ()" class="form-control mb-1" style="padding:5px;" placeholder="Soru">
            <textarea oninput="saveFAQ()" class="form-control" style="padding:5px; height:60px;" placeholder="Cevap">${a}</textarea>
            <button type="button" onclick="this.parentElement.remove();saveFAQ();" style="position:absolute; right:12px; top:8px; color:#ef4444; border:0; background:none; cursor:pointer;">✕</button>`;
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

    function loadScanners() {
        try {
            const toc = JSON.parse(document.getElementById('tocHidden').value || '[]');
            toc.forEach(i => addTocRow(i.text, i.level));
            const faq = JSON.parse(document.getElementById('faqHidden').value || '[]');
            faq.forEach(i => addFaqRow(i.q, i.a));
        } catch (e) { }
    }

    function insertBox(type) {
        let color = '#3b82f6', icon = 'ℹ️', title = 'BİLGİ';
        if (type === 'warning') { color = '#ef4444'; icon = '⚠️'; title = 'DİKKAT'; }
        const r = quill.getSelection();
        const index = r ? r.index : quill.getLength();
        quill.clipboard.dangerouslyPasteHTML(index, `
            <div style="border-left:5px solid ${color}; background:${color}15; padding:20px; margin:15px 0; border-radius:12px;">
                <strong style="color:${color}; display:block; margin-bottom:10px;">${icon} ${title}</strong>
                <span>Buraya açıklamanızı yazın...</span>
            </div><p><br></p>`);
    }

    function previewImg(input) {
        if (input.files[0]) {
            const r = new FileReader();
            r.onload = e => {
                document.getElementById('preview').src = e.target.result;
            }
            r.readAsDataURL(input.files[0]);
        }
    }

    function fillSena() {
        const html = `<div style="margin-top:25px; padding:20px; background:#fdf2f8; border-radius:15px; border:1px solid #fbcfe8;">
            <strong style="color:var(--primary-pink); display:block; margin-bottom:10px;">✍️ Uzm. Psk. Sena Ceren Notu:</strong>
            <p style="margin:0;">Bu makale ruh sağlığı farkındalığı oluşturmak amacıyla hazırlanmıştır. Her bireyin deneyimi özeldir; profesyonel destek almak için randevu oluşturabilirsiniz.</p>
        </div><p><br></p>`;
        quill.clipboard.dangerouslyPasteHTML(quill.getLength(), html);
    }

    function fillSedat() {
        const html = `<div style="margin-top:25px; padding:20px; background:#eff6ff; border-radius:15px; border:1px solid #bfdbfe;">
            <strong style="color:#2563eb; display:block; margin-bottom:10px;">✍️ Uzm. Psk. Sedat Parmaksız Notu:</strong>
            <p style="margin:0;">Bu içerik bilgilendirme amaçlıdır ve profesyonel tıbbi tavsiye yerine geçmez. Detaylı bilgi ve randevu için iletişime geçebilirsiniz.</p>
        </div><p><br></p>`;
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