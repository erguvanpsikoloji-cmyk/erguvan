<?php
// TAMPON BAŞLATIYORUZ (Header already sent hatası için)
ob_start();

// DEBUG: Script started
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// === DIAGNOSTIC BLOCK START ===
echo "<div style='background:#fef3c7; border:2px solid #f59e0b; padding:15px; font-family:monospace; margin-bottom:20px; text-align:left; color:black;'>";
echo "<h3>🔧 DIAGNOSTIC MODE (V3)</h3>";
echo "<strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "<strong>Script:</strong> " . __FILE__ . "<br>";
echo "<strong>Writable Dir:</strong> " . (is_writable(__DIR__) ? '✅ YES' : '❌ NO') . "<br>";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
    echo "<strong>Session:</strong> Started (Was inactive)<br>";
} else {
    echo "<strong>Session:</strong> Already Active<br>";
}
echo "<strong>Session ID:</strong> " . session_id() . "<br>";

if (isset($_SESSION)) {
    echo "<strong>Session Contents:</strong><pre>" . print_r($_SESSION, true) . "</pre>";
} else {
    echo "<strong>Session:</strong> EMPTY/NULL<br>";
}

// Log dosyasını kontrol et
$logFile = __DIR__ . '/blog_debug_trace.txt';
echo "<strong>Log File Path:</strong> $logFile <br>";
if (file_exists($logFile)) {
    echo "<strong>Log File:</strong> Exists (" . filesize($logFile) . " bytes)<br>";
} else {
    echo "<strong>Log File:</strong> Not Found. Attempting create... ";
    $cr = file_put_contents($logFile, "Init Log " . date('Y-m-d H:i:s') . "\n");
    echo ($cr !== false ? "✅ Created" : "❌ Failed") . "<br>";
}

echo "</div>";
// === DIAGNOSTIC BLOCK END ===

ini_set('memory_limit', '256M'); // Increase memory limit for image processing

// FATAL ERROR CATCHER
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
        // Hata logla
        error_log("FATAL ERROR: " . print_r($error, true));

        // Eğer output buffering açıksan temizle
        if (ob_get_level())
            ob_end_clean();

        echo "<div style='background:red;color:white;padding:20px;font-family:sans-serif;z-index:99999;position:fixed;top:0;left:0;width:100%;'>";
        echo "<h3>🛑 FATAL ERROR DETECTED</h3>";
        echo "<p><strong>Message:</strong> " . $error['message'] . "</p>";
        echo "<p><strong>File:</strong> " . $error['file'] . " (" . $error['line'] . ")</p>";
        echo "</div>";
    }
});

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
// requireLogin(); // Login kontrolünü geçici olarak kapatalım, session'ı görelim
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../database/db.php';
require_once __DIR__ . '/../includes/upload-handler.php';

