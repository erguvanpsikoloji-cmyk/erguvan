<?php
// admin/pages/about.php
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

// Tek bir kayıt olduğu için ID=1 varsayıyoruz veya ilk kaydı alıyoruz
$stmt = $db->query("SELECT * FROM about_us LIMIT 1");
$about = $stmt->fetch();

// Eğer kayıt yoksa oluştur (güvenlik ağı)
if (!$about) {
    $db->exec("INSERT INTO about_us (title, content) VALUES ('Hakkımızda', '')");
    $about = $db->query("SELECT * FROM about_us LIMIT 1")->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Geçersiz istek!';
    } else {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $meta_title = $_POST['meta_title'] ?? '';
        $meta_description = $_POST['meta_description'] ?? '';

        // Görsel İşleme
        $image = $_POST['current_image'] ?? '';

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = handleImageUpload($_FILES['image'], 'office'); // office klasörünü kullanalım veya genel
            if ($uploadResult['success']) {
                $image = $uploadResult['url'];
            } else {
                $error = $uploadResult['message'];
            }
        }

        if (!$error) {
            try {
                $stmt = $db->prepare("UPDATE about_us SET 
                                       title = :title, 
                                       content = :content, 
                                       image = :image, 
                                       meta_title = :meta_title,
                                       meta_description = :meta_description,
                                       updated_at = NOW()
                                       WHERE id = :id");
                $stmt->execute([
                    ':title' => $title,
                    ':content' => $content,
                    ':image' => $image,
                    ':meta_title' => $meta_title,
                    ':meta_description' => $meta_description,
                    ':id' => $about['id']
                ]);

                $success = true;
                // Güncel veriyi yansıt
                $about['title'] = $title;
                $about['content'] = $content;
                $about['image'] = $image;
                $about['meta_title'] = $meta_title;
                $about['meta_description'] = $meta_description;

            } catch (PDOException $e) {
                $error = 'Veritabanı hatası: ' . $e->getMessage();
            }
        }
    }
}

$page = 'about';
$page_title = 'Hakkımızda Düzenle';
require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($error): ?>
    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="success-message"
        style="background: #efe; border: 1px solid #cfc; color: #3c3; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        <strong>✅ Başarılı:</strong> Hakkımızda sayfası güncellendi!
    </div>
<?php endif; ?>

<div class="edit-header">
    <h2 style="margin: 0;">Hakkımızda Sayfasını Düzenle</h2>
    <a href="<?php echo url('hakkimizda'); // Frontend url'i tahminidir ?>" target="_blank"
        class="btn btn-secondary">👁️ Sitede Gör</a>
</div>

<form method="POST" action="" id="aboutForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>

    <div class="form-group">
        <label for="title">Sayfa Başlığı *</label>
        <input type="text" id="title" name="title" class="form-control"
            value="<?php echo htmlspecialchars($about['title']); ?>" required>
    </div>

    <div class="form-group">
        <label for="image">Ana Görsel</label>
        <div class="image-input-group">
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($about['image']); ?>">
            <input type="file" id="image" name="image" class="form-control"
                accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" onchange="previewImageFile(this)">
        </div>
        <div id="imagePreview"
            style="margin-top: 10px; <?php echo $about['image'] ? 'display: block;' : 'display: none;'; ?>">
            <p style="margin-bottom: 5px; font-size: 12px; font-weight: bold;">Mevcut / Seçilen Görsel:</p>
            <img id="previewImg" src="<?php echo htmlspecialchars($about['image']); ?>" alt="Önizleme"
                style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #e2e8f0;">
        </div>
    </div>

    <div class="form-group">
        <label for="content">İçerik *</label>
        <div id="content" style="min-height: 400px; background: white; border: 1px solid #e2e8f0; border-radius: 8px;">
            <?php echo $about['content']; ?>
        </div>
        <textarea name="content" style="display: none;"
            required><?php echo htmlspecialchars($about['content']); ?></textarea>
    </div>

    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #e2e8f0;">
    <h3 style="font-size: 16px; margin-bottom: 15px; color: #475569;">SEO Ayarları</h3>

    <div class="form-group">
        <label for="meta_title">Meta Başlık (Title)</label>
        <input type="text" id="meta_title" name="meta_title" class="form-control"
            value="<?php echo htmlspecialchars($about['meta_title']); ?>"
            placeholder="Tarayıcı sekmesinde görünecek başlık">
    </div>

    <div class="form-group">
        <label for="meta_description">Meta Açıklama (Description)</label>
        <textarea id="meta_description" name="meta_description" class="form-control" rows="2"
            placeholder="Google arama sonuçlarında görünecek kısa açıklama"><?php echo htmlspecialchars($about['meta_description']); ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Kaydet</button>
    </div>
</form>

<script>
    // Görsel önizleme
    function previewImageFile(input) {
        const preview = document.getElementById('imagePreview');
        const img = document.getElementById('previewImg');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                img.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Quill.js
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Quill !== 'undefined') {
            const quill = new Quill('#content', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                placeholder: 'İçerik buraya...',
                });

            // Form gönderilmeden önce Quill içeriğini textarea'ya aktar
            document.getElementById('aboutForm').addEventListener('submit', function (e) {
                const content = document.querySelector('#content .ql-editor').innerHTML;
                document.querySelector('textarea[name="content"]').value = content;
            });
        } else {
            // Fallback: Editör yüklenemezse normal kutuyu göster
            console.warn('Quill editör yüklenemedi, düz metin modu aktif.');
            document.getElementById('content').style.display = 'none';
            const textarea = document.querySelector('textarea[name="content"]');
            textarea.style.display = 'block';
            textarea.style.width = '100%';
            textarea.style.minHeight = '400px';
            textarea.style.padding = '10px';
            textarea.style.border = '1px solid #ddd';
            textarea.style.borderRadius = '5px';
        }
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
