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
$success = '';
$error = '';

// SEO ayarlarını getir
$seo_settings = $db->query("SELECT * FROM seo_settings ORDER BY page_type")->fetchAll();

// Sayfa güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_seo') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Geçersiz istek!';
    } else {
        $id = (int)($_POST['id'] ?? 0);
        $meta_title = trim($_POST['meta_title'] ?? '');
        $meta_description = trim($_POST['meta_description'] ?? '');
        $meta_keywords = trim($_POST['meta_keywords'] ?? '');
        $og_title = trim($_POST['og_title'] ?? '');
        $og_description = trim($_POST['og_description'] ?? '');
        $og_image = trim($_POST['og_image'] ?? '');
        $canonical_url = trim($_POST['canonical_url'] ?? '');
        $robots_content = trim($_POST['robots_content'] ?? 'index, follow');
        
        try {
            $stmt = $db->prepare("UPDATE seo_settings SET 
                                   meta_title = :meta_title,
                                   meta_description = :meta_description,
                                   meta_keywords = :meta_keywords,
                                   og_title = :og_title,
                                   og_description = :og_description,
                                   og_image = :og_image,
                                   canonical_url = :canonical_url,
                                   robots_content = :robots_content
                                   WHERE id = :id");
            $stmt->execute([
                ':meta_title' => $meta_title,
                ':meta_description' => $meta_description,
                ':meta_keywords' => $meta_keywords,
                ':og_title' => $og_title,
                ':og_description' => $og_description,
                ':og_image' => $og_image,
                ':canonical_url' => $canonical_url,
                ':robots_content' => $robots_content,
                ':id' => $id
            ]);
            $success = 'SEO ayarları başarıyla güncellendi!';
        } catch (PDOException $e) {
            $error = 'Hata: ' . $e->getMessage();
        }
    }
}

// Yeni sayfa ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_page') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Geçersiz istek!';
    } else {
        $page_type = trim($_POST['page_type'] ?? '');
        $page_path = trim($_POST['page_path'] ?? '');
        
        if ($page_type) {
            try {
                $stmt = $db->prepare("INSERT INTO seo_settings (page_type, page_path) VALUES (:page_type, :page_path)");
                $stmt->execute([
                    ':page_type' => $page_type,
                    ':page_path' => $page_path
                ]);
                $success = 'Yeni sayfa eklendi!';
                // Sayfayı yenile
                header('Location: ' . admin_url('pages/seo.php'));
                exit;
            } catch (PDOException $e) {
                $error = 'Hata: ' . $e->getMessage();
            }
        }
    }
}

// Sayfa silme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_page') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Geçersiz istek!';
    } else {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            try {
                $stmt = $db->prepare("DELETE FROM seo_settings WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $success = 'Sayfa silindi!';
                header('Location: ' . admin_url('pages/seo.php'));
                exit;
            } catch (PDOException $e) {
                $error = 'Hata: ' . $e->getMessage();
            }
        }
    }
}

// SEO ayarlarını tekrar getir
$seo_settings = $db->query("SELECT * FROM seo_settings ORDER BY page_type")->fetchAll();

$page = 'seo';
$page_title = 'SEO Yönetimi';
require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($success): ?>
    <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Sayfa SEO Ayarları</h2>
        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('addPageForm').style.display='block'">
            + Yeni Sayfa Ekle
        </button>
    </div>
    
    <!-- Yeni Sayfa Ekleme Formu -->
    <div id="addPageForm" style="display: none; margin-bottom: 20px; padding: 20px; background: #f8fafc; border-radius: 8px;">
        <h3 style="margin-top: 0;">Yeni Sayfa Ekle</h3>
        <form method="POST" action="">
            <?php echo csrfField(); ?>
            <input type="hidden" name="action" value="add_page">
            <div class="form-row">
                <div class="form-group">
                    <label>Sayfa Tipi *</label>
                    <input type="text" name="page_type" class="form-control" required placeholder="ornek: contact, about">
                    <small style="color: #64748b;">Benzersiz bir sayfa tipi (örn: contact, about, services)</small>
                </div>
                <div class="form-group">
                    <label>Sayfa Yolu</label>
                    <input type="text" name="page_path" class="form-control" placeholder="/pages/contact.php">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Ekle</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addPageForm').style.display='none'">İptal</button>
            </div>
        </form>
    </div>
    
    <!-- SEO Ayarları Listesi -->
    <?php if (empty($seo_settings)): ?>
        <div class="empty-state">
            <p>Henüz SEO ayarı bulunmuyor.</p>
        </div>
    <?php else: ?>
        <div class="seo-list">
            <?php foreach ($seo_settings as $seo): ?>
                <div class="seo-item" style="margin-bottom: 20px; padding: 20px; background: white; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                        <div>
                            <h3 style="margin: 0 0 5px 0; color: #1e293b;"><?php echo htmlspecialchars($seo['page_type']); ?></h3>
                            <?php if ($seo['page_path']): ?>
                                <p style="margin: 0; color: #64748b; font-size: 14px;"><?php echo htmlspecialchars($seo['page_path']); ?></p>
                            <?php endif; ?>
                        </div>
                        <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Bu sayfayı silmek istediğinizden emin misiniz?');">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="action" value="delete_page">
                            <input type="hidden" name="id" value="<?php echo $seo['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                        </form>
                    </div>
                    
                    <form method="POST" action="" class="seo-form">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="action" value="update_seo">
                        <input type="hidden" name="id" value="<?php echo $seo['id']; ?>">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Meta Title</label>
                                <input type="text" name="meta_title" class="form-control" value="<?php echo htmlspecialchars($seo['meta_title'] ?? ''); ?>" placeholder="Sayfa başlığı">
                            </div>
                            <div class="form-group">
                                <label>Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="2" placeholder="Sayfa açıklaması (150-160 karakter)"><?php echo htmlspecialchars($seo['meta_description'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Meta Keywords</label>
                            <input type="text" name="meta_keywords" class="form-control" value="<?php echo htmlspecialchars($seo['meta_keywords'] ?? ''); ?>" placeholder="anahtar, kelimeler, virgülle, ayrılmış">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>OG Title (Open Graph)</label>
                                <input type="text" name="og_title" class="form-control" value="<?php echo htmlspecialchars($seo['og_title'] ?? ''); ?>" placeholder="Sosyal medya başlığı">
                            </div>
                            <div class="form-group">
                                <label>OG Description</label>
                                <textarea name="og_description" class="form-control" rows="2" placeholder="Sosyal medya açıklaması"><?php echo htmlspecialchars($seo['og_description'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>OG Image URL</label>
                                <input type="url" name="og_image" class="form-control" value="<?php echo htmlspecialchars($seo['og_image'] ?? ''); ?>" placeholder="https://...">
                            </div>
                            <div class="form-group">
                                <label>Canonical URL</label>
                                <input type="url" name="canonical_url" class="form-control" value="<?php echo htmlspecialchars($seo['canonical_url'] ?? ''); ?>" placeholder="https://...">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Robots Meta</label>
                            <input type="text" name="robots_content" class="form-control" value="<?php echo htmlspecialchars($seo['robots_content'] ?? 'index, follow'); ?>" placeholder="index, follow">
                            <small style="color: #64748b;">Örnek: index, follow veya noindex, nofollow</small>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Güncelle</button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
