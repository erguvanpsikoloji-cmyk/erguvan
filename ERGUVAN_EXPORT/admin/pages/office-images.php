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

$db = getDB();

// POST işlemleri (CSRF korumalı) - Header'dan ÖNCE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        // Silme işlemi
        if (isset($_POST['delete_id'])) {
            $id = (int)$_POST['delete_id'];
            $db->prepare("DELETE FROM office_images WHERE id = ?")->execute([$id]);
        }
        // Aktif/Pasif toggle
        elseif (isset($_POST['toggle_id'])) {
            $id = (int)$_POST['toggle_id'];
            $db->prepare("UPDATE office_images SET is_active = 1 - is_active WHERE id = ?")->execute([$id]);
        }
    }
    redirect(admin_url('pages/office-images.php'));
}

// Tüm ofis görsellerini getir
$office_images = $db->query("SELECT * FROM office_images ORDER BY display_order ASC, created_at DESC")->fetchAll();

// Şimdi header'ı dahil et
$page = 'office-images';
$page_title = 'Ofis Görselleri';
require_once __DIR__ . '/../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="margin: 0;">Tüm Ofis Görselleri (<?php echo count($office_images); ?>)</h2>
    <a href="<?php echo admin_url('pages/office-image-add.php'); ?>" class="btn btn-success">➕ Yeni Görsel Ekle</a>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Görsel</th>
                <th>Başlık</th>
                <th>Açıklama</th>
                <th>Sıra</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($office_images)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">
                    Henüz ofis görseli eklenmemiş. <a href="<?php echo admin_url('pages/office-image-add.php'); ?>" style="color: var(--primary);">İlk görseli ekleyin</a>.
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($office_images as $img): ?>
            <tr>
                <td>
                    <img src="<?php echo htmlspecialchars($img['image']); ?>" alt="" class="slider-image-thumb">
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($img['title'] ?? 'Başlıksız'); ?></strong>
                </td>
                <td><?php echo htmlspecialchars(substr($img['description'] ?? '', 0, 60)); ?><?php echo strlen($img['description'] ?? '') > 60 ? '...' : ''; ?></td>
                <td><?php echo $img['display_order']; ?></td>
                <td>
                    <span class="status-badge <?php echo $img['is_active'] ? 'active' : 'inactive'; ?>">
                        <?php echo $img['is_active'] ? 'Aktif' : 'Pasif'; ?>
                    </span>
                </td>
                <td>
                    <div class="table-actions">
                        <form method="POST" style="display: inline;">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="toggle_id" value="<?php echo $img['id']; ?>">
                            <button type="submit" class="btn btn-sm <?php echo $img['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
                                <?php echo $img['is_active'] ? '👁️ Gizle' : '✅ Aktif Et'; ?>
                            </button>
                        </form>
                        <a href="<?php echo admin_url('pages/office-image-edit.php?id=' . $img['id']); ?>" class="btn btn-warning btn-sm">✏️ Düzenle</a>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Bu görseli silmek istediğinizden emin misiniz?');">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="delete_id" value="<?php echo $img['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">🗑️ Sil</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>





