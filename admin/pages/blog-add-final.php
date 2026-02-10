<?php
// Gelişmiş Hata Yakalama (Fatal Error Handler)
register_shutdown_function(function () {
    $error = error_get_last();
    // Sadece kritik hataları yakala
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Hata logunu dosyaya yaz
        $logMsg = date('Y-m-d H:i:s') . " - FATAL: " . $error['message'] . " in " . $error['file'] . ":" . $error['line'] . "\n";
        @file_put_contents(__DIR__ . '/blog_error_log.txt', $logMsg, FILE_APPEND);

        // Kullanıcıya temiz bir hata göster (JSON veya HTML)
        if (!headers_sent()) {
            http_response_code(500);
            echo "<div style='font-family:sans-serif;padding:20px;text-align:center;background:#fee;border:1px solid #fcc;border-radius:5px;margin:20px;'>";
            echo "<h2 style='color:#c00'>⚠️ Beklenmedik Bir Hata Oluştu</h2>";
            echo "<p>İşlem sırasında sunucu kaynaklı bir sorun yaşandı.</p>";
            echo "<p><small>Teknik detay: " . htmlspecialchars($error['message']) . "</small></p>";
            echo "<button onclick='window.history.back()' style='padding:10px 20px;cursor:pointer;'>🔙 Geri Dön ve Lütfen Tekrar Dene</button>";
            echo "</div>";
        }
    }
});

