<?php 
// Session'ı sadece bir kez başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../database/db.php';
require_once __DIR__ . '/../includes/upload-handler.php';

// Giriş kontrolü
requireLogin();

$db = getDB();
$error = '';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    redirect(admin_url('pages/certificates.php'));
}

// Sertifikayı getir
$stmt = $db->prepare("SELECT * FROM certificates WHERE id = ?");
$stmt->execute([$id]);
$certificate = $stmt->fetch();

if (!$certificate) {
    redirect(admin_url('pages/certificates.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Geçersiz istek!';
    } else {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $image = $_POST['image'] ?? $certificate['image']; // Mevcut görseli koru
        $display_order = (int)($_POST['display_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Eğer yeni dosya yüklendiyse
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            // Eski görseli sil
            if ($certificate['image']) {
                $old_image_path = __DIR__ . '/../../' . ltrim($certificate['image'], '/');
                if (file_exists($old_image_path)) {
                    @unlink($old_image_path);
                }
            }
            
            $uploadResult = handleImageUpload($_FILES['image_file'], 'certificates');
            if ($uploadResult['success']) {
                $image = $uploadResult['url'];
            } else {
                $error = $uploadResult['message'];
            }
        }
        
        if ($title && $image && !$error) {
            try {
                $stmt = $db->prepare("UPDATE certificates SET 
                                       title = :title, 
                                       description = :description, 
                                       image = :image, 
                                       display_order = :display_order,
                                       is_active = :is_active
                                       WHERE id = :id");
                $stmt->execute([
                    ':title' => $title,
                    ':description' => $description,
                    ':image' => $image,
                    ':display_order' => $display_order,
                    ':is_active' => $is_active,
                    ':id' => $id
                ]);
                redirect(admin_url('pages/certificates.php'));
            } catch (PDOException $e) {
                $error = 'Hata: ' . $e->getMessage();
            }
        } else {
            if (!$error) {
                $error = 'Lütfen gerekli alanları doldurun!';
            }
        }
    }
}

$page = 'certificates';
$page_title = 'Sertifika Düzenle';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <h1>Sertifika Düzenle</h1>
        <a href="<?php echo admin_url('pages/certificates.php'); ?>" class="btn btn-secondary">Geri Dön</a>
    </div>

    <div style="margin-bottom: 20px; padding: 15px; background: #f8fafc; border-radius: 8px;">
        <p style="margin: 0; color: #64748b; font-size: 14px;">
            <strong>ID:</strong> <?php echo $certificate['id']; ?> | 
            <strong>Oluşturulma:</strong> <?php echo date('d.m.Y H:i', strtotime($certificate['created_at'])); ?> | 
            <strong>Son Güncelleme:</strong> <?php echo date('d.m.Y H:i', strtotime($certificate['updated_at'])); ?>
        </p>
    </div>

    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <?php echo csrfField(); ?>
        
        <div class="form-group">
            <label for="title">Başlık *</label>
            <input type="text" id="title" name="title" class="form-control" required value="<?php echo htmlspecialchars($certificate['title']); ?>">
        </div>
        
        <div class="form-group">
            <label for="description">Açıklama</label>
            <textarea id="description" name="description" class="form-control" rows="3"><?php echo htmlspecialchars($certificate['description'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="image_file">Görsel</label>
            <div class="image-upload-wrapper">
                <input type="file" id="image_file" name="image_file" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" class="form-control" onchange="handleFileUpload(this)">
                <input type="hidden" id="image" name="image" value="<?php echo htmlspecialchars($certificate['image']); ?>" required>
                <div id="uploadStatus" style="margin-top: 10px; display: none;"></div>
            </div>
            <div id="imagePreview" style="margin-top: 10px;">
                <img id="previewImg" src="<?php echo url($certificate['image']); ?>" alt="Mevcut Görsel" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #e2e8f0;">
            </div>
            <small style="color: #64748b; font-size: 12px;">Yeni görsel yüklemek için dosya seçin. JPG, PNG, GIF veya WebP formatında, maksimum 5MB</small>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="display_order">Görüntülenme Sırası</label>
                <input type="number" id="display_order" name="display_order" class="form-control" value="<?php echo $certificate['display_order']; ?>" min="0">
                <small style="color: #64748b; font-size: 12px;">Düşük sayı önce görünür</small>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" <?php echo $certificate['is_active'] ? 'checked' : ''; ?>> Aktif
                </label>
                <small style="color: #64748b; font-size: 12px; display: block; margin-top: 5px;">Aktif olmayan sertifikalar ana sayfada görünmez</small>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Güncelle
            </button>
            <a href="<?php echo admin_url('pages/certificates.php'); ?>" class="btn btn-secondary">İptal</a>
        </div>
    </form>
</div>

<script>
function handleFileUpload(input) {
    const file = input.files[0];
    if (!file) return;
    
    const formData = new FormData();
    formData.append('image', file);
    
    const statusDiv = document.getElementById('uploadStatus');
    const previewImg = document.getElementById('previewImg');
    const imageInput = document.getElementById('image');
    
    statusDiv.style.display = 'block';
    statusDiv.innerHTML = '<span style="color: #6366f1;">Yükleniyor...</span>';
    
    fetch('<?php echo admin_url('pages/upload-image.php'); ?>?folder=certificates', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusDiv.innerHTML = '<span style="color: #10b981;">✓ Yükleme başarılı!</span>';
            imageInput.value = data.url;
            previewImg.src = data.url;
        } else {
            statusDiv.innerHTML = '<span style="color: #ef4444;">✗ ' + data.message + '</span>';
        }
    })
    .catch(error => {
        statusDiv.innerHTML = '<span style="color: #ef4444;">✗ Yükleme hatası!</span>';
        console.error('Error:', error);
    });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
