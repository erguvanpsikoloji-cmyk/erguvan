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

$db = getDB();
$error = '';

// Slug oluşturma fonksiyonu
function createSlug($text) {
    $turkish = ['ş', 'Ş', 'ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'ç', 'Ç'];
    $english = ['s', 's', 'i', 'i', 'g', 'g', 'u', 'u', 'o', 'o', 'c', 'c'];
    $text = str_replace($turkish, $english, $text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Geçersiz istek!';
    } else {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $icon = trim($_POST['icon'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $features = trim($_POST['features'] ?? '');
        $display_order = (int)($_POST['display_order'] ?? 0);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Slug oluştur (eğer boşsa)
        if (empty($slug) && !empty($title)) {
            $slug = createSlug($title);
        }
        
        // Slug kontrolü (benzersiz olmalı)
        if (!empty($slug)) {
            $stmt = $db->prepare("SELECT id FROM services WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetch()) {
                $slug = $slug . '-' . time();
            }
        }
        
        if ($title && $slug && !$error) {
            try {
                $stmt = $db->prepare("INSERT INTO services (title, slug, icon, description, features, display_order, is_featured, is_active, created_at) 
                                       VALUES (:title, :slug, :icon, :description, :features, :display_order, :is_featured, :is_active, NOW())");
                $stmt->execute([
                    ':title' => $title,
                    ':slug' => $slug,
                    ':icon' => $icon,
                    ':description' => $description,
                    ':features' => $features,
                    ':display_order' => $display_order,
                    ':is_featured' => $is_featured,
                    ':is_active' => $is_active
                ]);
                redirect(admin_url('pages/services.php'));
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

$page = 'services';
$page_title = 'Yeni Hizmet Ekle';
require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($error): ?>
    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST" action="">
    <?php echo csrfField(); ?>
    <div class="form-row">
        <div class="form-group">
            <label for="title">Başlık *</label>
            <input type="text" id="title" name="title" class="form-control" required oninput="updateSlug(this.value)">
        </div>
        <div class="form-group">
            <label for="slug">Slug *</label>
            <input type="text" id="slug" name="slug" class="form-control" required>
            <small style="color: #64748b;">URL için kullanılacak benzersiz tanımlayıcı (otomatik oluşturulur)</small>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="icon">İkon</label>
            <input type="text" id="icon" name="icon" class="form-control" placeholder="👤 veya emoji">
            <small style="color: #64748b;">Emoji veya ikon kodu (örn: 👤, 💻, 💑)</small>
        </div>
        <div class="form-group">
            <label for="display_order">Sıralama</label>
            <input type="number" id="display_order" name="display_order" class="form-control" value="0" min="0">
            <small style="color: #64748b;">Küçük sayılar önce gösterilir</small>
        </div>
    </div>
    
    <div class="form-group">
        <label for="description">Açıklama</label>
        <textarea id="description" name="description" class="form-control" rows="4" placeholder="Hizmet hakkında kısa açıklama..."></textarea>
    </div>
    
    <div class="form-group">
        <label for="features">Özellikler (Her satıra bir özellik)</label>
        <textarea id="features" name="features" class="form-control" rows="6" placeholder="✓ 50 dakika seans&#10;✓ Kişiselleştirilmiş yaklaşım&#10;✓ Gizlilik garantisi"></textarea>
        <small style="color: #64748b;">Her satıra bir özellik yazın (✓ işareti ile başlayabilirsiniz)</small>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 10px; margin-top: 30px;">
                <input type="checkbox" name="is_featured">
                <span>Öne Çıkan Hizmet</span>
            </label>
            <small style="color: #64748b;">Öne çıkan hizmetler özel olarak vurgulanır</small>
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
        <a href="<?php echo admin_url('pages/services.php'); ?>" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 6px;">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            İptal
        </a>
    </div>
</form>

<script>
// Başlıktan otomatik slug oluştur
function updateSlug(title) {
    if (!document.getElementById('slug').value) {
        var slug = title.toLowerCase()
            .replace(/ş/g, 's')
            .replace(/Ş/g, 's')
            .replace(/ı/g, 'i')
            .replace(/İ/g, 'i')
            .replace(/ğ/g, 'g')
            .replace(/Ğ/g, 'g')
            .replace(/ü/g, 'u')
            .replace(/Ü/g, 'u')
            .replace(/ö/g, 'o')
            .replace(/Ö/g, 'o')
            .replace(/ç/g, 'c')
            .replace(/Ç/g, 'c')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>




