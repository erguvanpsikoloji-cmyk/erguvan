<?php 
// Session'ı sadece bir kez başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../database/db.php';

// Giriş kontrolü
requireLogin();

$page = 'certificates';
$db = getDB();

// Silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $id = (int)$_POST['delete_id'];
        // Önce görseli sil
        $stmt = $db->prepare("SELECT image FROM certificates WHERE id = ?");
        $stmt->execute([$id]);
        $cert = $stmt->fetch();
        if ($cert && $cert['image']) {
            $image_path = __DIR__ . '/../../' . ltrim($cert['image'], '/');
            if (file_exists($image_path)) {
                @unlink($image_path);
            }
        }
        $db->prepare("DELETE FROM certificates WHERE id = ?")->execute([$id]);
    }
    redirect(admin_url('pages/certificates.php'));
}

// Toplu silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $ids = $_POST['selected_ids'] ?? [];
        if (!empty($ids)) {
            // Görselleri sil
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $db->prepare("SELECT image FROM certificates WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $certs = $stmt->fetchAll();
            foreach ($certs as $cert) {
                if ($cert['image']) {
                    $image_path = __DIR__ . '/../../' . ltrim($cert['image'], '/');
                    if (file_exists($image_path)) {
                        @unlink($image_path);
                    }
                }
            }
            // Kayıtları sil
            $stmt = $db->prepare("DELETE FROM certificates WHERE id IN ($placeholders)");
            $stmt->execute($ids);
        }
    }
    redirect(admin_url('pages/certificates.php'));
}

// Durum güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("UPDATE certificates SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$id]);
    }
    redirect(admin_url('pages/certificates.php'));
}

// Sertifikaları getir
$stmt = $db->query("SELECT * FROM certificates ORDER BY display_order ASC, created_at DESC");
$certificates = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="admin-content">
    <div class="admin-header">
        <h1>Sertifikalar</h1>
        <a href="<?php echo admin_url('pages/certificate-add.php'); ?>" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Yeni Sertifika Ekle
        </a>
    </div>

    <?php if (empty($certificates)): ?>
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 1rem; opacity: 0.5;">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="9" y1="3" x2="9" y2="21"></line>
            </svg>
            <p>Henüz sertifika eklenmemiş.</p>
            <a href="<?php echo admin_url('pages/certificate-add.php'); ?>" class="btn btn-primary">İlk Sertifikayı Ekle</a>
        </div>
    <?php else: ?>
        <form method="POST" id="bulkForm" style="margin-bottom: 20px;">
            <?php echo csrfField(); ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th style="width: 120px;">Görsel</th>
                            <th>Başlık</th>
                            <th style="width: 100px;">Sıra</th>
                            <th style="width: 100px;">Durum</th>
                            <th style="width: 150px;">Tarih</th>
                            <th style="width: 150px;">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($certificates as $cert): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected_ids[]" value="<?php echo $cert['id']; ?>" class="row-checkbox">
                                </td>
                                <td>
                                    <img src="<?php echo url($cert['image']); ?>" alt="<?php echo htmlspecialchars($cert['title']); ?>" style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px;">
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($cert['title']); ?></strong>
                                    <?php if ($cert['description']): ?>
                                        <br><small style="color: #64748b;"><?php echo htmlspecialchars(mb_substr($cert['description'], 0, 50)); ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $cert['display_order']; ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <?php echo csrfField(); ?>
                                        <input type="hidden" name="id" value="<?php echo $cert['id']; ?>">
                                        <button type="submit" name="toggle_status" class="btn-status <?php echo $cert['is_active'] ? 'active' : 'inactive'; ?>">
                                            <?php echo $cert['is_active'] ? 'Aktif' : 'Pasif'; ?>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <small><?php echo date('d.m.Y', strtotime($cert['created_at'])); ?></small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?php echo admin_url('pages/certificate-edit.php?id=' . $cert['id']); ?>" class="btn btn-sm btn-secondary">Düzenle</a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Bu sertifikayı silmek istediğinize emin misiniz?');">
                                            <?php echo csrfField(); ?>
                                            <input type="hidden" name="delete_id" value="<?php echo $cert['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 15px;">
                <button type="submit" name="bulk_delete" class="btn btn-danger" onclick="return confirm('Seçili sertifikaları silmek istediğinize emin misiniz?');">Seçilenleri Sil</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