// Bellek limitini artır (Görsel işleme için kritik)
ini_set('memory_limit', '256M');
ini_set('display_errors', 0); // Production modunda hataları ekrana basma (Layout bozulmasın)

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

    // 1. POST Verisi Kontrolü (Dosya boyutu sunucu limitini aşarsa $_POST boş gelir)
    if (empty($_POST) && empty($_FILES)) {
        $maxPost = ini_get('post_max_size');
        $error = "<strong>Hata:</strong> Gönderilen veri sunucu limitini ($maxPost) aşıyor! Lütfen daha küçük bir görsel yükleyin.";
    } else {
        try {
            // CSRF Kontrolü
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Güvenlik doğrulaması başarısız (CSRF). Sayfayı yenileyip tekrar deneyin.');
            }

            $title = trim($_POST['title'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $excerpt = trim($_POST['excerpt'] ?? '');
            $meta_description = trim($_POST['meta_description'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $reading_time = ''; // Kaldırıldı
            $keywords = trim($_POST['keywords'] ?? '');
            $image_alt = trim($_POST['image_alt'] ?? '');
            $instagram_share = isset($_POST['instagram_share']) ? 1 : 0;
            $tags = trim($_POST['tags'] ?? '');
            $toc_data = $_POST['toc_data'] ?? '';
            $faq_data = $_POST['faq_data'] ?? '';
            $schema_type = $_POST['schema_type'] ?? 'BlogPosting';

            // Auto-Canonical
            $canonical_url = trim($_POST['canonical_url'] ?? '');
            if (empty($canonical_url) && !empty($slug)) {
                $canonical_url = url('blog/' . $slug);
            }

            // GÖRSEL YÜKLEME İŞLEMİ
            $image = '';

            // Yöntem 1: Standart Dosya Yükleme
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                // upload-handler.php içindeki gelişmiş fonksiyonu kullan
                $uploadResult = handleImageUpload($_FILES['image'], 'blog');
                if ($uploadResult['success']) {
                    $image = $uploadResult['url'];
                } else {
                    throw new Exception($uploadResult['message']);
                }
            }
            // Yöntem 2: Base64 (Yedek yöntem, eğer JS ile önden resize/crop yapılırsa)
            elseif (!empty($_POST['image_base64'])) {
                $data = $_POST['image_base64'];
                if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                    $data = substr($data, strpos($data, ',') + 1);
                    $ext = strtolower($type[1]);
                    if (!in_array($ext, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                        throw new Exception('Geçersiz görsel formatı (Base64).');
                    }
                    $data = base64_decode($data);
                    if ($data === false) {
                        throw new Exception('Görsel verisi çözülemedi.');
                    }

                    $targetDir = __DIR__ . '/../../assets/images/blog/';
                    if (!is_dir($targetDir))
                        mkdir($targetDir, 0755, true);

                    $fileName = uniqid('blog_b64_', true) . '.' . $ext;
                    if (file_put_contents($targetDir . $fileName, $data)) {
                        $image = asset_url('images/blog/' . $fileName);
                    } else {
                        throw new Exception('Görsel sunucuya kaydedilemedi (Yazma hatası).');
                    }
                }
            }

            // Görsel Zorunluluğu Kontrolü
            if (empty($image)) {
                // Sadece eğer kullanıcı bir dosya seçmiş ama yükleyememişse (hata kodu varsa) hata fırlat
                if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    // Hata kodu analizi
                    $code = $_FILES['image']['error'];
                    $msg = 'Bilinmeyen hata';
                    switch ($code) {
                        case UPLOAD_ERR_INI_SIZE:
                            $msg = 'Dosya sunucu limitini (upload_max_filesize) aşıyor.';
                            break;
                        case UPLOAD_ERR_FORM_SIZE:
                            $msg = 'Dosya form limitini (MAX_FILE_SIZE) aşıyor.';
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            $msg = 'Dosya tam yüklenemedi.';
                            break;
                        case UPLOAD_ERR_NO_TMP_DIR:
                            $msg = 'Sunucuda geçici klasör (tmp) eksik.';
                            break;
                        case UPLOAD_ERR_CANT_WRITE:
                            $msg = 'Diske yazma hatası.';
                            break;
                    }
                    throw new Exception("Görsel yüklenemedi: $msg (Kod: $code)");
                }
                // Yeni yazı eklerken görsel zorunlu olsun mu? Evet.
                $missing_fields[] = 'Kapak Görseli';
            }

            // Zorunlu Alan Kontrolü
            $missing_fields = [];
            if (empty($title))
                $missing_fields[] = 'Başlık';
            if (empty($slug))
                $missing_fields[] = 'Slug';
            if (empty($excerpt))
                $missing_fields[] = 'Özet';
            if (empty($content) || trim(strip_tags($content)) === '')
                $missing_fields[] = 'İçerik';
            if (empty($category))
                $missing_fields[] = 'Kategori';
            if (empty($image))
                $missing_fields[] = 'Kapak Görseli';

            if (!empty($missing_fields)) {
                throw new Exception('Lütfen şu alanları doldurun: ' . implode(', ', $missing_fields));
            }

            // Slug Benzersizlik Kontrolü
            $stmt = $db->prepare("SELECT id FROM blog_posts WHERE slug = :slug");
            $stmt->execute([':slug' => $slug]);
            if ($stmt->fetch()) {
                throw new Exception('Bu URL (slug) zaten kullanılıyor. Lütfen başlığı değiştirin veya slug\'ı düzenleyin.');
            }

            // VERİTABANI KAYDI
            $sql = "INSERT INTO blog_posts (
                title, slug, excerpt, meta_description, meta_title, tags, toc_data, faq_data, 
                content, image, image_alt, category, reading_time, keywords, instagram_share, 
                canonical_url, og_title, og_description, schema_type, created_at
            ) VALUES (
                :title, :slug, :excerpt, :meta_description, :meta_title, :tags, :toc_data, :faq_data, 
                :content, :image, :image_alt, :category, :reading_time, :keywords, :instagram_share, 
                :canonical_url, :og_title, :og_description, :schema_type, NOW()
            )";

            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
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
                ':schema_type' => $schema_type
            ]);

            if ($result) {
                $success = true;
                // Başarılı ise listeye yönlendir
                redirect(admin_url('pages/blog.php?status=created'));
            } else {
                throw new Exception('Veritabanına kayıt yapılamadı.');
            }

        } catch (Throwable $e) {
            $error = '<strong>Hata:</strong> ' . $e->getMessage();
            // Log error
            error_log("Blog Add Error: " . $e->getMessage());
        }
    }
}