$db = getDB();
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log file path
    $logFile = __DIR__ . '/blog_debug_trace.txt';
    file_put_contents($logFile, "POST Started at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

    // 1. GÖRSEL BOYUTU KONTROLÜ (KRİTİK)
    if (empty($_POST) && empty($_FILES)) {
        file_put_contents($logFile, "❌ POST data empty (size limit exceeded)\n", FILE_APPEND);
        $error = '<strong>Hata:</strong> Yüklenen dosya çok büyük! Lütfen görsel boyutunu küçültün (Örn: Max 2MB).';
    } else {
        try {
            file_put_contents($logFile, "CSRF Checking...\n", FILE_APPEND);
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                file_put_contents($logFile, "❌ CSRF Failed\n", FILE_APPEND);
                throw new Exception('Geçersiz istek! CSRF token doğrulanamadı.');
            }
            file_put_contents($logFile, "✅ CSRF OK. Processing fields...\n", FILE_APPEND);

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

            // 1. Yöntem: Standart Yükleme
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                file_put_contents($logFile, "Start Image Upload...\n", FILE_APPEND);
                $uploadResult = handleImageUpload($_FILES['image'], 'blog');
                if ($uploadResult['success']) {
                    $image = $uploadResult['url'];
                    file_put_contents($logFile, "✅ Image Upload Success: $image\n", FILE_APPEND);
                } else {
                    file_put_contents($logFile, "❌ Image Upload Failed: " . $uploadResult['message'] . "\n", FILE_APPEND);
                    throw new Exception($uploadResult['message']);
                }
            }
            // 2. Yöntem: Base64 Yükleme
            elseif (!empty($_POST['image_base64'])) {
                $data = $_POST['image_base64'];
                if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                    $data = substr($data, strpos($data, ',') + 1);
                    $type = strtolower($type[1]);

                    if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                        throw new Exception('Geçersiz görsel türü (Base64).');
                    }
                    $data = base64_decode($data);
                    if ($data === false) {
                        throw new Exception('Görsel verisi çözülemedi.');
                    }

                    $targetDir = __DIR__ . '/../../assets/images/blog/';
                    if (!is_dir($targetDir))
                        mkdir($targetDir, 0755, true);

                    $fileName = uniqid('blog_b64_', true) . '.' . $type;
                    $filePath = $targetDir . $fileName;

                    if (file_put_contents($filePath, $data)) {
                        $image = asset_url('images/blog/' . $fileName);
                    } else {
                        throw new Exception('Görsel kaydedilemedi.');
                    }
                }
            }

            // Hata Kontrolü
            if (empty($image)) {
                if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_OK && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $phpFileUploadErrors = [
                        1 => 'Dosya çok büyük (php.ini).',
                        2 => 'Dosya çok büyük (HTML form).',
                        3 => 'Kısmi yükleme.',
                        4 => 'Dosya yok.',
                        6 => 'Geçici klasör eksik.',
                        7 => 'Yazma hatası.',
                        8 => 'PHP uzantısı durdurdu.'
                    ];
                    $errCode = $_FILES['image']['error'];
                    $errMsg = $phpFileUploadErrors[$errCode] ?? 'Bilinmeyen Hata';

                    // Base64 de boşsa hata fırlat
                    if (empty($_POST['image_base64'])) {
                        throw new Exception("Görsel yüklenemedi: $errMsg (Kod: $errCode)");
                    }
                } elseif (empty($_POST['image_base64'])) {
                    // Görsel zorunlu değilse burayı geçebiliriz ama zorunlu olsun
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
            // if (empty($content) || $content === '<p><br></p>') $missing_fields[] = 'İçerik'; // Optional content check
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

            file_put_contents($logFile, "Preparing DB Insert...\n", FILE_APPEND);
            $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, meta_description, meta_title, tags, toc_data, faq_data, content, image, image_alt, category, reading_time, keywords, instagram_share, canonical_url, og_title, og_description, schema_type, created_at) 
                                   VALUES (:title, :slug, :excerpt, :meta_description, :meta_title, :tags, :toc_data, :faq_data, :content, :image, :image_alt, :category, :reading_time, :keywords, :instagram_share, :canonical_url, :og_title, :og_description, :schema_type, NOW())");

            file_put_contents($logFile, "Executing DB Insert...\n", FILE_APPEND);
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
                ':schema_type' => $_POST['schema_type'] ?? 'BlogPosting'
            ]);

            if ($result) {
                file_put_contents($logFile, "✅ DB Insert Success!\n", FILE_APPEND);
                $success = true;
                // Formu temizle
                $_POST = [];
            } else {
                throw new Exception('Veritabanına kayıt yapılamadı.');
            }

        } catch (Throwable $e) {
            $error = '<strong>Hata:</strong> ' . $e->getMessage() . '<br>';
            $error .= '<small>Dosya: ' . basename($e->getFile()) . ' (' . $e->getLine() . ')</small>';
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
        z-index: 1000;
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
    <div
        style="background: #ecfdf5; color: #047857; padding: 20px; border-radius: 12px; margin-bottom:30px; border: 1px solid #10b981; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h3 style="margin:0 0 10px 0;">✨ Harika! Yazı başarıyla yayınlandı.</h3>
        <p style="margin:0;">Yayındaki blog yazısını görmek veya listeye dönmek için aşağıdaki butonları kullanabilirsiniz.
        </p>
        <div style="margin-top:15px; display:flex; gap:10px;">
            <a href="blog.php" class="btn btn-light" style="color:#059669; font-weight:bold; border:1px solid #10b981;">📋
                Listeye Dön</a>
            <a href="<?php echo url('blog/' . $slug); ?>" target="_blank" class="btn btn-light"
                style="color:#059669; font-weight:bold; border:1px solid #10b981;">👁️ Yazıyı Görüntüle</a>
            <a href="blog-add.php" class="btn btn-light" style="color:#059669; border:1px solid #10b981;">➕ Yeni Yazı
                Ekle</a>
        </div>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div
        style="background: #fef2f2; color: #b91c1c; padding: 15px; border-radius: 12px; margin-bottom:30px; border: 1px solid #f87171; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        ⚠️
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<!-- FORM BAŞLANGICI -->
<form method="POST" id="blogForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>

    <div class="editor-container">
        <!-- SOL: İçerik Bölümü -->
        <div class="col-left">
            <div class="admin-card">
                <div class="card-title">✍️ İçerik ve Başlık</div>
                <div class="form-group">
                    <label class="form-label">Blog Başlığı</label>
                    <input type="text" name="title" id="title" class="form-control" required
                        value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">SEO URL (Slug)</label>
                    <div class="input-group">
                        <input type="text" name="slug" id="slug" class="form-control" required
                            value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>">
                        <button type="button" class="btn btn-secondary" onclick="generateSlug()"><i
                                class="fas fa-sync"></i> Otomatik Oluştur</button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Kısa Özet (Meta Description)</label>
                    <textarea name="excerpt" id="excerpt" class="form-control" rows="3" required
                        maxlength="300"><?php echo htmlspecialchars($_POST['excerpt'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">İçerik</label>
                    <div id="quill-toolbar">
                        <!-- Toolbar Items -->
                        <span class="ql-formats"><select class="ql-header">
                                <option value="2"></option>
                                <option value="3"></option>
                                <option selected></option>
                            </select></span>
                        <span class="ql-formats"><button class="ql-bold"></button><button
                                class="ql-italic"></button><button class="ql-link"></button></span>
                        <span class="ql-formats"><button class="ql-list" value="ordered"></button><button
                                class="ql-list" value="bullet"></button></span>
                    </div>
                    <div id="editor" style="height: 400px; background: white;"></div>
                    <textarea name="content" id="contentHidden" style="display:none;"></textarea>
                    <div id="phpContentData" style="display:none;">
                        <?php echo htmlspecialchars($_POST['content'] ?? ''); ?>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">🧠 Akıllı Tarayıcılar</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label>İçindekiler <button type="button" class="btn-magic" onclick="scanTOC()">🪄
                                Tara</button></label>
                        <div id="tocList" style="margin-top: 10px;"></div>
                        <input type="hidden" name="toc_data" id="tocHidden"
                            value="<?php echo htmlspecialchars($_POST['toc_data'] ?? ''); ?>">
                    </div>
                    <div>
                        <label>SSS / FAQ <button type="button" class="btn-magic" onclick="scanFAQ()">🪄
                                Tara</button></label>
                        <div id="faqList" style="margin-top: 10px;"></div>
                        <input type="hidden" name="faq_data" id="faqHidden"
                            value="<?php echo htmlspecialchars($_POST['faq_data'] ?? ''); ?>">
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
                        <div id="light-title" class="dot"></div> <span>Başlık Uzunluğu</span>
                    </div>
                    <div class="seo-item">
                        <div id="light-meta" class="dot"></div> <span>Meta Açıklama</span>
                    </div>
                    <div class="seo-item">
                        <div id="light-content" class="dot"></div> <span>Anahtar Kelime</span>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 15px;">
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
                </div>
                <div id="imgPreview" style="display:none; margin-bottom: 10px;">
                    <img id="preview" src="" style="width:100%; border-radius: 10px;">
                </div>
                <div class="form-group">
                    <label>Görsel Alt Yazısı</label>
                    <input type="text" name="image_alt" class="form-control" placeholder="Resimde ne görünüyor?"
                        value="<?php echo htmlspecialchars($_POST['image_alt'] ?? ''); ?>">
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">🏷️ Etiketler & Ayarlar</div>
                <div class="form-group">
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
                    <label>Etiketler</label>
                    <input type="text" name="tags" class="form-control" placeholder="Virgülle ayırın"
                        value="<?php echo htmlspecialchars($_POST['tags'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="instagram_share" value="1" <?php echo isset($_POST['instagram_share']) ? 'checked' : ''; ?>> 📸 Instagram</label>
                </div>
                <div class="form-group" style="margin-top:10px;">
                    <label>Canonical URL</label>
                    <input type="url" name="canonical_url" class="form-control" placeholder="https://..."
                        value="<?php echo htmlspecialchars($_POST['canonical_url'] ?? ''); ?>">
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">👤 Yazar Notu</div>
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
        if (document.getElementById('editor')) {
            quill = new Quill('#editor', {
                theme: 'snow',
                modules: { toolbar: '#quill-toolbar' },
                placeholder: 'Anlatmaya başlayın...'
            });

            var oldContent = document.getElementById('phpContentData').innerHTML;
            if (oldContent.trim().length > 0) quill.clipboard.dangerouslyPasteHTML(0, oldContent);

            var oldBase64 = document.getElementById('image_base64').value;
            if (oldBase64.trim().length > 0) {
                document.getElementById('preview').src = oldBase64;
                document.getElementById('imgPreview').style.display = 'block';
            }

            document.getElementById('title').oninput = runSEO;
            document.getElementById('excerpt').oninput = runSEO;
            document.getElementById('focus_keyword').oninput = runSEO;
            quill.on('text-change', runSEO);
        }
    });

    function generateSlug() {
        const t = document.getElementById('title').value;
        const s = t.toLowerCase().replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ş/g, 's').replace(/ı/g, 'i').replace(/ö/g, 'o').replace(/ç/g, 'c').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = s;
        runSEO();
    }

    function runSEO() {
        if (!quill) return;
        const t = document.getElementById('title').value;
        const e = document.getElementById('excerpt').value;
        const k = document.getElementById('focus_keyword').value.toLowerCase();

        const setLight = (id, status) => { document.getElementById(id).className = 'dot ' + status; }

        if (t.length >= 40 && t.length <= 60) setLight('light-title', 'green'); else setLight('light-title', t.length > 0 ? 'orange' : 'red');
        if (e.length >= 120 && e.length <= 160) setLight('light-meta', 'green'); else setLight('light-meta', e.length > 0 ? 'orange' : 'red');

        if (!k) setLight('light-content', 'red');
        else {
            let score = 0;
            if (t.toLowerCase().includes(k)) score++;
            if (document.getElementById('slug').value.includes(k)) score++;
            if (score >= 2) setLight('light-content', 'green'); else setLight('light-content', 'orange');
        }
    }

    function scanTOC() { alert('Başlıklar tarandı! (Prototip)'); }
    function scanFAQ() { alert('Sorular tarandı! (Prototip)'); }
    function fillSena() { if (quill) quill.clipboard.dangerouslyPasteHTML(quill.getLength(), '<div>Sena Ceren Notu...</div>'); }
    function fillSedat() { if (quill) quill.clipboard.dangerouslyPasteHTML(quill.getLength(), '<div>Sedat Parmaksız Notu...</div>'); }

    function previewImg(input) {
        const MAX_SIZE = 2 * 1024 * 1024;
        if (input.files && input.files[0]) {
            if (input.files[0].size > MAX_SIZE) {
                alert("⚠️ HATA: Dosya çok büyük (Max 2MB)!");
                input.value = '';
                return;
            }
            const r = new FileReader();
            r.onload = e => {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('imgPreview').style.display = 'block';
                document.getElementById('image_base64').value = e.target.result;
            }
            r.readAsDataURL(input.files[0]);
        }
    }

    document.getElementById('blogForm').onsubmit = function () {
        if (quill) document.getElementById('contentHidden').value = quill.root.innerHTML;
        return true;
    };

    function livePreview() {
        const d = { title: document.getElementById('title').value, content: quill ? quill.root.innerHTML : '' };
        localStorage.setItem('preview_data', JSON.stringify(d));
        window.open('blog-preview.php', '_blank');
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<?php ob_end_flush(); ?>