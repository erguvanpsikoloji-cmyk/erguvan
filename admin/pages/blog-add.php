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
                <div class="card-title">🧠 Akıllı Tarayıcılar</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label>İçindekiler <button type="button" class="btn-magic" onclick="scanTOC()">🪄
                                Tara</button></label>
                        <div id="tocList" style="margin-top: 10px;"></div>
                        <input type="hidden" name="toc_data" id="tocHidden"
                            value="<?php echo htmlspecialchars($_POST['toc_data'] ?? ''); ?>">
                        <button type="button" class="btn-magic" onclick="addTocManually()">+ Manuel</button>
                    </div>
                    <div>
                        <label>SSS / FAQ <button type="button" class="btn-magic" onclick="scanFAQ()">🪄
                                Tara</button></label>
                        <div id="faqList" style="margin-top: 10px;"></div>
                        <input type="hidden" name="faq_data" id="faqHidden"
                            value="<?php echo htmlspecialchars($_POST['faq_data'] ?? ''); ?>">
                        <button type="button" class="btn-magic" onclick="addFaqManually()">+ Manuel</button>
                    </div>
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
        document.getElementById('title').oninput = runSEO;
        document.getElementById('slug').oninput = runSEO;
        document.getElementById('excerpt').oninput = runSEO;
        document.getElementById('focus_keyword').oninput = runSEO;
        document.querySelector('input[name="image"]').onchange = runSEO;
        document.querySelector('input[name="image_alt"]').oninput = runSEO;

        // Counter Events
        document.getElementById('meta_title').addEventListener('input', function () {
            document.getElementById('metaTitleCount').innerText = this.value.length;
        });
        document.getElementById('meta_description').addEventListener('input', function () {
            document.getElementById('metaDescCount').innerText = this.value.length;
        });

        quill.on('text-change', runSEO);

        // RESTORE IMAGE: Check if we have Base64 image to restore
        var oldBase64 = document.getElementById('image_base64').value;
        if (oldBase64.trim().length > 0) {
            document.getElementById('preview').src = oldBase64;
            document.getElementById('imgPreview').style.display = 'block';
        }
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
        const t = document.getElementById('title').value.trim();
        const e = document.getElementById('excerpt').value.trim();
        const k = document.getElementById('focus_keyword').value.toLowerCase().trim();
        const s = document.getElementById('slug').value.trim();
        const img = document.getElementById('image_base64').value || document.querySelector('input[name="image"]').value;
        const imgAlt = document.querySelector('input[name="image_alt"]').value.trim();

        const contentText = quill.root.innerText;
        const wordCount = contentText.split(/\s+/).filter(w => w.length > 0).length;

        let score = 0;
        const setStatus = (id, ok) => {
            document.getElementById(id).className = 'dot ' + (ok ? 'green' : 'red');
        };

        // 1. Title (40-60 chars) [15 Puan]
        if (t.length >= 40 && t.length <= 60) { score += 15; setStatus('check-title', true); }
        else if (t.length > 10) { score += 5; setStatus('check-title', false); } // Kısmi puan
        else setStatus('check-title', false);

        // 2. Slug [10 Puan]
        if (s.length > 0 && !s.includes(' ')) { score += 10; setStatus('check-slug', true); }
        else setStatus('check-slug', false);

        // 3. Meta Description (120-160 chars) [15 Puan]
        document.getElementById('metaDescCount').innerText = e.length;
        if (e.length >= 120 && e.length <= 160) { score += 15; setStatus('check-desc', true); }
        else if (e.length > 50) { score += 5; setStatus('check-desc', false); }
        else setStatus('check-desc', false);

        // 4. Content Length (>300 words) [20 Puan]
        if (wordCount >= 300) { score += 20; setStatus('check-content', true); }
        else if (wordCount >= 100) { score += 10; setStatus('check-content', false); }
        else setStatus('check-content', false);

        // 5. Keyword Usage [20 Puan]
        let kScore = 0;
        if (k.length > 0) {
            if (t.toLowerCase().includes(k)) kScore += 5;
            if (s.includes(k.replace(/ /g, '-'))) kScore += 5;
            if (contentText.toLowerCase().includes(k)) kScore += 5;
            // İlk 100 kelimede var mı?
            if (contentText.substring(0, 500).toLowerCase().includes(k)) kScore += 5;
        }
        if (kScore === 20) setStatus('check-keyword', true);
        else setStatus('check-keyword', false); // Tam puan değilse kırmızı kalsın (veya sarı)
        score += kScore;

        // 6. Subheadings (H2, H3) [10 Puan]
        const hTags = quill.root.querySelectorAll('h2, h3').length;
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

    // --- RESCUE OPERATION START ---
    // Bu blok, FTP ile yuklenemeyen dosyalari sunucu tarafinda olusturur.
    // Islem basarili olduktan sonra bu blogu silebilirsiniz.
    if (isset($_GET['rescue_files']) && $_GET['rescue_files'] == '1') {
        $filesToRescue = [
            '../../includes/footer.php' => base64_decode('PGZvb3RlciBjbGFzcz0iZm9vdGVyIiBzdHlsZT0iYmFja2dyb3VuZC1jb2xvcjogIzJkMzc0ODsgY29sb3I6IHdoaXRlOyI+DQogICAgPGRpdiBjbGFzcz0iY29udGFpbmVyIj4NCiAgICAgICAgPGRpdiBjbGFzcz0iZm9vdGVyLWdyaWQiPg0KICAgICAgICAgICAgPGRpdiBjbGFzcz0iZm9vdGVyLWJyYW5kIj4NCiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJmb290ZXItbG9nbyI+DQogICAgICAgICAgICAgICAgICAgIDxpbWcgc3JjPSI8P3BocCBlY2hvIGFzc2V0X3VybCgnaW1hZ2VzL2xvZ28ud2VicCcpOyA/PiIgYWx0PSJFcmd1dmFuIFBzaWtvbG9qaSINCiAgICAgICAgICAgICAgICAgICAgICAgIHN0eWxlPSJoZWlnaHQ6IDYwcHg7IHdpZHRoOiBhdXRvOyBtYXJnaW4tYm90dG9tOiAyMHB4OyBmaWx0ZXI6IGJyaWdodG5lc3MoMCkgaW52ZXJ0KDEpOyI+DQogICAgICAgICAgICAgICAgPC9kaXY+DQogICAgICAgICAgICAgICAgPHAgc3R5bGU9ImNvbG9yOiAjY2JkNWUwOyI+QWthZGVtaWsgdGVtZWxsaSwgZXRpayB2ZSBwcm9mZXN5b25lbCBwc2lrb2xvamlrIGRhbsOEwrHDhcW4bWFubMOEwrFrIGhpem1ldGxlcmkgaWxlDQogICAgICAgICAgICAgICAgICAgIHlhbsOEwrFuw4TCsXpkYXnDhMKxei48L3A+DQogICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iZm9vdGVyLXNvY2lhbCIgc3R5bGU9ImRpc3BsYXk6ZmxleDsgZ2FwOjEwcHg7Ij4NCiAgICAgICAgICAgICAgICAgICAgPGEgaHJlZj0iaHR0cHM6Ly9pbnN0YWdyYW0uY29tL2VyZ3V2YW5wc2lrb2xvamkiIHRhcmdldD0iX2JsYW5rIiBhcmlhLWxhYmVsPSJJbnN0YWdyYW0iDQogICAgICAgICAgICAgICAgICAgICAgICBzdHlsZT0iY29sb3I6d2hpdGU7Ij4NCiAgICAgICAgICAgICAgICAgICAgICAgIDxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCA0NDggNTEyIg0KICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZpbGw9ImN1cnJlbnRDb2xvciI+DQogICAgICAgICAgICAgICAgICAgICAgICAgICAgPHBhdGgNCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZD0iTTIyNC4xIDE0MWMtNjMuNiAwLTExNC45IDUxLjMtMTE0LjkgMTE0LjlzNTEuMyAxMTQuOSAxMTQuOSAxMTQuOVMzMzkgMzE5LjUgMzM5IDI1NS45IDI4Ny43IDE0MSAyMjQuMSAxNDF6bTAgMTg5LjZjLTQxLjEgMC03NC43LTMzLjUtNzQuNy03NC43czMzLjUtNzQuNyA3NC43LTc0LjcgNzQuNyAzMy41IDc0LjcgNzQuNy0zMy42IDc0LjctNzQuNyA3NC43em0xNDYuNC0xOTQuM2MwIDE0LjktMTIgMjYuOC0yNi44IDI2LjgtMTQuOSAwLTI2LjgtMTItMjYuOC0yNi44czEyLTI2LjggMjYuOC0yNi44IDI2LjggMTIgMjYuOCAyNi44em03Ni4xIDI3LjJjLTEuNy0zNS45LTkuOS02Ny43LTM2LjItOTMuOS0yNi4yLTI2LjItNTgtMzQuNC05My45LTM2LjItMzctMi4xLTE0Ny45LTIuMS0xODQuOSAwLTM1LjggMS43LTY3LjYgOS45LTkzLjkgMzYuMXMtMzQuNCA1OC0zNi4yIDkzLjljLTIuMSAzNy0yLjEgMTQ3LjkgMCAxODQuOSAxLjcgMzUuOSA5LjkgNjcuNyAzNi4yIDkzLjlzNTggMzQuNCA5My45IDM2LjJjMzcgMi4xIDE0Ny45IDIuMSAxODQuOSAwIDM1LjktMS43IDY3LjctOS45IDkzLjktMzYuMiAyNi4yLTI2LjIgMzQuNC01OCAzNi4yLTkzLjkgMi4xLTM3IDIuMS0xNDcuOSAwLTE4NC45ek0zOTguOCAzODhjLTcuOCAxOS42LTIyLjkgMzQuNy00Mi42IDQyLjYtMjkuNSAxMS43LTk5LjUgOS0xMzIuMSA5cy0xMDIuNyAyLjYtMTMyLjEtOWMtMTkuNi03LjgtMzQuNy0yMi45LTQyLjYtNDIuNi0xMS43LTI5LjUtOS05OS41LTktMTMyLjFzLTIuNi0xMDIuNyA5LTEzMi4xYzcuOC0xOS42IDIyLjktMzQuNyA0Mi42LTQyLjYgMjkuNS0xMS43IDk5LjUtOSAxMzIuMS05czEwMi43LTIuNiAxMzIuMSA5YzE5LjYgNy44IDM0LjcgMjIuOSA0Mi42IDQyLjYgMTEuNyAyOS41IDkgOTkuNSA5IDEzMi4xczIuNyAxMDIuNy05IDEzMi4xeiIgLz4NCiAgICAgICAgICAgICAgICAgICAgICAgIDwvc3ZnPg0KICAgICAgICAgICAgICAgICAgICA8L2E+DQogICAgICAgICAgICAgICAgICAgIDxhIGhyZWY9IiMiIHRhcmdldD0iX2JsYW5rIiBhcmlhLWxhYmVsPSJMaW5rZWRJbiIgc3R5bGU9ImNvbG9yOndoaXRlOyI+DQogICAgICAgICAgICAgICAgICAgICAgICA8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjI0IiBoZWlnaHQ9IjI0IiB2aWV3Qm94PSIwIDAgNDQ4IDUxMiINCiAgICAgICAgICAgICAgICAgICAgICAgICAgICBmaWxsPSJjdXJyZW50Q29sb3IiPg0KICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxwYXRoDQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGQ9Ik0xMDAuMjggNDQ4SDcuNFYxNDguOWg5Mi44OHpNNTMuNzkgMTA4LjFDMjQuMDkgMTA4LjEgMCA4My41IDAgNTMuOGE1My43OSA1My43OSAwIDAgMSAxMDcuNTggMGMwIDI5LjctMjQuMSA1NC4zLTUzLjc5IDU0LjN6TTQ0Ny45IDQ0OGgtOTIuNjhWMzAyLjRjMC0zNC43LS43LTc5LjItNDguMjktNzkuMi00OC4yOSAwLTU1LjY5IDM3LjctNTUuNjkgNzYuN1Y0NDhoLTkyLjc4VjE0OC45aDg5LjA4djQwLjhoMS4zYzEyLjQtMjMuNSA0Mi42OS00OC4zIDg3Ljg4LTQ4LjMgOTQgMCAxMTEuMjggNjEuOSAxMTEuMjggMTQyLjNWNDQ4eiIgLz4NCiAgICAgICAgICAgICAgICAgICAgICAgIDwvc3ZnPg0KICAgICAgICAgICAgICAgICAgICA8L2E+DQogICAgICAgICAgICAgICAgPC9kaXY+DQogICAgICAgICAgICA8L2Rpdj4NCg0KICAgICAgICAgICAgPGRpdiBjbGFzcz0iZm9vdGVyLW5hdiI+DQogICAgICAgICAgICAgICAgPGg0IHN0eWxlPSJjb2xvcjp3aGl0ZTsiPkjDhMKxemzDhMKxIE1lbsODwrw8L2g0Pg0KICAgICAgICAgICAgICAgIDx1bCBzdHlsZT0ibGlzdC1zdHlsZTpub25lOyBwYWRkaW5nOjA7Ij4NCiAgICAgICAgICAgICAgICAgICAgPGxpPjxhIGhyZWY9Ijw/cGhwIGVjaG8gdXJsKCk7ID8+IiBzdHlsZT0iY29sb3I6I2NiZDVlMDsgdGV4dC1kZWNvcmF0aW9uOm5vbmU7Ij5BbmEgU2F5ZmE8L2E+PC9saT4NCiAgICAgICAgICAgICAgICAgICAgPGxpPjxhIGhyZWY9Ijw/cGhwIGVjaG8gdXJsKCcjaGl6bWV0bGVyJyk7ID8+Ig0KICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN0eWxlPSJjb2xvcjojY2JkNWUwOyB0ZXh0LWRlY29yYXRpb246bm9uZTsiPkhpem1ldGxlcmltaXo8L2E+PC9saT4NCiAgICAgICAgICAgICAgICAgICAgPGxpPjxhIGhyZWY9Ijw/cGhwIGVjaG8gcGFnZV91cmwoJ2Jsb2cucGhwJyk7ID8+Ig0KICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN0eWxlPSJjb2xvcjojY2JkNWUwOyB0ZXh0LWRlY29yYXRpb246bm9uZTsiPkJsb2c8L2E+PC9saT4NCiAgICAgICAgICAgICAgICAgICAgPGxpPjxhIGhyZWY9Ijw/cGhwIGVjaG8gdXJsKCcjaWxldGlzaW0nKTsgPz4iDQogICAgICAgICAgICAgICAgICAgICAgICAgICAgc3R5bGU9ImNvbG9yOiNjYmQ1ZTA7IHRleHQtZGVjb3JhdGlvbjpub25lOyI+w4TCsGxldGnDhcW4aW08L2E+PC9saT4NCiAgICAgICAgICAgICAgICA8L3VsPg0KICAgICAgICAgICAgPC9kaXY+DQoNCiAgICAgICAgICAgIDxkaXYgY2xhc3M9ImZvb3Rlci1zZXJ2aWNlcyI+DQogICAgICAgICAgICAgICAgPGg0IHN0eWxlPSJjb2xvcjp3aGl0ZTsiPlV6bWFubMOEwrFrbGFyPC9oND4NCiAgICAgICAgICAgICAgICA8dWwgc3R5bGU9Imxpc3Qtc3R5bGU6bm9uZTsgcGFkZGluZzowOyI+DQogICAgICAgICAgICAgICAgICAgIDxsaT48YSBocmVmPSIjIiBzdHlsZT0iY29sb3I6I2NiZDVlMDsgdGV4dC1kZWNvcmF0aW9uOm5vbmU7Ij5CaXJleXNlbCBUZXJhcGk8L2E+PC9saT4NCiAgICAgICAgICAgICAgICAgICAgPGxpPjxhIGhyZWY9IiMiIHN0eWxlPSJjb2xvcjojY2JkNWUwOyB0ZXh0LWRlY29yYXRpb246bm9uZTsiPsOD4oChaWZ0IFRlcmFwaXNpPC9hPjwvbGk+DQogICAgICAgICAgICAgICAgICAgIDxsaT48YSBocmVmPSIjIiBzdHlsZT0iY29sb3I6I2NiZDVlMDsgdGV4dC1kZWNvcmF0aW9uOm5vbmU7Ij7Dg+KAoW9jdWsgdmUgRXJnZW48L2E+PC9saT4NCiAgICAgICAgICAgICAgICAgICAgPGxpPjxhIGhyZWY9IiMiIHN0eWxlPSJjb2xvcjojY2JkNWUwOyB0ZXh0LWRlY29yYXRpb246bm9uZTsiPk9ubGluZSBEYW7DhMKxw4XFuG1hbmzDhMKxazwvYT48L2xpPg0KICAgICAgICAgICAgICAgIDwvdWw+DQogICAgICAgICAgICA8L2Rpdj4NCg0KICAgICAgICAgICAgPGRpdiBjbGFzcz0iZm9vdGVyLWNvbnRhY3QiPg0KICAgICAgICAgICAgICAgIDxoNCBzdHlsZT0iY29sb3I6d2hpdGU7Ij7DhMKwbGV0acOFxbhpbTwvaDQ+DQogICAgICAgICAgICAgICAgPHVsIHN0eWxlPSJsaXN0LXN0eWxlOm5vbmU7IHBhZGRpbmc6MDsiPg0KICAgICAgICAgICAgICAgICAgICA8bGkgc3R5bGU9ImNvbG9yOiNjYmQ1ZTA7IG1hcmdpbi1ib3R0b206MTBweDsiPjxzdHJvbmcgc3R5bGU9ImNvbG9yOndoaXRlOyI+VGVsOjwvc3Ryb25nPiA8YQ0KICAgICAgICAgICAgICAgICAgICAgICAgICAgIGhyZWY9InRlbDorOTA1NTExNzY1Mjg1IiBzdHlsZT0iY29sb3I6I2NiZDVlMDsgdGV4dC1kZWNvcmF0aW9uOm5vbmU7Ij4wNTUxIDE3NiA1MiA4NTwvYT4NCiAgICAgICAgICAgICAgICAgICAgPC9saT4NCiAgICAgICAgICAgICAgICAgICAgPGxpIHN0eWxlPSJjb2xvcjojY2JkNWUwOyBtYXJnaW4tYm90dG9tOjEwcHg7Ij48c3Ryb25nIHN0eWxlPSJjb2xvcjp3aGl0ZTsiPkVtYWlsOjwvc3Ryb25nPiA8YQ0KICAgICAgICAgICAgICAgICAgICAgICAgICAgIGhyZWY9Im1haWx0bzppbmZvQGVyZ3V2YW5wc2lrb2xvamkuY29tIg0KICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN0eWxlPSJjb2xvcjojY2JkNWUwOyB0ZXh0LWRlY29yYXRpb246bm9uZTsiPmluZm9AZXJndXZhbnBzaWtvbG9qaS5jb208L2E+PC9saT4NCiAgICAgICAgICAgICAgICAgICAgPGxpIHN0eWxlPSJjb2xvcjojY2JkNWUwOyI+PHN0cm9uZyBzdHlsZT0iY29sb3I6d2hpdGU7Ij5BZHJlczo8L3N0cm9uZz4gw4XCnmVocmVtaW5pLCBNaWxsZXQgQ2QuIDM0MDk4DQogICAgICAgICAgICAgICAgICAgICAgICBGYXRpaC/DhMKwc3RhbmJ1bDwvbGk+DQogICAgICAgICAgICAgICAgPC91bD4NCiAgICAgICAgICAgIDwvZGl2Pg0KICAgICAgICA8L2Rpdj4NCg0KICAgICAgICA8ZGl2IGNsYXNzPSJmb290ZXItYm90dG9tIiBzdHlsZT0iYm9yZGVyLXRvcDogMXB4IHNvbGlkICM0YTU1Njg7IG1hcmdpbi10b3A6IDMwcHg7IHBhZGRpbmctdG9wOiAyMHB4OyI+DQogICAgICAgICAgICA8cCBzdHlsZT0iY29sb3I6ICNhMGFlYzA7Ij4mY29weTsgPD9waHAgZWNobyBkYXRlKCdZJyk7ID8+IEVyZ3V2YW4gUHNpa29sb2ppLiBUw4PCvG0gaGFrbGFyw4TCsSBzYWtsw4TCsWTDhMKxci48L3A+DQogICAgICAgIDwvZGl2Pg0KICAgIDwvZGl2Pg0KPC9mb290ZXI+DQoNCjwhLS0gQXNzZXRzIC0tPg0KPHNjcmlwdCBzcmM9Imh0dHBzOi8vdW5wa2cuY29tL3N3aXBlci9zd2lwZXItYnVuZGxlLm1pbi5qcyI+PC9zY3JpcHQ+DQo8c2NyaXB0IHNyYz0iPD9waHAgZWNobyBhc3NldF91cmwoJ2pzL3NjcmlwdC5qcycpOyA/PiIgZGVmZXI+PC9zY3JpcHQ+DQoNCjwhLS0gRmxvYXRpbmcgQnV0dG9ucyAtLT4NCjxkaXYgY2xhc3M9ImZsb2F0aW5nLWN0YSI+DQogICAgPGEgaHJlZj0iaHR0cHM6Ly93YS5tZS85MDU1MTE3NjUyODUiIGNsYXNzPSJjdGEtYnRuIHdoYXRzYXBwIiB0YXJnZXQ9Il9ibGFuayIgYXJpYS1sYWJlbD0iV2hhdHNBcHAiPg0KICAgICAgICA8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDQ0OCA1MTIiIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgZmlsbD0iY3VycmVudENvbG9yIj4NCiAgICAgICAgICAgIDxwYXRoDQogICAgICAgICAgICAgICAgZD0iTTM4MC45IDk3LjFDMzM5IDU1LjEgMjgzLjIgMzIgMjIzLjkgMzJjLTEyMi40IDAtMjIyIDk5LjYtMjIyIDIyMiAwIDM5LjEgMTAuMiA3Ny4zIDI5LjYgMTExTDAgNDgwbDExNy43LTMwLjljMzIuNCAxNy43IDY4LjkgMjcgMTA2LjEgMjdoLjFjMTIyLjMgMCAyMjQuMS05OS42IDIyNC4xLTIyMiAwLTU5LjMtMjUuMi0xMTUtNjcuMS0xNTd6bS0xNTcgMzQxLjZjLTMzLjIgMC02NS43LTguOS05NC0yNS43bC02LjctNC02OS44IDE4LjNMNzIgMzU5LjJsLTQuNC03Yy0xOC41LTI5LjQtMjguMi02My4zLTI4LjItOTguMiAwLTEwMS43IDgyLjgtMTg0LjUgMTg0LjYtMTg0LjUgNDkuMyAwIDk1LjYgMTkuMiAxMzAuNCA1NC4xIDM0LjggMzQuOSA1Ni4yIDgxLjIgNTYuMSAxMzAuNSAwIDEwMS44LTg0LjkgMTg0LjYtMTg2LjYgMTg0LjZ6bTEwMS4yLTEzOC4yYy01LjUtMi44LTMyLjgtMTYuMi0zNy45LTE4LTUuMS0xLjktOC44LTIuOC0xMi41IDIuOC0zLjcgNS42LTE0LjMgMTgtMTcuNiAyMS44LTMuMiAzLjctNi41IDQuMi0xMiAxLjQtMzIuNi0xNi4zLTU0LTI5LjEtNzUuNS02Ni01LjctOS44IDUuNy05LjEgMTYuMy0zMC4zIDEuOC0zLjcgLjktNi45LS41LTkuNy0xLjQtMi44LTEyLjUtMzAuMS0xNy4xLTQxLjItNC41LTEwLjgtOS4xLTkuMy0xMi41LTkuNS0zLjItLjItNi45LS4yLTEwLjYtLjItMy43IDAtOS43IDEuNC0xNC44IDYuOS01LjEgNS42LTE5LjQgMTktMTkuNCA0Ni4zIDAgMjcuMyAxOS45IDUzLjcgMjIuNiA1Ny40IDIuOCAzLjcgMzkuMSA1OS43IDk0LjggODMuOCAzNS4yIDE1LjIgNDkgMTYuNSA2Ni42IDEzLjkgMTAuNy0xLjYgMzIuOC0xMy40IDM3LjQtMjYuNCA0LjYtMTMgNC42LTI0LjEgMy4yLTI2LjQtMS4zLTIuNS01LTQtMTAuNS02Ljd6IiAvPg0KICAgICAgICA8L3N2Zz4NCiAgICA8L2E+DQogICAgPGEgaHJlZj0idGVsOis5MDU1MTE3NjUyODUiIGNsYXNzPSJjdGEtYnRuIHBob25lIiBhcmlhLWxhYmVsPSJBcmF5w4TCsW4iPg0KICAgICAgICA8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHdpZHRoPSIyMiIgaGVpZ2h0PSIyMiIgZmlsbD0iY3VycmVudENvbG9yIj4NCiAgICAgICAgICAgIDxwYXRoDQogICAgICAgICAgICAgICAgZD0iTTQ5My40IDI0LjZsLTEwNC0yNGMtMTEuMy0yLjYtMjIuOSAzLjMtMjcuNSAxMy45bC00OCAxMTJjLTQuMiA5LjgtMS40IDIxLjMgNi45IDI4bDYwLjYgNDkuNmMtMzYgNzYuNy05OC45IDE0MC45LTE3Ny4yIDE3Ny4ybC00OS42LTYwLjZjLTYuOC04LjMtMTguMi0xMS4xLTI4LTYuOWwtMTEyIDQ4QzMuOSAzNjYuNS0yIDM3OC4xLjYgMzg5LjRsMjQgMTA0QzI3LjEgNTA0LjIgMzYuNyA1MTIgNDggNTEyYzI1Ni4xIDAgNDY0LTIwNy45IDQ2NC00NjQgMC0xMS4yLTcuNy0yMC45LTE4LjYtMjMuNHoiIC8+DQogICAgICAgIDwvc3ZnPg0KICAgIDwvYT4NCjwvZGl2Pg0KDQo8P3BocA0KLy8gQ29uZmlnIGRvc3lhc8OEwrFuw4TCsSB5w4PCvGtsZSAoZcOExbhlciB5w4PCvGtsZW5tZW1pw4XFuHNlKQ0KaWYgKCFkZWZpbmVkKCdCQVNFX1VSTCcpKSB7DQogICAgcmVxdWlyZV9vbmNlIF9fRElSX18gLiAnLy4uL2NvbmZpZy5waHAnOw0KfQ0KDQovLyBTdHJ1Y3R1cmVkIERhdGEgKEpTT04tTEQpDQokcHJvdG9jb2wgPSBpc3NldCgkX1NFUlZFUlsnSFRUUFMnXSkgJiYgJF9TRVJWRVJbJ0hUVFBTJ10gPT09ICdvbicgPyAnaHR0cHMnIDogJ2h0dHAnOw0KJGhvc3QgPSAkX1NFUlZFUlsnSFRUUF9IT1NUJ107DQokc2l0ZV91cmwgPSAkcHJvdG9jb2wgLiAnOi8vJyAuICRob3N0IC4gQkFTRV9VUkw7DQokY3VycmVudF91cmwgPSAkcHJvdG9jb2wgLiAnOi8vJyAuICRob3N0IC4gJF9TRVJWRVJbJ1JFUVVFU1RfVVJJJ107DQoNCi8vIE9yZ2FuaXphdGlvbiBTY2hlbWENCiRvcmdhbml6YXRpb25fc2NoZW1hID0gWw0KICAgICJAY29udGV4dCIgPT4gImh0dHBzOi8vc2NoZW1hLm9yZyIsDQogICAgIkB0eXBlIiA9PiAiUHJvZmVzc2lvbmFsU2VydmljZSIsDQogICAgIm5hbWUiID0+ICJVem0uIFBzay4gU2VuYSBDZXJlbiIsDQogICAgImRlc2NyaXB0aW9uIiA9PiAiUHJvZmVzeW9uZWwgcHNpa29sb2ppayBkYW7DhMKxw4XFuG1hbmzDhMKxayB2ZSB0ZXJhcGkgaGl6bWV0bGVyaSIsDQogICAgInVybCIgPT4gJHNpdGVfdXJsLA0KICAgICJsb2dvIiA9PiAkc2l0ZV91cmwgLiBhc3NldF91cmwoJ2ltYWdlcy9sb2dvLndlYnAnKSwNCiAgICAiaW1hZ2UiID0+ICRzaXRlX3VybCAuIGFzc2V0X3VybCgnaW1hZ2VzL2xvZ28ud2VicCcpLA0KICAgICJ0ZWxlcGhvbmUiID0+ICIrOTA1NTExNzY1Mjg1IiwNCiAgICAiZW1haWwiID0+ICJpbmZvQHV6bWFucHNpa29sb2dzZW5hY2VyZW4uY29tIiwNCiAgICAiYWRkcmVzcyIgPT4gWw0KICAgICAgICAiQHR5cGUiID0+ICJQb3N0YWxBZGRyZXNzIiwNCiAgICAgICAgInN0cmVldEFkZHJlc3MiID0+ICLDhcKeZWhyZW1pbmksIE1pbGxldCBDZC4iLA0KICAgICAgICAiYWRkcmVzc0xvY2FsaXR5IiA9PiAiRmF0aWgiLA0KICAgICAgICAiYWRkcmVzc1JlZ2lvbiIgPT4gIsOEwrBzdGFuYnVsIiwNCiAgICAgICAgInBvc3RhbENvZGUiID0+ICIzNDA5OCIsDQogICAgICAgICJhZGRyZXNzQ291bnRyeSIgPT4gIlRSIg0KICAgIF0sDQogICAgImdlbyIgPT4gWw0KICAgICAgICAiQHR5cGUiID0+ICJHZW9Db29yZGluYXRlcyIsDQogICAgICAgICJsYXRpdHVkZSIgPT4gIjQxLjAxNTI4IiwNCiAgICAgICAgImxvbmdpdHVkZSIgPT4gIjI4LjkzMjkxIg0KICAgIF0sDQogICAgIm9wZW5pbmdIb3Vyc1NwZWNpZmljYXRpb24iID0+IFsNCiAgICAgICAgWw0KICAgICAgICAgICAgIkB0eXBlIiA9PiAiT3BlbmluZ0hvdXJzU3BlY2lmaWNhdGlvbiIsDQogICAgICAgICAgICAiZGF5T2ZXZWVrIiA9PiBbIk1vbmRheSIsICJUdWVzZGF5IiwgIldlZG5lc2RheSIsICJUaHVyc2RheSIsICJGcmlkYXkiXSwNCiAgICAgICAgICAgICJvcGVucyIgPT4gIjA5OjAwIiwNCiAgICAgICAgICAgICJjbG9zZXMiID0+ICIyMjowMCINCiAgICAgICAgXSwNCiAgICAgICAgWw0KICAgICAgICAgICAgIkB0eXBlIiA9PiAiT3BlbmluZ0hvdXJzU3BlY2lmaWNhdGlvbiIsDQogICAgICAgICAgICAiZGF5T2ZXZWVrIiA9PiBbIlNhdHVyZGF5IiwgIlN1bmRheSJdLA0KICAgICAgICAgICAgIm9wZW5zIiA9PiAiMDk6MDAiLA0KICAgICAgICAgICAgImNsb3NlcyIgPT4gIjIxOjAwIg0KICAgICAgICBdDQogICAgXSwNCiAgICAicHJpY2VSYW5nZSIgPT4gIiQkIiwNCiAgICAiYXJlYVNlcnZlZCIgPT4gWw0KICAgICAgICAiQHR5cGUiID0+ICJDb3VudHJ5IiwNCiAgICAgICAgIm5hbWUiID0+ICJUdXJrZXkiDQogICAgXSwNCiAgICAic2VydmljZVR5cGUiID0+IFsNCiAgICAgICAgIkJpcmV5c2VsIFRlcmFwaSIsDQogICAgICAgICLDg+KAoWlmdCBUZXJhcGlzaSIsDQogICAgICAgICJPbmxpbmUgVGVyYXBpIiwNCiAgICAgICAgIkFpbGUgRGFuw4TCscOFxbhtYW5sw4TCscOExbjDhMKxIiwNCiAgICAgICAgIk95dW4gVGVyYXBpc2kiLA0KICAgICAgICAiw4PigKFvY3VrIHZlIEVyZ2VuIFRlcmFwaXNpIg0KICAgIF0sDQogICAgInNhbWVBcyIgPT4gWw0KICAgICAgICAiaHR0cHM6Ly93d3cuaW5zdGFncmFtLmNvbS91em0ucHNrLnNlbmFjZXJlbiIsDQogICAgICAgICJodHRwczovL3R3aXR0ZXIuY29tL3NlbmFjZXJlbiINCiAgICBdDQpdOw0KDQovLyBXZWJTaXRlIFNjaGVtYQ0KJHdlYnNpdGVfc2NoZW1hID0gWw0KICAgICJAY29udGV4dCIgPT4gImh0dHBzOi8vc2NoZW1hLm9yZyIsDQogICAgIkB0eXBlIiA9PiAiV2ViU2l0ZSIsDQogICAgIm5hbWUiID0+ICJFcmd1dmFuIFBzaWtvbG9qaSIsDQogICAgInVybCIgPT4gJHNpdGVfdXJsLA0KICAgICJwb3RlbnRpYWxBY3Rpb24iID0+IFsNCiAgICAgICAgIkB0eXBlIiA9PiAiU2VhcmNoQWN0aW9uIiwNCiAgICAgICAgInRhcmdldCIgPT4gJHNpdGVfdXJsIC4gIi9wYWdlcy9ibG9nLnBocD9zZWFyY2g9e3NlYXJjaF90ZXJtX3N0cmluZ30iLA0KICAgICAgICAicXVlcnktaW5wdXQiID0+ICJyZXF1aXJlZCBuYW1lPXNlYXJjaF90ZXJtX3N0cmluZyINCiAgICBdDQpdOw0KDQovLyBTYXlmYSB0aXBpbmUgZ8ODwrZyZSBlayBzY2hlbWENCiRhZGRpdGlvbmFsX3NjaGVtYSA9IFtdOw0KaWYgKGlzc2V0KCRwYWdlX3R5cGUpICYmICRwYWdlX3R5cGUgPT09ICdhcnRpY2xlJyAmJiBpc3NldCgkcG9zdCkgJiYgaXNfYXJyYXkoJHBvc3QpICYmICFlbXB0eSgkcG9zdCkpIHsNCiAgICAvLyBCbG9nIHBvc3QgacODwqdpbiBBcnRpY2xlIFNjaGVtYQ0KICAgICRhZGRpdGlvbmFsX3NjaGVtYSA9IFsNCiAgICAgICAgIkBjb250ZXh0IiA9PiAiaHR0cHM6Ly9zY2hlbWEub3JnIiwNCiAgICAgICAgIkB0eXBlIiA9PiAiQmxvZ1Bvc3RpbmciLA0KICAgICAgICAiaGVhZGxpbmUiID0+IGh0bWxzcGVjaWFsY2hhcnMoJHBvc3RbJ3RpdGxlJ10pLA0KICAgICAgICAiZGVzY3JpcHRpb24iID0+IGh0bWxzcGVjaWFsY2hhcnMoJHBvc3RbJ2V4Y2VycHQnXSksDQogICAgICAgICJpbWFnZSIgPT4gJHBvc3RbJ2ltYWdlJ10gPz8gKGZpbGVfZXhpc3RzKF9fRElSX18gLiAnLy4uL2Fzc2V0cy9pbWFnZXMvbG9nby53ZWJwJykgPyAkc2l0ZV91cmwgLiBhc3NldF91cmwoJ2ltYWdlcy9sb2dvLndlYnAnKSA6IChmaWxlX2V4aXN0cyhfX0RJUl9fIC4gJy8uLi9hc3NldHMvaW1hZ2VzL2xvZ28ucG5nJykgPyAkc2l0ZV91cmwgLiBhc3NldF91cmwoJ2ltYWdlcy9sb2dvLnBuZycpIDogJycpKSwNCiAgICAgICAgImRhdGVQdWJsaXNoZWQiID0+IGRhdGUoJ2MnLCBzdHJ0b3RpbWUoJHBvc3RbJ2NyZWF0ZWRfYXQnXSkpLA0KICAgICAgICAiZGF0ZU1vZGlmaWVkIiA9PiBpc3NldCgkcG9zdFsndXBkYXRlZF9hdCddKSA/IGRhdGUoJ2MnLCBzdHJ0b3RpbWUoJHBvc3RbJ3VwZGF0ZWRfYXQnXSkpIDogZGF0ZSgnYycsIHN0cnRvdGltZSgkcG9zdFsnY3JlYXRlZF9hdCddKSksDQogICAgICAgICJhdXRob3IiID0+IFsNCiAgICAgICAgICAgICJAdHlwZSIgPT4gIlBlcnNvbiIsDQogICAgICAgICAgICAibmFtZSIgPT4gIkVyZ3V2YW4gUHNpa29sb2ppIg0KICAgICAgICBdLA0KICAgICAgICAicHVibGlzaGVyIiA9PiBbDQogICAgICAgICAgICAiQHR5cGUiID0+ICJPcmdhbml6YXRpb24iLA0KICAgICAgICAgICAgIm5hbWUiID0+ICJFcmd1dmFuIFBzaWtvbG9qaSIsDQogICAgICAgICAgICAibG9nbyIgPT4gWw0KICAgICAgICAgICAgICAgICJAdHlwZSIgPT4gIkltYWdlT2JqZWN0IiwNCiAgICAgICAgICAgICAgICAidXJsIiA9PiAoZmlsZV9leGlzdHMoX19ESVJfXyAuICcvLi4vYXNzZXRzL2ltYWdlcy9sb2dvLndlYnAnKSA/ICRzaXRlX3VybCAuIGFzc2V0X3VybCgnaW1hZ2VzL2xvZ28ud2VicCcpIDogKGZpbGVfZXhpc3RzKF9fRElSX18gLiAnLy4uL2Fzc2V0cy9pbWFnZXMvbG9nby5wbmcnKSA/ICRzaXRlX3VybCAuIGFzc2V0X3VybCgnaW1hZ2VzL2xvZ28ucG5nJykgOiAnJykpDQogICAgICAgICAgICBdDQogICAgICAgIF0sDQogICAgICAgICJtYWluRW50aXR5T2ZQYWdlIiA9PiBbDQogICAgICAgICAgICAiQHR5cGUiID0+ICJXZWJQYWdlIiwNCiAgICAgICAgICAgICJAaWQiID0+ICRjdXJyZW50X3VybA0KICAgICAgICBdDQogICAgXTsNCn0NCj8+DQoNCjwhLS0gU3RydWN0dXJlZCBEYXRhIChKU09OLUxEKSAtLT4NCjxzY3JpcHQgdHlwZT0iYXBwbGljYXRpb24vbGQranNvbiI+DQogICAgPD9waHAgZWNobyBqc29uX2VuY29kZSgkb3JnYW5pemF0aW9uX3NjaGVtYSwgSlNPTl9VTkVTQ0FQRURfVU5JQ09ERSB8IEpTT05fVU5FU0NBUEVEX1NMQVNIRVMgfCBKU09OX1BSRVRUWV9QUklOVCk7ID8+DQogICAgPC9zY3JpcHQ+DQoNCjxzY3JpcHQgdHlwZT0iYXBwbGljYXRpb24vbGQranNvbiI+DQogICAgPD9waHAgZWNobyBqc29uX2VuY29kZSgkd2Vic2l0ZV9zY2hlbWEsIEpTT05fVU5FU0NBUEVEX1VOSUNPREUgfCBKU09OX1VORVNDQVBFRF9TTEFTSEVTIHwgSlNPTl9QUkVUVFlfUFJJTlQpOyA/Pg0KICAgIDwvc2NyaXB0Pg0KDQo8P3BocCBpZiAoIWVtcHR5KCRhZGRpdGlvbmFsX3NjaGVtYSkpOiA/Pg0KICAgIDxzY3JpcHQgdHlwZT0iYXBwbGljYXRpb24vbGQranNvbiI+DQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPD9waHAgZWNobyBqc29uX2VuY29kZSgkYWRkaXRpb25hbF9zY2hlbWEsIEpTT05fVU5FU0NBUEVEX1VOSUNPREUgfCBKU09OX1VORVNDQVBFRF9TTEFTSEVTIHwgSlNPTl9QUkVUVFlfUFJJTlQpOyA/Pg0KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvc2NyaXB0Pg0KPD9waHAgZW5kaWY7ID8+DQoNCg0KPC9ib2R5Pg0KDQo8L2h0bWw+'),
            '../../pages/blog-post.php' => base64_decode('PD9waHANCi8qKg0KICogRVJHVVZBTiBQU8OEwrBLT0xPSsOEwrAgLSBQUkVNSVVNIEJMT0cgVEFTQVJJTUkgKFY4KQ0KICogWWF6w4TCsSBzdGlsbGVyaSwgZm9udGxhciwgYm/DhcW4bHVrbGFyIHZlIGdlbmVsIGJsb2cgZ8ODwrZyw4PCvG7Dg8K8bcODwrxuw4PCvCBwcm9mZXN5b25lbCBzZXZpeWV5ZSB0YcOFxbjDhMKxci4NCiAqLw0KDQpyZXF1aXJlX29uY2UgX19ESVJfXyAuICcvLi4vY29uZmlnLnBocCc7DQpyZXF1aXJlX29uY2UgJy4uL2RhdGFiYXNlL2RiLnBocCc7DQoNCiRkYiA9IGdldERCKCk7DQokc2x1ZyA9ICRfR0VUWydzbHVnJ10gPz8gJyc7DQokcG9zdCA9ICRkYi0+cHJlcGFyZSgiU0VMRUNUICogRlJPTSBibG9nX3Bvc3RzIFdIRVJFIHNsdWcgPSA/Iik7DQokcG9zdC0+ZXhlY3V0ZShbJHNsdWddKTsNCiRwb3N0ID0gJHBvc3QtPmZldGNoKCk7DQppZiAoISRwb3N0KQ0KICAgIHJlZGlyZWN0KEJBU0VfVVJMIC4gJy9ibG9nJyk7DQoNCiRwYWdlX3RpdGxlID0gJHBvc3RbJ3RpdGxlJ107DQokcGFnZV9kZXNjcmlwdGlvbiA9ICRwb3N0WydtZXRhX2Rlc2NyaXB0aW9uJ10gPzogJHBvc3RbJ2V4Y2VycHQnXTsNCg0KaWYgKCFmdW5jdGlvbl9leGlzdHMoJ3NsdWdpZnknKSkgew0KICAgIGZ1bmN0aW9uIHNsdWdpZnkoJHRleHQpDQogICAgew0KICAgICAgICAkdGV4dCA9IHByZWdfcmVwbGFjZSgnflteXHBMXGRdK351JywgJy0nLCAkdGV4dCk7DQogICAgICAgICR0ZXh0ID0gaWNvbnYoJ3V0Zi04JywgJ3VzLWFzY2lpLy9UUkFOU0xJVCcsICR0ZXh0KTsNCiAgICAgICAgJHRleHQgPSBwcmVnX3JlcGxhY2UoJ35bXi1cd10rficsICcnLCAkdGV4dCk7DQogICAgICAgICR0ZXh0ID0gdHJpbSgkdGV4dCwgJy0nKTsNCiAgICAgICAgJHRleHQgPSBwcmVnX3JlcGxhY2UoJ34tK34nLCAnLScsICR0ZXh0KTsNCiAgICAgICAgJHRleHQgPSBzdHJ0b2xvd2VyKCR0ZXh0KTsNCiAgICAgICAgaWYgKGVtcHR5KCR0ZXh0KSkgew0KICAgICAgICAgICAgcmV0dXJuICduLWEnOw0KICAgICAgICB9DQogICAgICAgIHJldHVybiAkdGV4dDsNCiAgICB9DQp9DQoNCmZ1bmN0aW9uIGluamVjdFByZW1pdW1EZXNpZ24oJiRjb250ZW50LCAkcG9zdCkNCnsNCiAgICAvLyBQUkVNSVVNIENTUw0KICAgICRzdHlsZSA9ICcNCiAgICA8c3R5bGUgaWQ9ImVyZ3V2YW4tcHJlbWl1bS1kZXNpZ24iPg0KICAgICAgICA6cm9vdCB7DQogICAgICAgICAgICAtLWFjY2VudC1jb2xvcjogdmFyKC0tc2Vjb25kYXJ5KTsNCiAgICAgICAgICAgIC0tYWNjZW50LXNvZnQ6IHJnYmEoMTM5LCA2MSwgNzIsIDAuMDUpOw0KICAgICAgICAgICAgLS10ZXh0LWRhcms6IHZhcigtLXRleHQtZGFyayk7DQogICAgICAgICAgICAtLXRleHQtbXV0ZWQ6IHZhcigtLXRleHQtbWVkaXVtKTsNCiAgICAgICAgfQ0KDQogICAgICAgIC8qIEdFTkVMIEJMT0cgRMODxZNaRU7DhMKwICovDQogICAgICAgIC5ibG9nLXBvc3Qtd3JhcHBlciB7DQogICAgICAgICAgICBtYXgtd2lkdGg6IDg1MHB4Ow0KICAgICAgICAgICAgbWFyZ2luOiAwIGF1dG87DQogICAgICAgICAgICBwYWRkaW5nOiAwIDIwcHg7DQogICAgICAgICAgICBmb250LWZhbWlseTogdmFyKC0tZm9udC1ib2R5KTsNCiAgICAgICAgfQ0KDQogICAgICAgIC5ibG9nLXBvc3QtY29udGVudCB7DQogICAgICAgICAgICBmb250LXNpemU6IDEuMTVyZW0gIWltcG9ydGFudDsNCiAgICAgICAgICAgIGxpbmUtaGVpZ2h0OiAxLjggIWltcG9ydGFudDsNCiAgICAgICAgICAgIGNvbG9yOiB2YXIoLS10ZXh0LWRhcmspICFpbXBvcnRhbnQ7DQogICAgICAgIH0NCg0KICAgICAgICAuYmxvZy1wb3N0LWNvbnRlbnQgcCB7DQogICAgICAgICAgICBtYXJnaW4tYm90dG9tOiAycmVtICFpbXBvcnRhbnQ7DQogICAgICAgIH0NCg0KICAgICAgICAvKiBCQcOFwp5MSUsgU1TDhMKwTExFUsOEwrAgKi8NCiAgICAgICAgLmJsb2ctcG9zdC1jb250ZW50IGgyIHsNCiAgICAgICAgICAgIGZvbnQtc2l6ZTogMnJlbSAhaW1wb3J0YW50Ow0KICAgICAgICAgICAgZm9udC13ZWlnaHQ6IDgwMCAhaW1wb3J0YW50Ow0KICAgICAgICAgICAgY29sb3I6IHZhcigtLXRleHQtZGFyaykgIWltcG9ydGFudDsNCiAgICAgICAgICAgIG1hcmdpbjogMy41cmVtIDAgMS41cmVtIDAgIWltcG9ydGFudDsNCiAgICAgICAgICAgIGxpbmUtaGVpZ2h0OiAxLjMgIWltcG9ydGFudDsNCiAgICAgICAgICAgIGJvcmRlci1sZWZ0OiA2cHggc29saWQgdmFyKC0tYWNjZW50LWNvbG9yKTsNCiAgICAgICAgICAgIHBhZGRpbmctbGVmdDogMTVweDsNCiAgICAgICAgICAgIGZvbnQtZmFtaWx5OiB2YXIoLS1mb250LWhlYWRpbmcpOw0KICAgICAgICB9DQoNCiAgICAgICAgLmJsb2ctcG9zdC1jb250ZW50IGgzIHsNCiAgICAgICAgICAgIGZvbnQtc2l6ZTogMS42cmVtICFpbXBvcnRhbnQ7DQogICAgICAgICAgICBmb250LXdlaWdodDogNzAwICFpbXBvcnRhbnQ7DQogICAgICAgICAgICBjb2xvcjogdmFyKC0tdGV4dC1kYXJrKSAhaW1wb3J0YW50Ow0KICAgICAgICAgICAgbWFyZ2luOiAyLjVyZW0gMCAxLjJyZW0gMCAhaW1wb3J0YW50Ow0KICAgICAgICAgICAgZm9udC1mYW1pbHk6IHZhcigtLWZvbnQtaGVhZGluZyk7DQogICAgICAgIH0NCg0KICAgICAgICAvKiDDhMKww4PigKHDhMKwTkRFS8OEwrBMRVIgKFRPQykgKi8NCiAgICAgICAgLnRvYy1jb250YWluZXItcHJlbWl1bSB7DQogICAgICAgICAgICBiYWNrZ3JvdW5kOiB2YXIoLS1iZy1zb2Z0KTsNCiAgICAgICAgICAgIGJvcmRlcjogMXB4IHNvbGlkIHJnYmEoMTM5LCA2MSwgNzIsIDAuMSk7DQogICAgICAgICAgICBib3JkZXItbGVmdDogMTJweCBzb2xpZCB2YXIoLS1hY2NlbnQtY29sb3IpOw0KICAgICAgICAgICAgYm9yZGVyLXJhZGl1czogMjRweDsNCiAgICAgICAgICAgIHBhZGRpbmc6IDIuNXJlbTsNCiAgICAgICAgICAgIG1hcmdpbjogM3JlbSAwOw0KICAgICAgICAgICAgYm94LXNoYWRvdzogMCAxMHB4IDMwcHggcmdiYSgyOSwgNDUsIDgwLCAwLjA1KTsNCiAgICAgICAgfQ0KDQogICAgICAgIC50b2MtdGl0bGUtcHJlbWl1bSB7DQogICAgICAgICAgICBjb2xvcjogdmFyKC0tYWNjZW50LWNvbG9yKTsNCiAgICAgICAgICAgIGZvbnQtc2l6ZTogMS43cmVtOw0KICAgICAgICAgICAgZm9udC13ZWlnaHQ6IDkwMDsNCiAgICAgICAgICAgIG1hcmdpbi1ib3R0b206IDEuNXJlbTsNCiAgICAgICAgICAgIGRpc3BsYXk6IGZsZXg7DQogICAgICAgICAgICBhbGlnbi1pdGVtczogY2VudGVyOw0KICAgICAgICAgICAgZ2FwOiAxMHB4Ow0KICAgICAgICAgICAgZm9udC1mYW1pbHk6IHZhcigtLWZvbnQtaGVhZGluZyk7DQogICAgICAgIH0NCg0KICAgICAgICAudG9jLWxpc3QtcHJlbWl1bSB7DQogICAgICAgICAgICBsaXN0LXN0eWxlOiBub25lOw0KICAgICAgICAgICAgcGFkZGluZzogMDsNCiAgICAgICAgICAgIG1hcmdpbjogMDsNCiAgICAgICAgfQ0KDQogICAgICAgIC50b2MtbGlzdC1wcmVtaXVtIGxpIHsNCiAgICAgICAgICAgIHBvc2l0aW9uOiByZWxhdGl2ZTsNCiAgICAgICAgICAgIHBhZGRpbmctbGVmdDogMi4ycmVtOw0KICAgICAgICAgICAgbWFyZ2luLWJvdHRvbTogMTRweDsNCiAgICAgICAgICAgIHRyYW5zaXRpb246IGFsbCAwLjJzOw0KICAgICAgICAgICAgbGluZS1oZWlnaHQ6IDEuNDsNCiAgICAgICAgICAgIGRpc3BsYXk6IGZsZXg7DQogICAgICAgICAgICBhbGlnbi1pdGVtczogZmxleC1zdGFydDsNCiAgICAgICAgfQ0KDQogICAgICAgIC50b2MtbGlzdC1wcmVtaXVtIGxpLnRvYy1oMSB7DQogICAgICAgICAgICBmb250LXdlaWdodDogODAwOw0KICAgICAgICAgICAgY29sb3I6IHZhcigtLXRleHQtZGFyayk7DQogICAgICAgICAgICBmb250LXNpemU6IDEuMnJlbTsNCiAgICAgICAgfQ0KDQogICAgICAgIC50b2MtbGlzdC1wcmVtaXVtIGxpLnRvYy1oMiB7DQogICAgICAgICAgICBmb250LXdlaWdodDogNzAwOw0KICAgICAgICAgICAgY29sb3I6IHZhcigtLXRleHQtZGFyayk7DQogICAgICAgICAgICBmb250LXNpemU6IDEuMTVyZW07DQogICAgICAgIH0NCg0KICAgICAgICAudG9jLWxpc3QtcHJlbWl1bSBsaS50b2MtaDMgew0KICAgICAgICAgICAgZm9udC13ZWlnaHQ6IDUwMDsNCiAgICAgICAgICAgIGNvbG9yOiB2YXIoLS10ZXh0LW11dGVkKTsNCiAgICAgICAgICAgIGZvbnQtc2l6ZTogMS4wNXJlbTsNCiAgICAgICAgfQ0KDQogICAgICAgIC50b2MtbGlzdC1wcmVtaXVtIGxpOjpiZWZvcmUgew0KICAgICAgICAgICAgY29udGVudDogIsOi4oKswqIiOw0KICAgICAgICAgICAgY29sb3I6IHZhcigtLWFjY2VudC1jb2xvcik7DQogICAgICAgICAgICBmb250LXNpemU6IDJyZW07DQogICAgICAgICAgICBwb3NpdGlvbjogYWJzb2x1dGU7DQogICAgICAgICAgICBsZWZ0OiAwOw0KICAgICAgICAgICAgdG9wOiAtNHB4Ow0KICAgICAgICAgICAgbGluZS1oZWlnaHQ6IDE7DQogICAgICAgIH0NCg0KICAgICAgICAudG9jLWxpc3QtcHJlbWl1bSBhIHsNCiAgICAgICAgICAgIGNvbG9yOiBpbmhlcml0Ow0KICAgICAgICAgICAgdGV4dC1kZWNvcmF0aW9uOiBub25lOw0KICAgICAgICB9DQoNCiAgICAgICAgLnRvYy1saXN0LXByZW1pdW0gYTpob3ZlciB7DQogICAgICAgICAgICBjb2xvcjogdmFyKC0tYWNjZW50LWNvbG9yKTsNCiAgICAgICAgICAgIHBhZGRpbmctbGVmdDogNXB4Ow0KICAgICAgICB9DQoNCiAgICAgICAgLyogU1NTIC8gRkFRIFNUw4TCsExMRVLDhMKwICovDQogICAgICAgIC5mYXEtcHJlbWl1bSB7DQogICAgICAgICAgICBtYXJnaW4tdG9wOiA1cmVtOw0KICAgICAgICAgICAgYmFja2dyb3VuZDogdmFyKC0tYmctc29mdCk7DQogICAgICAgICAgICBib3JkZXItcmFkaXVzOiAzMHB4Ow0KICAgICAgICAgICAgcGFkZGluZzogM3JlbTsNCiAgICAgICAgfQ0KDQogICAgICAgIC5mYXEtaXRlbS1wcmVtaXVtIHsNCiAgICAgICAgICAgIGJhY2tncm91bmQ6IHdoaXRlOw0KICAgICAgICAgICAgYm9yZGVyLXJhZGl1czogMThweDsNCiAgICAgICAgICAgIG1hcmdpbi1ib3R0b206IDFyZW07DQogICAgICAgICAgICBib3JkZXI6IDFweCBzb2xpZCByZ2JhKDI5LCA0NSwgODAsIDAuMSk7DQogICAgICAgICAgICB0cmFuc2l0aW9uOiBhbGwgMC4zczsNCiAgICAgICAgfQ0KDQogICAgICAgIC5mYXEtcXVlc3Rpb24tcHJlbWl1bSB7DQogICAgICAgICAgICBwYWRkaW5nOiAxLjVyZW07DQogICAgICAgICAgICBjdXJzb3I6IHBvaW50ZXI7DQogICAgICAgICAgICBmb250LXdlaWdodDogNzAwOw0KICAgICAgICAgICAgZGlzcGxheTogZmxleDsNCiAgICAgICAgICAgIGp1c3RpZnktY29udGVudDogc3BhY2UtYmV0d2VlbjsNCiAgICAgICAgICAgIGFsaWduLWl0ZW1zOiBjZW50ZXI7DQogICAgICAgICAgICBjb2xvcjogdmFyKC0tdGV4dC1kYXJrKTsNCiAgICAgICAgfQ0KDQogICAgICAgIC5mYXEtYW5zd2VyLXByZW1pdW0gew0KICAgICAgICAgICAgcGFkZGluZzogMCAxLjVyZW07DQogICAgICAgICAgICBtYXgtaGVpZ2h0OiAwOw0KICAgICAgICAgICAgb3ZlcmZsb3c6IGhpZGRlbjsNCiAgICAgICAgICAgIHRyYW5zaXRpb246IGFsbCAwLjNzOw0KICAgICAgICAgICAgY29sb3I6IHZhcigtLXRleHQtbXV0ZWQpOw0KICAgICAgICAgICAgbGluZS1oZWlnaHQ6IDEuNzsNCiAgICAgICAgfQ0KDQogICAgICAgIC5mYXEtaXRlbS1wcmVtaXVtLmFjdGl2ZSAuZmFxLWFuc3dlci1wcmVtaXVtIHsNCiAgICAgICAgICAgIHBhZGRpbmctYm90dG9tOiAxLjVyZW07DQogICAgICAgICAgICBtYXgtaGVpZ2h0OiAxMDAwcHg7DQogICAgICAgIH0NCg0KICAgICAgICAvKiBHw4PigJNSU0VMIETDg8WTWkVOTEVNRSAqLw0KICAgICAgICAuYmxvZy1wb3N0LWNvbnRlbnQgaW1nIHsNCiAgICAgICAgICAgIGJvcmRlci1yYWRpdXM6IDIwcHg7DQogICAgICAgICAgICBib3gtc2hhZG93OiAwIDEwcHggNDBweCByZ2JhKDAsMCwwLDAuMSk7DQogICAgICAgICAgICBtYXJnaW46IDJyZW0gMDsNCiAgICAgICAgfQ0KICAgIDwvc3R5bGU+DQogICAgJzsNCg0KICAgIC8vIFRPQyBJTkpFQ1QNCiAgICAkdG9jX2l0ZW1zID0gW107DQogICAgJG1hbnVhbF90b2MgPSBqc29uX2RlY29kZSgkcG9zdFsndG9jX2RhdGEnXSwgdHJ1ZSk7DQogICAgaWYgKCFlbXB0eSgkbWFudWFsX3RvYykpIHsNCiAgICAgICAgZm9yZWFjaCAoJG1hbnVhbF90b2MgYXMgJGkgPT4gJGl0ZW0pIHsNCiAgICAgICAgICAgICRpZCA9ICdzZWN0aW9uLScgLiAoJGkgKyAxKTsNCiAgICAgICAgICAgICRsZXZlbCA9ICRpdGVtWydsZXZlbCddID86ICdoMic7DQogICAgICAgICAgICAkdG9jX2l0ZW1zW10gPSBbJ3RleHQnID0+ICRpdGVtWyd0ZXh0J10sICdpZCcgPT4gJGlkLCAnY2xhc3MnID0+ICd0b2MtJyAuICRsZXZlbF07DQogICAgICAgICAgICAkY29udGVudCA9IHByZWdfcmVwbGFjZSgnLzxoKDF8MnwzKVtePl0qPlxzKicgLiBwcmVnX3F1b3RlKCRpdGVtWyd0ZXh0J10sICcvJykgLiAnXHMqPFwvaFwxPi91aScsICI8JGxldmVsIGlkPVwiJGlkXCI+JGl0ZW1bdGV4dF08LyRsZXZlbD4iLCAkY29udGVudCwgMSk7DQogICAgICAgIH0NCiAgICB9DQoNCiAgICBpZiAoIWVtcHR5KCR0b2NfaXRlbXMpKSB7DQogICAgICAgICR0b2NfaHRtbCA9ICc8ZGl2IGNsYXNzPSJ0b2MtY29udGFpbmVyLXByZW1pdW0iPjxkaXYgY2xhc3M9InRvYy10aXRsZS1wcmVtaXVtIj7En8W44oCcwo0gw4TCsMODwqdpbmRla2lsZXI8L2Rpdj48dWwgY2xhc3M9InRvYy1saXN0LXByZW1pdW0iPic7DQogICAgICAgIGZvcmVhY2ggKCR0b2NfaXRlbXMgYXMgJGl0ZW0pDQogICAgICAgICAgICAkdG9jX2h0bWwgLj0gIjxsaSBjbGFzcz1cIiRpdGVtW2NsYXNzXVwiPjxhIGhyZWY9XCIjJGl0ZW1baWRdXCI+JGl0ZW1bdGV4dF08L2E+PC9saT4iOw0KICAgICAgICAkdG9jX2h0bWwgLj0gJzwvdWw+PC9kaXY+JzsNCiAgICAgICAgJGNvbnRlbnQgPSAkdG9jX2h0bWwgLiAkY29udGVudDsNCiAgICB9DQoNCiAgICAvLyBGQVEgSU5KRUNUDQogICAgJGZhcV9kYXRhID0ganNvbl9kZWNvZGUoJHBvc3RbJ2ZhcV9kYXRhJ10sIHRydWUpOw0KICAgIGlmICghZW1wdHkoJGZhcV9kYXRhKSkgew0KICAgICAgICAkZmFxX2h0bWwgPSAnPGRpdiBjbGFzcz0iZmFxLXByZW1pdW0iPjxoMiBzdHlsZT0idGV4dC1hbGlnbjpjZW50ZXI7IGJvcmRlcjpub25lOyBtYXJnaW4tdG9wOjAgIWltcG9ydGFudDsiPlPDhMKxa8ODwqdhIFNvcnVsYW4gU29ydWxhcjwvaDI+JzsNCiAgICAgICAgZm9yZWFjaCAoJGZhcV9kYXRhIGFzICRmKSB7DQogICAgICAgICAgICAkZmFxX2h0bWwgLj0gJzxkaXYgY2xhc3M9ImZhcS1pdGVtLXByZW1pdW0iPg0KICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9ImZhcS1xdWVzdGlvbi1wcmVtaXVtIj48c3Bhbj4nIC4gJGZbJ3EnXSAuICc8L3NwYW4+PHNwYW4gc3R5bGU9ImNvbG9yOnZhcigtLWVyZ3V2YW4tcGluaykiPis8L3NwYW4+PC9kaXY+DQogICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iZmFxLWFuc3dlci1wcmVtaXVtIj4nIC4gbmwyYnIoaHRtbHNwZWNpYWxjaGFycygkZlsnYSddKSkgLiAnPC9kaXY+DQogICAgICAgICAgICA8L2Rpdj4nOw0KICAgICAgICB9DQogICAgICAgICRmYXFfaHRtbCAuPSAnPC9kaXY+DQogICAgICAgIDxzY3JpcHQ+DQogICAgICAgICAgICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCIuZmFxLXF1ZXN0aW9uLXByZW1pdW0iKS5mb3JFYWNoKHEgPT4gew0KICAgICAgICAgICAgICAgIHEub25jbGljayA9ICgpID0+IHEucGFyZW50RWxlbWVudC5jbGFzc0xpc3QudG9nZ2xlKCJhY3RpdmUiKTsNCiAgICAgICAgICAgIH0pOw0KICAgICAgICA8L3NjcmlwdD4nOw0KICAgICAgICAkY29udGVudCAuPSAkZmFxX2h0bWw7DQogICAgfQ0KDQogICAgJGNvbnRlbnQgPSAkc3R5bGUgLiAkY29udGVudDsNCn0NCg0KaW5qZWN0UHJlbWl1bURlc2lnbigkcG9zdFsnY29udGVudCddLCAkcG9zdCk7DQppbmNsdWRlICcuLi9pbmNsdWRlcy9oZWFkZXIucGhwJzsNCj8+DQo8YXJ0aWNsZSBjbGFzcz0iYmxvZy1wb3N0LXdyYXBwZXIiPg0KICAgIDxoZWFkZXIgc3R5bGU9Im1hcmdpbi1ib3R0b206IDNyZW07IHRleHQtYWxpZ246Y2VudGVyOyI+DQogICAgICAgIDxuYXYgc3R5bGU9Im1hcmdpbi1ib3R0b206IDJyZW07Ij48YSBocmVmPSI8P3BocCBlY2hvIHBhZ2VfdXJsKCdibG9nLnBocCcpOyA/PiINCiAgICAgICAgICAgICAgICBzdHlsZT0iY29sb3I6dmFyKC0tZXJndXZhbi1waW5rKTsgdGV4dC1kZWNvcmF0aW9uOm5vbmU7IGZvbnQtd2VpZ2h0OjYwMDsiPsOi4oCgwpAgQmxvZyBMaXN0ZXNpbmUgRMODwrZuPC9hPjwvbmF2Pg0KICAgICAgICA8c3Bhbg0KICAgICAgICAgICAgc3R5bGU9ImJhY2tncm91bmQ6dmFyKC0tZXJndXZhbi1saWdodC1waW5rKTsgY29sb3I6dmFyKC0tZXJndXZhbi1waW5rKTsgcGFkZGluZzogOHB4IDE2cHg7IGJvcmRlci1yYWRpdXM6MzBweDsgZm9udC13ZWlnaHQ6NzAwOyBmb250LXNpemU6IDAuOXJlbTsgdGV4dC10cmFuc2Zvcm06dXBwZXJjYXNlOyBsZXR0ZXItc3BhY2luZzoxcHg7Ij48P3BocCBlY2hvIGh0bWxzcGVjaWFsY2hhcnMoJHBvc3RbJ2NhdGVnb3J5J10pOyA/Pjwvc3Bhbj4NCiAgICAgICAgPGgxDQogICAgICAgICAgICBzdHlsZT0iZm9udC1zaXplOiAyLjhyZW07IGZvbnQtd2VpZ2h0OiA5MDA7IGNvbG9yOiAjMWUyOTNiOyBtYXJnaW4tdG9wOiAxLjVyZW07IGxpbmUtaGVpZ2h0OiAxLjI7IGZvbnQtZmFtaWx5OiBcJ1BsYXlmYWlyIERpc3BsYXlcJywgc2VyaWY7Ij4NCiAgICAgICAgICAgIDw/cGhwIGVjaG8gaHRtbHNwZWNpYWxjaGFycygkcG9zdFsndGl0bGUnXSk7ID8+DQogICAgICAgIDwvaDE+DQogICAgICAgIDxkaXYgc3R5bGU9Im1hcmdpbi10b3A6IDEuNXJlbTsgY29sb3I6ICM2NDc0OGI7IGZvbnQtd2VpZ2h0OiA1MDA7Ij4NCiAgICAgICAgICAgIDxzcGFuPsOiwo/CsSA8P3BocCBlY2hvIGh0bWxzcGVjaWFsY2hhcnMoJHBvc3RbJ3JlYWRpbmdfdGltZSddKTsgPz4gb2t1bWE8L3NwYW4+DQogICAgICAgICAgICA8c3BhbiBzdHlsZT0ibWFyZ2luOiAwIDE1cHg7Ij7DouKCrMKiPC9zcGFuPg0KICAgICAgICAgICAgPHNwYW4+xJ/FuOKAnOKApiA8P3BocCBlY2hvIGRhdGUoJ2QubS5ZJywgc3RydG90aW1lKCRwb3N0WydjcmVhdGVkX2F0J10pKTsgPz48L3NwYW4+DQogICAgICAgIDwvZGl2Pg0KICAgIDwvaGVhZGVyPg0KDQogICAgPGRpdiBzdHlsZT0ibWFyZ2luLWJvdHRvbTogNHJlbTsiPg0KICAgICAgICA8aW1nIHNyYz0iPD9waHAgZWNobyB3ZWJwX3VybCgkcG9zdFsnaW1hZ2UnXSk7ID8+Ig0KICAgICAgICAgICAgc3R5bGU9IndpZHRoOjEwMCU7IGJvcmRlci1yYWRpdXM6MzBweDsgYm94LXNoYWRvdzogMCAyNXB4IDUwcHggLTEycHggcmdiYSgwLDAsMCwwLjE1KTsiDQogICAgICAgICAgICBhbHQ9Ijw/cGhwIGVjaG8gaHRtbHNwZWNpYWxjaGFycygkcG9zdFsndGl0bGUnXSk7ID8+Ij4NCiAgICA8L2Rpdj4NCg0KICAgIDxkaXYgY2xhc3M9ImJsb2ctcG9zdC1jb250ZW50Ij4NCiAgICAgICAgPD9waHAgZWNobyAkcG9zdFsnY29udGVudCddOyA/Pg0KICAgIDwvZGl2Pg0KDQogICAgPD9waHAgaWYgKCFlbXB0eSgkcG9zdFsndGFncyddKSk6ID8+DQogICAgICAgIDxkaXYgc3R5bGU9Im1hcmdpbi10b3A6IDNyZW07IHBhZGRpbmctdG9wOiAycmVtOyBib3JkZXItdG9wOiAxcHggc29saWQgI2VlZTsiPg0KICAgICAgICAgICAgPGg0DQogICAgICAgICAgICAgICAgc3R5bGU9ImZvbnQtc2l6ZTogMS4ycmVtOyBmb250LXdlaWdodDogNzAwOyBjb2xvcjogIzFlMjkzYjsgbWFyZ2luLWJvdHRvbTogMXJlbTsgZm9udC1mYW1pbHk6dmFyKC0tZm9udC1oZWFkaW5nKTsgZGlzcGxheTpmbGV4OyBhbGlnbi1pdGVtczpjZW50ZXI7IGdhcDoxMHB4OyI+DQogICAgICAgICAgICAgICAgPHNwYW4gc3R5bGU9ImNvbG9yOnZhcigtLWVyZ3V2YW4tcGluayk7Ij4jPC9zcGFuPiBLb251IEV0aWtldGxlcmkNCiAgICAgICAgICAgIDwvaDQ+DQogICAgICAgICAgICA8ZGl2IHN0eWxlPSJkaXNwbGF5OiBmbGV4OyBmbGV4LXdyYXA6IHdyYXA7IGdhcDogMTBweDsiPg0KICAgICAgICAgICAgICAgIDw/cGhwDQogICAgICAgICAgICAgICAgJHRhZ3MgPSBleHBsb2RlKCcsJywgJHBvc3RbJ3RhZ3MnXSk7DQogICAgICAgICAgICAgICAgZm9yZWFjaCAoJHRhZ3MgYXMgJHRhZyk6DQogICAgICAgICAgICAgICAgICAgICR0YWcgPSB0cmltKCR0YWcpOw0KICAgICAgICAgICAgICAgICAgICBpZiAoISR0YWcpDQogICAgICAgICAgICAgICAgICAgICAgICBjb250aW51ZTsNCiAgICAgICAgICAgICAgICAgICAgPz4NCiAgICAgICAgICAgICAgICAgICAgPGEgaHJlZj0iPD9waHAgZWNobyB1cmwoJ2V0aWtldC8nIC4gc2x1Z2lmeSgkdGFnKSk7ID8+Ig0KICAgICAgICAgICAgICAgICAgICAgICAgc3R5bGU9ImJhY2tncm91bmQ6IHdoaXRlOyBib3JkZXI6IDFweCBzb2xpZCAjZTJlOGYwOyBjb2xvcjogIzY0NzQ4YjsgcGFkZGluZzogOHB4IDIwcHg7IGJvcmRlci1yYWRpdXM6IDUwcHg7IHRleHQtZGVjb3JhdGlvbjogbm9uZTsgZm9udC1zaXplOiAwLjlyZW07IHRyYW5zaXRpb246IGFsbCAwLjJzOyBmb250LXdlaWdodDo1MDA7IGRpc3BsYXk6aW5saW5lLWJsb2NrOyI+DQogICAgICAgICAgICAgICAgICAgICAgICA8P3BocCBlY2hvIGh0bWxzcGVjaWFsY2hhcnMoJHRhZyk7ID8+DQogICAgICAgICAgICAgICAgICAgIDwvYT4NCiAgICAgICAgICAgICAgICA8P3BocCBlbmRmb3JlYWNoOyA/Pg0KICAgICAgICAgICAgPC9kaXY+DQogICAgICAgICAgICA8c3R5bGU+DQogICAgICAgICAgICAgICAgYVtocmVmKj0iZXRpa2V0Il06aG92ZXIgew0KICAgICAgICAgICAgICAgICAgICBib3JkZXItY29sb3I6IHZhcigtLWVyZ3V2YW4tcGluaykgIWltcG9ydGFudDsNCiAgICAgICAgICAgICAgICAgICAgY29sb3I6IHZhcigtLWVyZ3V2YW4tcGluaykgIWltcG9ydGFudDsNCiAgICAgICAgICAgICAgICAgICAgYmFja2dyb3VuZDogdmFyKC0tZXJndXZhbi1saWdodC1waW5rKSAhaW1wb3J0YW50Ow0KICAgICAgICAgICAgICAgICAgICB0cmFuc2Zvcm06IHRyYW5zbGF0ZVkoLTJweCk7DQogICAgICAgICAgICAgICAgICAgIGJveC1zaGFkb3c6IDAgNHB4IDEycHggcmdiYSgxMzksIDYxLCA3MiwgMC4xKTsNCiAgICAgICAgICAgICAgICB9DQogICAgICAgICAgICA8L3N0eWxlPg0KICAgICAgICA8L2Rpdj4NCiAgICA8P3BocCBlbmRpZjsgPz4NCjwvYXJ0aWNsZT4NCjw/cGhwIGluY2x1ZGUgJy4uL2luY2x1ZGVzL2Zvb3Rlci5waHAnOyA/Pg==')
        ];

        foreach($filesToRescue as $path => $content) {
            $fullPath = __DIR__. '/'.$path;
            if (file_put_contents($fullPath, $content)) {
                echo "<div style='background:green;color:white;padding:10px;margin:5px;'>✅ Dosya kurtarıldı: $path</div>";
            } else {
                echo "<div style='background:red;color:white;padding:10px;margin:5px;'>❌ Dosya yazılamadı: $path (Yazma izni yok mu?)</div>";
            }
        }
    }
    // --- RESCUE OPERATION END ---

    function scanTOC() {
        const container = document.getElementById('tocList');
        container.innerHTML = '';

        // Quill editor elementlerini doğrudan tara (Regex yerine DOM kullan)
        const headings = quill.root.querySelectorAll('h2, h3');

        if (headings.length === 0) {
            alert('⚠️ İçerikte H2 veya H3 başlık bulunamadı.\nLütfen metindeki başlıkları seçip editör araç çubuğundan "Başlık" olarak işaretleyin.');
            return;
        }

        let count = 0;
        headings.forEach(h => {
            const text = h.innerText.trim();
            if (text.length > 2) {
                addTocRow(text, h.tagName.toLowerCase());
                count++;
            }
        });

        if (count > 0) {
            // alert('✅ ' + count + ' başlık bulundu.');
        }
    }

    function scanFAQ() {
        const container = document.getElementById('faqList');
        // Satır satır ayır (Boş satırları koruyarak)
        const lines = quill.root.innerText.split('\n');

        const questions = [];
        let currentQ = null;
        let currentA = [];

        lines.forEach(line => {
            line = line.trim();
            if (!line) return; // Boş satırları atla

            // Soru işareti ile biten satır -> Yeni Soru
            if (line.endsWith('?') && line.length > 10 && line.length < 150) {
                // Önceki soru varsa kaydet
                if (currentQ) {
                    questions.push({ q: currentQ, a: currentA.join(' ') });
                }
                currentQ = line;
                currentA = []; // Cevabı sıfırla
            }
            // Soru değilse ve aktif bir soru varsa -> Cevap satırı
            else if (currentQ) {
                currentA.push(line);
            }
        });

        // Son soruyu da ekle
        if (currentQ) {
            questions.push({ q: currentQ, a: currentA.join(' ') });
        }

        if (questions.length === 0) {
            alert('⚠️ Soru formatında cümle bulunamadı.\nSSS oluşturmak için satırların sonuna soru işareti (?) koyduğunuzdan emin olun.');
            return;
        }

        container.innerHTML = '';
        questions.forEach(item => {
            addFaqRow(item.q, item.a);
        });
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