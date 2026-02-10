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
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Geçersiz istek!';
    } else {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $image = $_POST['image'] ?? '';
    $display_order = (int)($_POST['display_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Eğer dosya yüklendiyse, önce yükle
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = handleImageUpload($_FILES['image_file'], 'office');
        if ($uploadResult['success']) {
            $image = $uploadResult['url'];
        } else {
            $error = $uploadResult['message'];
        }
    }
    
    if ($image && !$error) {
        try {
            $stmt = $db->prepare("INSERT INTO office_images (title, description, image, display_order, is_active, created_at) 
                                   VALUES (:title, :description, :image, :display_order, :is_active, NOW())");
            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':image' => $image,
                ':display_order' => $display_order,
                ':is_active' => $is_active
            ]);
            redirect(admin_url('pages/office-images.php'));
        } catch (PDOException $e) {
            $error = 'Hata: ' . $e->getMessage();
        }
    } else {
        if (!$error) {
            $error = 'Lütfen görsel yükleyin!';
        }
    }
    }
}

$page = 'office-images';
$page_title = 'Yeni Ofis Görseli Ekle';
require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($error): ?>
    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    
    <div class="form-group">
        <label for="title">Başlık (Opsiyonel)</label>
        <input type="text" id="title" name="title" class="form-control" placeholder="Örn: Bekleme Odası">
    </div>
    
    <div class="form-group">
        <label for="description">Açıklama (Opsiyonel)</label>
        <textarea id="description" name="description" class="form-control" rows="3" placeholder="Görsel hakkında kısa bir açıklama"></textarea>
    </div>
    
    <div class="form-group">
        <label for="image_file">Görsel Yükle *</label>
        <div class="image-upload-wrapper">
            <input type="file" id="image_file" name="image_file" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" class="form-control" onchange="handleFileUpload(this)">
            <input type="hidden" id="image" name="image" required>
            <div id="uploadStatus" style="margin-top: 10px; display: none;"></div>
        </div>
        <div id="imagePreview" style="margin-top: 10px; display: none;">
            <img id="previewImg" src="" alt="Önizleme" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #e2e8f0;">
        </div>
        <small style="color: #64748b; font-size: 12px;">JPG, PNG, GIF veya WebP formatında, maksimum 5MB. Görsel otomatik olarak WebP formatına çevrilecektir.</small>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="display_order">Sıralama</label>
            <input type="number" id="display_order" name="display_order" class="form-control" value="0" min="0">
            <small style="color: #64748b;">Küçük sayılar önce gösterilir</small>
        </div>
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 10px; margin-top: 30px;">
                <input type="checkbox" name="is_active" checked>
                <span>Aktif</span>
            </label>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 6px;">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            Kaydet
        </button>
        <a href="<?php echo admin_url('pages/office-images.php'); ?>" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 6px;">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            İptal
        </a>
    </div>
</form>

<script>
// Görsel yükleme
function handleFileUpload(input) {
    const file = input.files[0];
    if (!file) return;

    // Dosya boyutu kontrolü
    if (file.size > 5 * 1024 * 1024) {
        showUploadStatus('Dosya boyutu 5MB\'dan büyük olamaz.', 'error');
        input.value = '';
        return;
    }

    // Önizleme göster
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('imagePreview');
        const img = document.getElementById('previewImg');
        img.src = e.target.result;
        preview.style.display = 'block';
    };
    reader.readAsDataURL(file);

    // AJAX ile yükle
    const formData = new FormData();
    formData.append('image', file);

    showUploadStatus('Yükleniyor...', 'loading');

    fetch('<?php echo admin_url('pages/upload-image.php?folder=office'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('image').value = data.url;
            showUploadStatus('Görsel başarıyla yüklendi ve WebP formatına çevrildi!', 'success');
        } else {
            showUploadStatus(data.message || 'Yükleme hatası!', 'error');
            input.value = '';
        }
    })
    .catch(error => {
        showUploadStatus('Bir hata oluştu: ' + error.message, 'error');
        input.value = '';
    });
}

function showUploadStatus(message, type) {
    const statusDiv = document.getElementById('uploadStatus');
    statusDiv.style.display = 'block';
    statusDiv.style.padding = '8px 12px';
    statusDiv.style.borderRadius = '6px';
    statusDiv.style.fontSize = '14px';
    
    if (type === 'success') {
        statusDiv.style.background = '#d1fae5';
        statusDiv.style.color = '#065f46';
        statusDiv.style.border = '1px solid #10b981';
    } else if (type === 'error') {
        statusDiv.style.background = '#fee2e2';
        statusDiv.style.color = '#991b1b';
        statusDiv.style.border = '1px solid #ef4444';
    } else {
        statusDiv.style.background = '#dbeafe';
        statusDiv.style.color = '#1e40af';
        statusDiv.style.border = '1px solid #3b82f6';
    }
    
    statusDiv.textContent = message;
    
    if (type === 'success') {
        setTimeout(() => {
            statusDiv.style.display = 'none';
        }, 3000);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>





