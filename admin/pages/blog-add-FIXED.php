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
    // 1. GÖRSEL BOYUTU KONTROLÜ (KRİTİK)
    if (empty($_POST) && empty($_FILES)) {
        $error = '<strong>Hata:</strong> Yüklenen dosya çok büyük! Lütfen görsel boyutunu küçültün (Örn: Max 2MB).';
    } else {
        try {
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Geçersiz istek! CSRF token doğrulanamadı.');
            }

            $title = trim($_POST['title'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $excerpt = trim($_POST['excerpt'] ?? '');
            $meta_description = trim($_POST['meta_description'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $reading_time = '';
            $keywords = trim($_POST['keywords'] ?? '');
            $image_alt = trim($_POST['image_alt'] ?? '');
            $instagram_share = isset($_POST['instagram_share']) ? 1 : 0;
            $tags = trim($_POST['tags'] ?? '');
            $toc_data = $_POST['toc_data'] ?? '';
            $faq_data = $_POST['faq_data'] ?? '';

            // Auto-Canonical Logic
            $canonical_url = trim($_POST['canonical_url'] ?? '');
            if (empty($canonical_url) && !empty($slug)) {
                $canonical_url = url('blog/' . $slug);
            }

            // Görsel yükleme işlemi
            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = handleImageUpload($_FILES['image'], 'blog');
                if ($uploadResult['success']) {
                    $image = $uploadResult['url'];
                } else {
                    throw new Exception('Görsel yükleme hatası: ' . $uploadResult['message']);
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
                throw new Exception('Lütfen şu alanları doldurun: ' . implode(', ', $missing_fields));
            }

            // Slug kontrolü
            $checkStmt = $db->prepare("SELECT id FROM blog_posts WHERE slug = :slug");
            $checkStmt->execute([':slug' => $slug]);
            if ($checkStmt->fetch()) {
                throw new Exception('Bu slug zaten kullanılıyor! Lütfen farklı bir slug girin.');
            }

            $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, meta_description, meta_title, tags, toc_data, faq_data, content, image, image_alt, category, reading_time, keywords, instagram_share, canonical_url, og_title, og_description, schema_type, created_at) 
                                   VALUES (:title, :slug, :excerpt, :meta_description, :meta_title, :tags, :toc_data, :faq_data, :content, :image, :image_alt, :category, :reading_time, :keywords, :instagram_share, :canonical_url, :og_title, :og_description, :schema_type, NOW())");
            $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':excerpt' => $excerpt,
                ':meta_description' => $meta_description,
                ':meta_title' => $_POST['meta_title'] ?? null,
                ':tags' => $tags,
                ':toc_data' => $toc_data,
                ':faq_data' => $faq_data,
                ':content' => $content,
                ':image' => $image,
                ':image_alt' => $image_alt,
                ':category' => $category,
                ':reading_time' => $reading_time,
                ':keywords' => $keywords,
                ':instagram_share' => $instagram_share,
                ':canonical_url' => $canonical_url,
                ':og_title' => $_POST['og_title'] ?? null,
                ':og_description' => $_POST['og_description'] ?? null,
                ':schema_type' => $_POST['schema_type'] ?? 'BlogPosting'
            ]);

            $success = true;
            redirect(admin_url('pages/blog.php'));

        } catch (\Throwable $e) {
            $error = '<strong>Hata:</strong> ' . $e->getMessage() . '<br>';
            $error .= '<small>Dosya: ' . $e->getFile() . ' (' . $e->getLine() . ')</small>';
            error_log('Blog Error: ' . $e->getMessage());
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
    /* Minified Admin Styles */
    :root { --primary-accent: #ec4899; --secondary-accent: #3b82f6; --admin-bg: #f8fafc; --card-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .editor-container { display: grid; grid-template-columns: 1.8fr 1fr; gap: 20px; margin-top: 20px; }
    .admin-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 20px; margin-bottom: 20px; box-shadow: var(--card-shadow); }
    .card-title { font-weight: 700; color: #1e293b; margin-bottom: 15px; font-size: 1.1rem; }
    .form-footer { position: sticky; bottom: 0; background: rgba(255,255,255,0.9); backdrop-filter: blur(5px); padding: 15px; border-top: 1px solid #ddd; margin: 20px -20px -20px; display: flex; gap: 10px; border-radius: 0 0 12px 12px; }
    @media (max-width: 768px) { .editor-container { grid-template-columns: 1fr; } }
</style>

<div class="admin-page-header">
    <h2 class="admin-page-title">🚀 Uzm. Psk. Sena Ceren - Akıllı Blog Asistanı</h2>
</div>

<?php if ($success): ?>
    <div style="background: #10b981; color: #fff; padding: 15px; border-radius: 12px; margin-bottom:30px;">
        ✨ <strong>Başarılı!</strong> Yazı başarıyla yayınlandı.
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div style="background: #ef4444; color: #fff; padding: 15px; border-radius: 12px; margin-bottom:30px;">
        ⚠️ <?php echo $error; ?>
    </div>
<?php endif; ?>

<form method="POST" id="blogForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>

    <div class="editor-container">
        <!-- SOL: İçerik Bölümü -->
        <div class="col-left">
            <div class="admin-card">
                <div class="card-title">✍️ İçerik ve Başlık</div>

                <div class="form-group">
                    <label class="form-label">Blog Başlığı</label>
                    <input type="text" name="title" class="form-control" required 
                           value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">SEO URL (Slug)</label>
                    <div class="input-group">
                        <input type="text" name="slug" class="form-control" required
                               value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>">
                        <button type="button" class="btn btn-secondary" onclick="generateSlug()">
                            <i class="fas fa-sync"></i> Otomatik Oluştur
                        </button>
                    </div>
                    <small class="form-text text-muted">Boş bırakırsanız başlıktan otomatik oluşturulur.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-control" required>
                        <option value="">Seçiniz...</option>
                        <?php
                        $categories = ['Psikoloji', 'Çocuk ve Ergen', 'Yetişkin Terapisi', 'Çift Terapisi', 'Kurumsal'];
                        $selected_category = $_POST['category'] ?? '';
                        foreach ($categories as $cat) {
                            $selected = ($selected_category === $cat) ? 'selected' : '';
                            echo "<option value=\"$cat\" $selected>$cat</option>";
                        }
                        ?>
                    </select>
                </div>
                
                 <!-- YENİ ALAN: Etiketler (Tags) -->
                <div class="form-group mt-3">
                    <label class="form-label">Etiketler (Virgülle ayırın)</label>
                    <input type="text" name="tags" class="form-control" placeholder="örnek: anksiyete, depresyon, terapi"
                        value="<?php echo htmlspecialchars($_POST['tags'] ?? ''); ?>">
                    <small class="text-muted">Yazı ile ilgili anahtar kelimeleri virgülle ayırarak yazın.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Öne Çıkan Görsel</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="form-text text-muted">Max 2MB. JPG, PNG veya WEBP.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Görsel Alt Metni (SEO)</label>
                    <input type="text" name="image_alt" class="form-control" placeholder="Görseli tanımlayan kısa metin"
                           value="<?php echo htmlspecialchars($_POST['image_alt'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Kısa Özet (Meta Description)</label>
                    <textarea name="excerpt" class="form-control" rows="3" required maxlength="300"><?php echo htmlspecialchars($_POST['excerpt'] ?? ''); ?></textarea>
                    <small class="form-text text-muted">Ana sayfada ve arama sonuçlarında görünecek kısa açıklama.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">İçerik</label>
                    <textarea name="content" id="editor"><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                </div>

                <!-- YENİ SEO ALANLARI -->
                <div class="card mt-4 mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-search"></i> Gelişmiş SEO Ayarları</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">SEO Başlığı (Meta Title)</label>
                            <input type="text" name="meta_title" class="form-control" placeholder="Tarayıcı sekmesinde görünecek başlık"
                                value="<?php echo htmlspecialchars($_POST['meta_title'] ?? ''); ?>">
                            <small class="text-muted">Boş bırakılırsa Blog Başlığı kullanılır.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">SEO Açıklaması (Meta Description)</label>
                            <textarea name="meta_description" class="form-control" rows="2" placeholder="Google arama sonuçlarında çıkacak açıklama"><?php echo htmlspecialchars($_POST['meta_description'] ?? ''); ?></textarea>
                            <small class="text-muted">Boş bırakılırsa Kısa Özet kullanılır.</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Anahtar Kelimeler (Keywords)</label>
                            <input type="text" name="keywords" class="form-control" placeholder="virgülle ayırın: psikolog, istanbul, terapi"
                                value="<?php echo htmlspecialchars($_POST['keywords'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Canonical URL</label>
                            <div class="input-group">
                                <input type="text" name="canonical_url" id="canonical_url" class="form-control" placeholder="https://erguvanpsikoloji.com/blog/..."
                                    value="<?php echo htmlspecialchars($_POST['canonical_url'] ?? ''); ?>">
                                <button type="button" class="btn btn-outline-secondary" onclick="autoCanonical()">
                                    Otomatik Doldur
                                </button>
                            </div>
                            <small class="text-muted">Bu yazının orijinal adresi. Boş bırakırsanız otomatik oluşturulur.</small>
                        </div>

                        <div class="form-check mt-3">
                            <input type="checkbox" name="instagram_share" class="form-check-input" id="instaCheck" 
                                   <?php echo (isset($_POST['instagram_share']) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="instaCheck">
                                Instagram'da Paylaşım İçin İşaretle
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Gizli alanlar -->
                <input type="hidden" name="toc_data" id="toc_data">
                <input type="hidden" name="faq_data" id="faq_data">
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
                    <input type="text" id="focus_keyword" name="keywords" class="form-control"
                        placeholder="Örn: sınav kaygısı">
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">🔍 Arama Motoru (SERP) Görünümü</div>
                <div class="form-group">
                    <label>SEO Başlığı (Meta Title)</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-control" maxlength="60"
                        placeholder="Boş bırakılırsa yazı başlığı kullanılır">
                    <div style="display:flex; justify-content:space-between; margin-top:5px;">
                        <small style="color:#666">Google'da görünecek mavi başlık.</small>
                        <small><span id="metaTitleCount">0</span>/60</small>
                    </div>
                </div>
                <div class="form-group">
                    <label>SEO Açıklaması (Meta Description)</label>
                    <textarea id="meta_description" name="meta_description" class="form-control" rows="3"
                        maxlength="160" placeholder="Boş bırakılırsa özet kullanılır"></textarea>
                    <div style="display:flex; justify-content:space-between; margin-top:5px;">
                        <small style="color:#666">Google'da başlığın altında çıkan gri yazı.</small>
                        <small><span id="metaDescCount">0</span>/160</small>
                    </div>
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
                <div class="card-title">🏷️ Etiketler & Ayarlar</div>
                <div class="form-group">
                    <label>Kategori *</label>
                    <select name="category" class="form-control" required>
                        <option value="">Seçin...</option>
                        <?php foreach ($common_categories as $ca): ?>
                            <option value="<?php echo $ca; ?>"><?php echo $ca; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Etiketler (Tags)</label>
                    <input type="text" name="tags" class="form-control"
                        placeholder="Örn: depresyon, anksiyete, terapi (Virgülle ayırın)">
                    <small style="color:#666">Her etiket için otomatik sayfa oluşturulur.</small>
                </div>
                <!-- Okuma süresi kaldırıldı -->
                <div class="form-group">
                    <label style="cursor:pointer;"><input type="checkbox" name="instagram_share" value="1"> 📸 Instagram
                        Paneline Düşsün</label>
                </div>
                <div class="form-group" style="margin-top:15px; border-top:1px solid #eee; padding-top:10px;">
                    <label>Canonical URL (İsteğe Bağlı)</label>
                    <input type="url" name="canonical_url" class="form-control" placeholder="https://...">
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">👤 Yazar Notu Ekle</div>
                <div style="display:grid; gap:10px;">
                    <button type="button" class="btn btn-secondary w-100" onclick="fillSena()">👩‍⚕️ Uzm. Psk. Sena
                        Ceren</button>
                    <button type="button" class="btn btn-secondary w-100" style="background:#3b82f6; color:white;"
                        onclick="fillSedat()">👨‍⚕️ Uzm. Psk. Sedat Parmaksız</button>
                </div>
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

        // Counter Events
        document.getElementById('meta_title').addEventListener('input', function () {
            document.getElementById('metaTitleCount').innerText = this.value.length;
        });
        document.getElementById('meta_description').addEventListener('input', function () {
            document.getElementById('metaDescCount').innerText = this.value.length;
        });

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

        // Update Light Status Helper
        const setLight = (id, status) => {
            const el = document.getElementById(id);
            el.className = 'dot ' + status;
        }

        // 1. Title Check
        if (t.length >= 40 && t.length <= 60) setLight('light-title', 'green');
        else if (t.length > 0) setLight('light-title', 'orange');
        else setLight('light-title', 'red');

        // 2. Meta Check
        if (e.length >= 120 && e.length <= 160) setLight('light-meta', 'green');
        else if (e.length > 0) setLight('light-meta', 'orange');
        else setLight('light-meta', 'red');

        // 3. Keyword Check (In Title, Slug, Content, First Paragraph)
        const lk = document.getElementById('light-content');
        if (!k) {
            lk.className = 'dot red';
            // Reset additional indicators if needed
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
            <strong>✍️ Uzm. Psk. Sena Ceren Notu:</strong><br>Bu makale mental sağlık farkındalığı oluşturmak adına hazırlanmıştır. Kaynak gösterilmeden alıntılanamaz.</div><p><br></p>`;
        quill.clipboard.dangerouslyPasteHTML(quill.getLength(), html);
    }

    function fillSedat() {
        const html = `<div style="margin-top:25px; padding:15px; background:#eff6ff; border-radius:10px; border:1px solid #bfdbfe;">
            <strong style="color:#1d4ed8;">✍️ Uzm. Psk. Sedat Parmaksız Notu:</strong><br>Bu içerik psikolojik bilgilendirme amaçlıdır. Tanı ve tedavi yerine geçmez.</div><p><br></p>`;
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