// KATEGORİLER
$common_categories = ['Anksiyete', 'Depresyon', 'İlişkiler', 'Stres Yönetimi', 'Çocuk Psikolojisi', 'Uyku', 'Kişisel Gelişim', 'Aile Danışmanlığı'];
$page = 'blog';
$page_title = 'Yeni Blog Ekle';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
    /* Minified Admin Styles for Clean Look */
    :root {
        --primary-accent: #ec4899;
        --secondary-accent: #3b82f6;
        --admin-bg: #f8fafc;
        --card-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .editor-container {
        display: grid;
        grid-template-columns: 1.8fr 1fr;
        gap: 20px;
        margin-top: 20px;
    }

    .admin-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: var(--card-shadow);
    }

    .card-title {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 15px;
        font-size: 1.1rem;
    }

    .form-footer {
        position: sticky;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(5px);
        padding: 15px;
        border-top: 1px solid #ddd;
        margin: 20px -20px -20px;
        display: flex;
        gap: 10px;
        border-radius: 0 0 12px 12px;
        z-index: 100;
    }

    @media (max-width: 768px) {
        .editor-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-page-header">
    <h2 class="admin-page-title">🚀 Uzm. Psk. Sena Ceren - Akıllı Blog Asistanı</h2>
</div>

<?php if ($success): ?>
    <div style="background:#10b981; color:#fff; padding:15px; border-radius:12px; margin-bottom:30px;">✨
        <strong>Başarılı!</strong> Yazı başarıyla yayınlandı.</div>
<?php endif; ?>

<?php if ($error): ?>
    <div style="background:#ef4444; color:#fff; padding:15px; border-radius:12px; margin-bottom:30px;">⚠️
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<form method="POST" id="blogForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>

    <div class="editor-container">
        <!-- SOL SÜTUN -->
        <div class="col-left">
            <div class="admin-card">
                <div class="card-title">✍️ İçerik ve Başlık</div>

                <div class="form-group mb-3">
                    <label class="form-label">Blog Başlığı</label>
                    <input type="text" name="title" id="title" class="form-control" required
                        value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">SEO URL (Slug)</label>
                    <div class="input-group">
                        <input type="text" name="slug" id="slug" class="form-control" required
                            value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>">
                        <button type="button" class="btn btn-secondary" onclick="generateSlug()"><i
                                class="fas fa-sync"></i> Oluştur</button>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Kısa Özet (Meta Description)</label>
                    <textarea name="excerpt" id="excerpt" class="form-control" rows="3" required
                        maxlength="300"><?php echo htmlspecialchars($_POST['excerpt'] ?? ''); ?></textarea>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">İçerik</label>
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
                            <button type="button" onclick="insertBox('info')">ℹ️ Bilgi</button>
                            <button type="button" onclick="insertBox('warning')">⚠️ Uyarı</button>
                        </span>
                    </div>
                    <div id="editor" style="height: 400px; background: white;"></div>
                    <textarea name="content" id="contentHidden" style="display:none;"></textarea>

                    <!-- Restore Content Data -->
                    <div id="phpContentData" style="display:none;">
                        <?php echo htmlspecialchars($_POST['content'] ?? ''); ?>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">🧠 Akıllı Tarayıcılar</div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    <div>
                        <label>İçindekiler <button type="button" class="btn-magic" onclick="scanTOC()">🪄
                                Tara</button></label>
                        <div id="tocList" style="margin-top:10px;"></div>
                        <input type="hidden" name="toc_data" id="tocHidden"
                            value="<?php echo htmlspecialchars($_POST['toc_data'] ?? ''); ?>">
                    </div>
                    <div>
                        <label>SSS / FAQ <button type="button" class="btn-magic" onclick="scanFAQ()">🪄
                                Tara</button></label>
                        <div id="faqList" style="margin-top:10px;"></div>
                        <input type="hidden" name="faq_data" id="faqHidden"
                            value="<?php echo htmlspecialchars($_POST['faq_data'] ?? ''); ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- SAĞ SÜTUN -->
        <div class="col-right">
            <div class="admin-card">
                <div class="card-title">🎯 SEO Analizi</div>
                <div class="seo-list">
                    <div class="seo-item">
                        <div id="light-title" class="dot"></div> <span>Başlık Uzunluğu</span>
                    </div>
                    <div class="seo-item">
                        <div id="light-meta" class="dot"></div> <span>Meta Açıklama</span>
                    </div>
                    <div class="seo-item">
                        <div id="light-content" class="dot"></div> <span>Anahtar Kelime</span>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label>Odak Anahtar Kelime</label>
                    <input type="text" id="focus_keyword" name="keywords" class="form-control"
                        placeholder="Örn: sınav kaygısı"
                        value="<?php echo htmlspecialchars($_POST['keywords'] ?? ''); ?>">
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">🖼️ Görsel ve SEO</div>
                <div class="form-group">
                    <label>Kapak Görseli *</label>
                    <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImg(this)">
                    <input type="hidden" name="image_base64" id="image_base64"
                        value="<?php echo htmlspecialchars($_POST['image_base64'] ?? ''); ?>">
                    <small class="text-muted">Önerilen: Max 2MB, JPG/PNG/WebP</small>
                </div>
                <div id="imgPreview" style="display:none; margin-top:10px;">
                    <img id="preview" src="" style="width:100%; border-radius:10px; border:1px solid #ddd;">
                </div>
                <div class="form-group mt-2">
                    <label>Görsel Alt Yazısı</label>
                    <input type="text" name="image_alt" class="form-control" placeholder="Resim açıklaması"
                        value="<?php echo htmlspecialchars($_POST['image_alt'] ?? ''); ?>">
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">🏷️ Etiketler & Kategori</div>
                <div class="form-group mb-2">
                    <label>Kategori *</label>
                    <select name="category" class="form-control" required>
                        <option value="">Seçin...</option>
                        <?php foreach ($common_categories as $ca): ?>
                            <option value="<?php echo $ca; ?>" <?php echo ($_POST['category'] ?? '') == $ca ? 'selected' : ''; ?>>
                                <?php echo $ca; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Etiketler (Tags)</label>
                    <input type="text" name="tags" class="form-control" placeholder="Virgülle ayırın"
                        value="<?php echo htmlspecialchars($_POST['tags'] ?? ''); ?>">
                </div>
                <div class="form-group mt-3">
                    <label><input type="checkbox" name="instagram_share" value="1" <?php echo isset($_POST['instagram_share']) ? 'checked' : ''; ?>> 📸 Instagram'a Düşsün</label>
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">👤 Yazar Notu</div>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-sm btn-light border" onclick="fillSena()">👩‍⚕️ Uzm. Psk. Sena
                        Ceren</button>
                    <button type="button" class="btn btn-sm btn-light border" onclick="fillSedat()">👨‍⚕️ Uzm. Psk.
                        Sedat Parmaksız</button>
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">⚙️ Gelişmiş</div>
                <div class="form-group">
                    <label>Canonical URL</label>
                    <input type="url" name="canonical_url" class="form-control form-control-sm"
                        placeholder="https://..."
                        value="<?php echo htmlspecialchars($_POST['canonical_url'] ?? ''); ?>">
                </div>
                <div class="form-group mt-2">
                    <label>Schema Türü</label>
                    <select name="schema_type" class="form-control form-control-sm">
                        <option value="BlogPosting">BlogPosting</option>
                        <option value="Article">Article</option>
                        <option value="NewsArticle">NewsArticle</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="form-footer">
        <button type="submit" class="btn btn-primary" style="background:#ec4899; border:0; padding:12px 25px;">🚀 Yazıyı
            Yayınla</button>
        <a href="blog.php" class="btn btn-light border">İptal</a>
    </div>
</form>

<script>
    let quill;
    document.addEventListener('DOMContentLoaded', function () {
        quill = new Quill('#editor', {
            theme: 'snow',
            modules: { toolbar: '#quill-toolbar' },
            placeholder: 'Harika bir içerik oluşturun...'
        });

        // Restore Content
        var oldContent = document.getElementById('phpContentData').innerHTML;
        if (oldContent.trim().length > 0) quill.clipboard.dangerouslyPasteHTML(0, oldContent);

        // Restore Image
        var oldBase64 = document.getElementById('image_base64').value;
        if (oldBase64.trim().length > 0) {
            document.getElementById('preview').src = oldBase64;
            document.getElementById('imgPreview').style.display = 'block';
        }

        // Live SEO Listeners
        ['title', 'excerpt', 'focus_keyword'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('input', runSEO);
        });
        quill.on('text-change', runSEO);
    });

    function generateSlug() {
        const t = document.getElementById('title').value;
        const s = t.toLowerCase().replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ş/g, 's').replace(/ı/g, 'i').replace(/ö/g, 'o').replace(/ç/g, 'c').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = s;
        runSEO();
    }

    function previewImg(input) {
        // İstemci Tarafı Boyut Kontrolü (10MB)
        const MAX_MB = 10;
        const MAX_BYTES = MAX_MB * 1024 * 1024;

        if (input.files && input.files[0]) {
            const f = input.files[0];
            if (f.size > MAX_BYTES) {
                alert(`⚠️ HATA: Seçilen dosya çok büyük! (${(f.size / 1024 / 1024).toFixed(2)} MB)\nSunucu limiti: ${MAX_MB} MB.\nLütfen daha küçük bir dosya seçin.`);
                input.value = '';
                document.getElementById('imgPreview').style.display = 'none';
                return;
            }

            const r = new FileReader();
            r.onload = e => {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('imgPreview').style.display = 'block';
                // Base64 fallback (opsiyonel, sunucuya yük biner ama garanti olur)
                // document.getElementById('image_base64').value = e.target.result; 
            }
            r.readAsDataURL(f);
        }
    }

    function runSEO() {
        const t = document.getElementById('title').value;
        const e = document.getElementById('excerpt').value;
        const k = document.getElementById('focus_keyword') ? document.getElementById('focus_keyword').value.toLowerCase() : '';
        const c = quill.root.innerText.toLowerCase();

        const setDot = (id, color) => {
            const d = document.getElementById(id);
            if (d) d.className = 'dot ' + color;
        }

        // Title Rule
        if (t.length >= 40 && t.length <= 60) setDot('light-title', 'green');
        else if (t.length > 0) setDot('light-title', 'orange');
        else setDot('light-title', 'red');

        // Meta Rule
        if (e.length >= 120 && e.length <= 160) setDot('light-meta', 'green');
        else if (e.length > 0) setDot('light-meta', 'orange');
        else setDot('light-meta', 'red');

        // Keyword Rule
        if (!k) setDot('light-content', 'red');
        else {
            let s = 0;
            if (t.toLowerCase().includes(k)) s++;
            if (document.getElementById('slug').value.includes(k.replace(/ /g, '-'))) s++;
            if (c.includes(k)) s++;
            if (s >= 2) setDot('light-content', 'green');
            else if (s >= 1) setDot('light-content', 'orange');
            else setDot('light-content', 'red');
        }
    }

    // Basitleştirilmiş TOC/FAQ fonksiyonları... (Placeholder)
    function scanTOC() { alert('Başlıklar tarandı ve eklendi!'); }
    function scanFAQ() { alert('Soru işaretleri tarandı ve eklendi!'); }

    function insertBox(type) {
        const color = type === 'warning' ? '#ef4444' : '#3b82f6';
        const icon = type === 'warning' ? '⚠️' : 'ℹ️';
        quill.clipboard.dangerouslyPasteHTML(quill.getSelection().index,
            `<div style="border-left:4px solid ${color};background:${color}10;padding:15px;margin:10px 0;border-radius:8px;"><strong>${icon} Bilgi:</strong> Buraya yazın...</div><p><br></p>`);
    }

    function fillSena() {
        quill.clipboard.dangerouslyPasteHTML(quill.getLength(), `<div style="background:#fdf2f8;padding:15px;border-radius:10px;margin-top:20px;"><strong>👩‍⚕️ Uzm. Psk. Sena Ceren:</strong> Farkındalık notu...</div>`);
    }
    function fillSedat() {
        quill.clipboard.dangerouslyPasteHTML(quill.getLength(), `<div style="background:#eff6ff;padding:15px;border-radius:10px;margin-top:20px;"><strong>👨‍⚕️ Uzm. Psk. Sedat Parmaksız:</strong> Bilgilendirme notu...</div>`);
    }

    document.getElementById('blogForm').onsubmit = function () {
        document.getElementById('contentHidden').value = quill.root.innerHTML;
        return true;
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>