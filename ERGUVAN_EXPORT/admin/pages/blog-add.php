<?php
// DEBUG: Script started
ini_set('memory_limit', '256M'); // Increase memory limit for image processing
// FATAL ERROR CATCHER
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
        echo "<div style='background:red;color:white;padding:20px;z-index:99999;position:fixed;top:0;left:0;width:100%;'>";
        echo "<h3>🛑 FATAL ERROR DETECTED</h3>";
        echo "<p><strong>Message:</strong> " . $error['message'] . "</p>";
        echo "<p><strong>File:</strong> " . $error['file'] . " (" . $error['line'] . ")</p>";
        echo "</div>";
    }
});

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
    file_put_contents(__DIR__ . '/blog_debug_trace.txt', "POST Started at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

    // 1. GÖRSEL BOYUTU KONTROLÜ (KRİTİK)
    if (empty($_POST) && empty($_FILES)) {
        file_put_contents(__DIR__ . '/blog_debug_trace.txt', "❌ POST data empty (size limit exceeded)\n", FILE_APPEND);
        $error = '<strong>Hata:</strong> Yüklenen dosya çok büyük! Lütfen görsel boyutunu küçültün (Örn: Max 2MB).';
    } else {
        try {
            file_put_contents(__DIR__ . '/blog_debug_trace.txt', "CSRF Checking...\n", FILE_APPEND);
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                file_put_contents(__DIR__ . '/blog_debug_trace.txt', "❌ CSRF Failed\n", FILE_APPEND);
                throw new Exception('Geçersiz istek! CSRF token doğrulanamadı.');
            }
            file_put_contents(__DIR__ . '/blog_debug_trace.txt', "✅ CSRF OK. Processing fields...\n", FILE_APPEND);

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

            // 1. Yöntem: Standart Yükleme (Eğer çalışırsa)
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                file_put_contents(__DIR__ . '/blog_debug_trace.txt', "Start Image Upload...\n", FILE_APPEND);
                $uploadResult = handleImageUpload($_FILES['image'], 'blog');
                if ($uploadResult['success']) {
                    $image = $uploadResult['url'];
                    file_put_contents(__DIR__ . '/blog_debug_trace.txt', "✅ Image Upload Success: $image\n", FILE_APPEND);
                } else {
                    file_put_contents(__DIR__ . '/blog_debug_trace.txt', "❌ Image Upload Failed: " . $uploadResult['message'] . "\n", FILE_APPEND);
                }
            }
            // 2. Yöntem: Base64 Yükleme (Tmp Dir Hatası İçin Fallback)
            elseif (!empty($_POST['image_base64'])) {
                $data = $_POST['image_base64'];

                // Base64 başlığını temizle (data:image/png;base64,...)
                if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                    $data = substr($data, strpos($data, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif

                    if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                        throw new Exception('Geçersiz görsel türü (Base64).');
                    }

                    $data = base64_decode($data);

                    if ($data === false) {
                        throw new Exception('Görsel verisi çözülemedi.');
                    }

                    // Klasörü kontrol et
                    $targetDir = __DIR__ . '/../../assets/images/blog/';
                    if (!is_dir($targetDir))
                        mkdir($targetDir, 0755, true);

                    // Dosyayı kaydet
                    $fileName = uniqid('blog_b64_', true) . '.' . $type;
                    $filePath = $targetDir . $fileName;

                    if (file_put_contents($filePath, $data)) {
                        // URL oluştur
                        $image = asset_url('images/blog/' . $fileName);
                    } else {
                        // Yazma hatası detayları
                        $errorDetails = error_get_last();
                        throw new Exception('Görsel kaydedilemedi. Hedef: ' . $filePath . ' - Hata: ' . ($errorDetails['message'] ?? 'Bilinmiyor'));
                    }
                }
            }

            // Hata Kontrolü: İkisi de başarısızsa
            if (empty($image)) {
                // Eğer dosya seçilmiş ama yüklenememişse detaylı hata ver
                if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_OK && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $phpFileUploadErrors = [
                        1 => 'Dosya çok büyük (ini).',
                        2 => 'Dosya çok büyük (form).',
                        3 => 'Kısmi yükleme.',
                        4 => 'Dosya yok.',
                        6 => 'Geçici klasör eksik (BU HATA BYPASS EDİLDİ, LÜTFEN TEKRAR DENEYİN).',
                        7 => 'Yazma hatası.',
                        8 => 'Eklenti hatası.',
                    ];
                    $err = $phpFileUploadErrors[$_FILES['image']['error']] ?? 'Bilinmeyen';
                    // Base64 de boşsa hata fırlat
                    if (empty($_POST['image_base64'])) {
                        throw new Exception("Görsel yüklenemedi: $err (Base64 verisi de yok).");
                    }
                } elseif (empty($_POST['image_base64'])) {
                    // Yeni yazı eklerken görsel zorunlu olsun
                    // $missing_fields[] = 'Görsel'; // Aşağıda kontrol ediliyor
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

            file_put_contents(__DIR__ . '/blog_debug_trace.txt', "Preparing DB Insert...\n", FILE_APPEND);
            $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, meta_description, meta_title, tags, toc_data, faq_data, content, image, image_alt, category, reading_time, keywords, instagram_share, canonical_url, og_title, og_description, schema_type, created_at) 
                                   VALUES (:title, :slug, :excerpt, :meta_description, :meta_title, :tags, :toc_data, :faq_data, :content, :image, :image_alt, :category, :reading_time, :keywords, :instagram_share, :canonical_url, :og_title, :og_description, :schema_type, NOW())");

            file_put_contents(__DIR__ . '/blog_debug_trace.txt', "Executing DB Insert...\n", FILE_APPEND);
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
            file_put_contents(__DIR__ . '/blog_debug_trace.txt', "✅ DB Insert Success!\n", FILE_APPEND);

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
                    <input type="text" id="title" name="title" class="form-control" required
                        value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">SEO URL (Slug)</label>
                    <div class="input-group">
                        <input type="text" id="slug" name="slug" class="form-control" required
                            value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>">
                        <button type="button" class="btn btn-secondary" onclick="generateSlug()">
                            <i class="fas fa-sync"></i> Otomatik Oluştur
                        </button>
                    </div>
                    <small class="form-text text-muted">Boş bırakırsanız başlıktan otomatik oluşturulur.</small>
                </div>

                <!-- Kategori ve Diğer Alanlar SAĞ Sütuna Taşındı -->

                <div class="form-group">
                    <label class="form-label">Kısa Özet (Meta Description)</label>
                    <textarea name="excerpt" id="excerpt" class="form-control" rows="3" required
                        maxlength="300"><?php echo htmlspecialchars($_POST['excerpt'] ?? ''); ?></textarea>
                    <small class="form-text text-muted">Ana sayfada ve arama sonuçlarında görünecek kısa
                        açıklama.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">İçerik</label>
                    <!-- Toolbar for Quill -->
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
                    </div>

                    <!-- Editor Container (This must be a DIV) -->
                    <div id="editor" style="height: 400px; background: white;"></div>

                    <!-- Hidden Textarea for Form Submission -->
                    <textarea name="content" id="contentHidden" style="display:none;"></textarea>

                    <!-- Hidden Input to Transfer PHP Data to JS -->
                    <div id="phpContentData" style="display:none;">
                        <?php echo htmlspecialchars($_POST['content'] ?? ''); ?>
                    </div>
                </div>

                <!-- Gelişmiş SEO Ayarları Kartı Kaldırıldı (Sağ Sütun ile Birleştirildi) -->

                <!-- Gizli alanlar (Mükerrerlik Kaldırıldı) -->
                <!-- toc_data ve faq_data aşağıda Akıllı Tarayıcılar içinde tanımlı -->
            </div>

            <div class="admin-card">
                <div class="card-title">📍 İçindekiler Tarayıcı</div>
                <div class="scanner-panel-v2">
                    <label>Başlıklar (H2, H3) <button type="button" class="btn-magic" onclick="scanTOC()">🪄 Sayfayı
                            Tara</button></label>
                    <div id="tocList"
                        style="margin-top: 10px; min-height: 50px; border: 1px dashed #ddd; padding: 10px; border-radius: 8px;">
                    </div>
                    <input type="hidden" name="toc_data" id="tocHidden"
                        value="<?php echo htmlspecialchars($_POST['toc_data'] ?? ''); ?>">
                    <button type="button" class="btn-magic" style="margin-top:10px; width:100%;" onclick="addTocRow()">+
                        Manuel Başlık Ekle</button>
                </div>
            </div>

            <div class="admin-card">
                <div class="card-title">❓ SSS / FAQ Tarayıcı</div>
                <div class="scanner-panel-v2">
                    <label>Sorular (?) <button type="button" class="btn-magic" onclick="scanFAQ()">🪄 Sayfayı
                            Tara</button></label>
                    <div id="faqList"
                        style="margin-top: 10px; min-height: 50px; border: 1px dashed #ddd; padding: 10px; border-radius: 8px;">
                    </div>
                    <input type="hidden" name="faq_data" id="faqHidden"
                        value="<?php echo htmlspecialchars($_POST['faq_data'] ?? ''); ?>">
                    <button type="button" class="btn-magic" style="margin-top:10px; width:100%;" onclick="addFaqRow()">+
                        Manuel Soru Ekle</button>
                </div>
            </div>
        </div>

        <!-- SAĞ: SEO & Ayarlar -->
        <div class="col-right">
            <div class="admin-card">
                <div class="card-title">🎯 SEO Analizi ve Puanı</div>

                <!-- Score Gauge -->
                <div style="display:flex; justify-content:center; margin-bottom:20px; position:relative;">
                    <svg viewBox="0 0 36 36" style="width:120px; height:120px; transform: rotate(-90deg);">
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none" stroke="#eee" stroke-width="3" />
                        <path id="scoreCircle"
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none" stroke="#ec4899" stroke-width="3" stroke-dasharray="0, 100"
                            style="transition: stroke-dasharray 0.5s ease;" />
                    </svg>
                    <div
                        style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); text-align:center;">
                        <div id="seoScore" style="font-size:2rem; font-weight:800; color:#1e293b;">0</div>
                        <div style="font-size:0.75rem; color:#64748b;">PUAN</div>
                    </div>
                </div>

                <div class="seo-list">
                    <div class="seo-item">
                        <div id="check-title" class="dot"></div> <span>Başlık Uzunluğu (40-60)</span>
                    </div>
                    <div class="seo-item">
                        <div id="check-slug" class="dot"></div> <span>SEO Dostu URL (Slug)</span>
                    </div>
                    <div class="seo-item">
                        <div id="check-desc" class="dot"></div> <span>Meta Açıklama (120-160)</span>
                    </div>
                    <div class="seo-item">
                        <div id="check-content" class="dot"></div> <span>İçerik Uzunluğu (>300 kelime)</span>
                    </div>
                    <div class="seo-item">
                        <div id="check-keyword" class="dot"></div> <span>Anahtar Kelime Kullanımı</span>
                    </div>
                    <div class="seo-item">
                        <div id="check-subheadings" class="dot"></div> <span>H2/H3 Başlık Kullanımı</span>
                    </div>
                    <div class="seo-item">
                        <div id="check-image" class="dot"></div> <span>Kapak Görseli & Alt Yazısı</span>
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
                <div class="card-title">🔍 Arama Motoru (SERP) Görünümü</div>
                <div class="form-group">
                    <label>SEO Başlığı (Meta Title)</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-control" maxlength="60"
                        placeholder="Boş bırakılırsa yazı başlığı kullanılır"
                        value="<?php echo htmlspecialchars($_POST['meta_title'] ?? ''); ?>">
                    <div style="display:flex; justify-content:space-between; margin-top:5px;">
                        <small style="color:#666">Google'da görünecek mavi başlık.</small>
                        <small><span id="metaTitleCount">0</span>/60</small>
                    </div>
                </div>
                <div class="form-group">
                    <label>SEO Açıklaması (Meta Description)</label>
                    <textarea id="meta_description" name="meta_description" class="form-control" rows="3"
                        maxlength="160"
                        placeholder="Boş bırakılırsa özet kullanılır"><?php echo htmlspecialchars($_POST['meta_description'] ?? ''); ?></textarea>
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
                    <input type="hidden" name="image_base64" id="image_base64"
                        value="<?php echo htmlspecialchars($_POST['image_base64'] ?? ''); ?>">
                </div>
                <div id="imgPreview" style="display:none; margin-bottom: 10px;">
                    <img id="preview" src="" style="width:100%; border-radius: 10px;">
                </div>
                <div class="form-group">
                    <label>Görsel Alt Yazısı (SEO İçin)</label>
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
                        <?php
                        $selected_cat = $_POST['category'] ?? '';
                        foreach ($common_categories as $ca):
                            $isSelected = ($selected_cat == $ca) ? 'selected' : '';
                            ?>
                            <option value="<?php echo $ca; ?>" <?php echo $isSelected; ?>><?php echo $ca; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Etiketler (Tags)</label>
                    <input type="text" name="tags" class="form-control"
                        placeholder="Örn: depresyon, anksiyete, terapi (Virgülle ayırın)"
                        value="<?php echo htmlspecialchars($_POST['tags'] ?? ''); ?>">
                    <small style="color:#666">Her etiket için otomatik sayfa oluşturulur.</small>
                </div>
                <!-- Okuma süresi kaldırıldı -->
                <div class="form-group">
                    <label style="cursor:pointer;">
                        <input type="checkbox" name="instagram_share" value="1" <?php echo (isset($_POST['instagram_share']) ? 'checked' : ''); ?>>
                        📸 Instagram Paneline Düşsün
                    </label>
                </div>
                <div class="form-group" style="margin-top:15px; border-top:1px solid #eee; padding-top:10px;">
                    <label>Canonical URL (İsteğe Bağlı)</label>
                    <input type="url" name="canonical_url" id="canonical_url" class="form-control"
                        placeholder="https://..."
                        value="<?php echo htmlspecialchars($_POST['canonical_url'] ?? ''); ?>">
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

        // RESTORE CONTENT: Check if we have PHP content to restore
        var oldContent = document.getElementById('phpContentData').innerHTML;
        if (oldContent.trim().length > 0) {
            quill.clipboard.dangerouslyPasteHTML(0, oldContent);
        }

        // Event for SEO
        const titleEl = document.getElementById('title');
        const slugEl = document.getElementById('slug');
        const excerptEl = document.getElementById('excerpt');
        const keywordEl = document.getElementById('focus_keyword');
        const imgEl = document.querySelector('input[name="image"]');
        const imgAltEl = document.querySelector('input[name="image_alt"]');

        if (titleEl) titleEl.oninput = runSEO;
        if (slugEl) slugEl.oninput = runSEO;
        if (excerptEl) excerptEl.oninput = runSEO;
        if (keywordEl) keywordEl.oninput = runSEO;
        if (imgEl) imgEl.onchange = runSEO;
        if (imgAltEl) imgAltEl.oninput = runSEO;

        // Counter Events
        const metaTitleEl = document.getElementById('meta_title');
        const metaDescEl = document.getElementById('meta_description');

        if (metaTitleEl) {
            metaTitleEl.addEventListener('input', function () {
                const counter = document.getElementById('metaTitleCount');
                if (counter) counter.innerText = this.value.length;
                runSEO();
            });
        }
        if (metaDescEl) {
            metaDescEl.addEventListener('input', function () {
                const counter = document.getElementById('metaDescCount');
                if (counter) counter.innerText = this.value.length;
                runSEO();
            });
        }

        quill.on('text-change', runSEO);

        // RESTORE IMAGE: Check if we have Base64 image to restore
        const base64Input = document.getElementById('image_base64');
        if (base64Input && base64Input.value.trim().length > 0) {
            document.getElementById('preview').src = base64Input.value;
            document.getElementById('imgPreview').style.display = 'block';
        }
    });

    function generateSlug() {
        const titleEl = document.getElementById('title');
        if (!titleEl) return;
        const t = titleEl.value;
        const s = t.toLowerCase()
            .replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ş/g, 's').replace(/ı/g, 'i').replace(/ö/g, 'o').replace(/ç/g, 'c')
            .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        const slugEl = document.getElementById('slug');
        if (slugEl) slugEl.value = s;
        runSEO();
    }

    function runSEO() {
        if (!quill) return;
        const t = document.getElementById('title') ? document.getElementById('title').value.trim() : '';
        const e = document.getElementById('excerpt') ? document.getElementById('excerpt').value.trim() : '';
        const k = document.getElementById('focus_keyword') ? document.getElementById('focus_keyword').value.toLowerCase().trim() : '';
        const s = document.getElementById('slug') ? document.getElementById('slug').value.trim() : '';
        const mt = document.getElementById('meta_title') ? document.getElementById('meta_title').value.trim() : '';
        const md = document.getElementById('meta_description') ? document.getElementById('meta_description').value.trim() : '';
        const imgInput = document.querySelector('input[name="image"]');
        const img = imgInput ? imgInput.files[0] : null;
        const imgAltInput = document.querySelector('input[name="image_alt"]');
        const imgAlt = imgAltInput ? imgAltInput.value.trim() : '';

        const contentText = quill.root.innerText;
        const wordCount = contentText.split(/\s+/).filter(w => w.length > 0).length;

        let score = 0;
        const setStatus = (id, ok) => {
            const el = document.getElementById(id);
            if (el) el.className = 'dot ' + (ok ? 'green' : 'red');
        };

        // 1. Title (40-60 chars) [15 Puan]
        const titleToTest = mt || t;
        if (titleToTest.length >= 40 && titleToTest.length <= 60) { score += 15; setStatus('check-title', true); }
        else setStatus('check-title', false);

        // 2. Slug [10 Puan]
        if (s.length > 0 && !s.includes(' ')) { score += 10; setStatus('check-slug', true); }
        else setStatus('check-slug', false);

        // 3. Meta Description (120-160 chars) [15 Puan]
        const descToTest = md || e;
        if (descToTest.length >= 120 && descToTest.length <= 160) { score += 15; setStatus('check-desc', true); }
        else setStatus('check-desc', false);

        // 4. Content Length (>300 words) [20 Puan]
        if (wordCount >= 300) { score += 20; setStatus('check-content', true); }
        else setStatus('check-content', false);

        // 5. Keyword Usage [20 Puan]
        let kScore = 0;
        if (k.length > 0) {
            if (t.toLowerCase().includes(k)) kScore += 5;
            if (s.includes(k.replace(/ /g, '-'))) kScore += 5;
            if (contentText.toLowerCase().includes(k)) kScore += 5;
            if (contentText.substring(0, 500).toLowerCase().includes(k)) kScore += 5;
        }
        if (kScore >= 15) setStatus('check-keyword', true);
        else setStatus('check-keyword', false);
        score += kScore;

        // 6. Subheadings (H2, H3) [10 Puan]
        const hTags = quill ? quill.root.querySelectorAll('h2, h3').length : 0;
        if (hTags > 0) { score += 10; setStatus('check-subheadings', true); }
        else setStatus('check-subheadings', false);

        // 7. Image & Alt [10 Puan]
        if (img && imgAlt) { score += 10; setStatus('check-image', true); }
        else setStatus('check-image', false);

        // Update Gauge
        updateGauge(score);
    }

    function updateGauge(score) {
        const circle = document.getElementById('scoreCircle');
        const text = document.getElementById('seoScore');
        const dashArray = `${score}, 100`;

        circle.style.strokeDasharray = dashArray;
        text.innerText = score;

        let color = '#ef4444'; // Red
        if (score >= 50) color = '#f59e0b'; // Orange
        if (score >= 80) color = '#10b981'; // Green
        circle.style.stroke = color;
    }

    function scanTOC() {
        const container = document.getElementById('tocList');
        container.innerHTML = '';
        quill.root.querySelectorAll('h2, h3').forEach(h => {
            addTocRow(h.innerText.trim(), h.tagName.toLowerCase());
        });
    }

    function addTocRow(text = '', level = 'h2') {
        const div = document.createElement('div');
        div.className = 'toc-item';
        div.style = "display:flex; gap:5px; margin-bottom:5px;";
        div.innerHTML = `
            <select onchange="saveTOC()" class="form-control" style="width:70px;"><option value="h2" ${level == 'h2' ? 'selected' : ''}>H2</option><option value="h3" ${level == 'h3' ? 'selected' : ''}>H3</option></select>
            <input type="text" value="${text}" oninput="saveTOC()" class="form-control" style="flex:1;">
            <button type="button" onclick="this.parentElement.remove();saveTOC();" style="color:#ef4444; border:0; background:none; cursor:pointer;">✕</button>`;
        document.getElementById('tocList').appendChild(div);
        saveTOC();
    }

    function saveTOC() {
        const items = [];
        document.querySelectorAll('.toc-item').forEach(row => {
            const level = row.querySelector('select').value;
            const text = row.querySelector('input').value.trim();
            if (text) items.push({ level, text });
        });
        document.getElementById('tocHidden').value = JSON.stringify(items);
    }

    function scanFAQ() {
        if (!quill) return;
        const container = document.getElementById('faqList');
        const text = quill.root.innerText;
        const matches = text.match(/[^.!?\n]{5,}\?/g);
        if (!matches) return alert('İçerikte soru (?) bulunamadı.');
        container.innerHTML = '';
        matches.forEach(q => addFaqRow(q.trim()));
    }

    function addFaqRow(q = '', a = '') {
        const div = document.createElement('div');
        div.className = 'faq-item';
        div.style = "margin-bottom:10px; padding:10px; background:#f8fafc; border-radius:8px; position:relative;";
        div.innerHTML = `
            <input type="text" value="${q}" oninput="saveFAQ()" class="form-control mb-1" placeholder="Soru">
            <textarea oninput="saveFAQ()" class="form-control" style="height:60px;" placeholder="Cevap">${a}</textarea>
            <button type="button" onclick="this.parentElement.remove();saveFAQ();" style="position:absolute; right:5px; top:5px; color:#ef4444; border:0; background:none; cursor:pointer;">✕</button>`;
        document.getElementById('faqList').appendChild(div);
        saveFAQ();
    }

    function saveFAQ() {
        const items = [];
        document.querySelectorAll('.faq-item').forEach(row => {
            const q = row.querySelector('input').value.trim();
            const a = row.querySelector('textarea').value.trim();
            if (q) items.push({ q, a });
        });
        document.getElementById('faqHidden').value = JSON.stringify(items);
    }

    function previewImg(input) {
        // Dosya boyutu kontrolü (2MB)
        const MAX_SIZE = 2 * 1024 * 1024;

        if (input.files && input.files[0]) {
            if (input.files[0].size > MAX_SIZE) {
                alert("⚠️ HATA: Seçtiğiniz görsel çok büyük! (Boyut: " + (input.files[0].size / 1024 / 1024).toFixed(2) + " MB)\n\nSunucu limiti: 2 MB.\nLütfen daha küçük bir görsel seçin veya görseli sıkıştırın.");
                input.value = ''; // Seçimi temizle
                document.getElementById('imgPreview').style.display = 'none';
                return;
            }

            const r = new FileReader();
            r.onload = e => {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('imgPreview').style.display = 'block';
                // Base64 verisini gizli inputa ata (Tmp Dir hatasını bypass etmek için)
                document.getElementById('image_base64').value = e.target.result;
            }
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