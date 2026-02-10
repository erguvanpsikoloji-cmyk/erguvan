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

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    redirect(admin_url('pages/sliders.php'));
}

// Slider'ı getir
$stmt = $db->prepare("SELECT * FROM sliders WHERE id = ?");
$stmt->execute([$id]);
$slider = $stmt->fetch();

if (!$slider) {
    redirect(admin_url('pages/sliders.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Geçersiz istek!';
    } else {
    $title = $_POST['title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $description = $_POST['description'] ?? '';
    $image = $_POST['image'] ?? '';
    $button_text = $_POST['button_text'] ?? '';
    $button_link = $_POST['button_link'] ?? '';
    $display_order = (int)($_POST['display_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Eğer dosya yüklendiyse, önce yükle
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = handleImageUpload($_FILES['image_file'], 'sliders');
        if ($uploadResult['success']) {
            $image = $uploadResult['url'];
        } else {
            $error = $uploadResult['message'];
        }
    }
    
    if ($title && $image && !$error) {
        try {
            $stmt = $db->prepare("UPDATE sliders SET 
                                   title = :title, 
                                   subtitle = :subtitle, 
                                   description = :description, 
                                   image = :image, 
                                   button_text = :button_text, 
                                   button_link = :button_link, 
                                   display_order = :display_order,
                                   is_active = :is_active
                                   WHERE id = :id");
            $stmt->execute([
                ':title' => $title,
                ':subtitle' => $subtitle,
                ':description' => $description,
                ':image' => $image,
                ':button_text' => $button_text,
                ':button_link' => $button_link,
                ':display_order' => $display_order,
                ':is_active' => $is_active,
                ':id' => $id
            ]);
            redirect(admin_url('pages/sliders.php'));
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

$page = 'sliders';
$page_title = 'Slider Düzenle';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="edit-header">
    <div>
        <h2 style="margin: 0 0 5px 0;">Slider Düzenle</h2>
        <p style="color: #64748b; margin: 0;">ID: <?php echo $slider['id']; ?> | Oluşturulma: <?php echo date('d.m.Y H:i', strtotime($slider['created_at'])); ?></p>
    </div>
</div>

<?php if ($error): ?>
    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST" action="" id="sliderForm" enctype="multipart/form-data">
    <?php echo csrfField(); ?>
    <div class="form-row">
        <div class="form-group">
            <label for="title">Başlık *</label>
            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($slider['title']); ?>" required>
        </div>
        <div class="form-group">
            <label for="subtitle">Alt Başlık</label>
            <input type="text" id="subtitle" name="subtitle" class="form-control" value="<?php echo htmlspecialchars($slider['subtitle'] ?? ''); ?>">
        </div>
    </div>
    
    <div class="form-group">
        <label for="description">Açıklama</label>
        <textarea id="description" name="description" class="form-control" rows="3"><?php echo htmlspecialchars($slider['description'] ?? ''); ?></textarea>
    </div>
    
    <div class="form-group">
        <label for="image_file">Görsel Yükle *</label>
        <div class="image-upload-wrapper">
            <input type="file" id="image_file" name="image_file" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" class="form-control" onchange="handleFileUpload(this)">
            <input type="hidden" id="image" name="image" value="<?php echo htmlspecialchars($slider['image']); ?>" required>
            <div id="uploadStatus" style="margin-top: 10px; display: none;"></div>
        </div>
        <div id="imagePreview" style="margin-top: 10px; <?php echo $slider['image'] ? 'display: block;' : 'display: none;'; ?>">
            <img id="previewImg" src="<?php echo htmlspecialchars($slider['image']); ?>" alt="Önizleme" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #e2e8f0;">
        </div>
        <small style="color: #64748b; font-size: 12px;">JPG, PNG, GIF veya WebP formatında, maksimum 5MB</small>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="button_text">Buton Metni</label>
            <input type="text" id="button_text" name="button_text" class="form-control" value="<?php echo htmlspecialchars($slider['button_text'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="button_link">Buton Linki</label>
            <input type="text" id="button_link" name="button_link" class="form-control" value="<?php echo htmlspecialchars($slider['button_link'] ?? ''); ?>">
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="display_order">Sıralama</label>
            <input type="number" id="display_order" name="display_order" class="form-control" value="<?php echo $slider['display_order']; ?>" min="0">
            <small style="color: #64748b;">Küçük sayılar önce gösterilir</small>
        </div>
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 10px; margin-top: 30px;">
                <input type="checkbox" name="is_active" <?php echo $slider['is_active'] ? 'checked' : ''; ?>>
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
            Güncelle
        </button>
        <a href="<?php echo admin_url('pages/sliders.php'); ?>" class="btn btn-secondary">
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

    fetch('<?php echo admin_url('pages/upload-image.php'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('image').value = data.url;
            showUploadStatus('Görsel başarıyla yüklendi!', 'success');
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
