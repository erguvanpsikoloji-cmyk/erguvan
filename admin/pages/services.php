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
            $db->prepare("DELETE FROM services WHERE id = ?")->execute([$id]);
        }
        // Aktif/Pasif toggle
        elseif (isset($_POST['toggle_id'])) {
            $id = (int)$_POST['toggle_id'];
            $db->prepare("UPDATE services SET is_active = 1 - is_active WHERE id = ?")->execute([$id]);
        }
        // Featured toggle
        elseif (isset($_POST['toggle_featured_id'])) {
            $id = (int)$_POST['toggle_featured_id'];
            $db->prepare("UPDATE services SET is_featured = 1 - is_featured WHERE id = ?")->execute([$id]);
        }
    }
    redirect(admin_url('pages/services.php'));
}

// Tüm hizmetleri getir
$services = $db->query("SELECT * FROM services ORDER BY display_order ASC, created_at DESC")->fetchAll();

// Şimdi header'ı dahil et
$page = 'services';
$page_title = 'Hizmetler';
require_once __DIR__ . '/../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="margin: 0;">Tüm Hizmetler (<?php echo count($services); ?>)</h2>
    <a href="<?php echo admin_url('pages/service-add.php'); ?>" class="btn btn-success">➕ Yeni Hizmet Ekle</a>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>İkon</th>
                <th>Başlık</th>
                <th>Açıklama</th>
                <th>Sıra</th>
                <th>Durum</th>
                <th>Öne Çıkan</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($services)): ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                    Henüz hizmet eklenmemiş. <a href="<?php echo admin_url('pages/service-add.php'); ?>" style="color: #6366f1;">İlk hizmeti eklemek için tıklayın</a>.
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($services as $service): ?>
            <tr>
                <td>
                    <?php if ($service['icon']): ?>
                        <div style="font-size: 32px; text-align: center;"><?php echo htmlspecialchars($service['icon']); ?></div>
                    <?php else: ?>
                        <div style="font-size: 32px; text-align: center; color: #94a3b8;">👤</div>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($service['title']); ?></strong><br>
                    <small style="color: #64748b;">Slug: <?php echo htmlspecialchars($service['slug']); ?></small>
                </td>
                <td>
                    <?php echo htmlspecialchars(substr($service['description'] ?? '', 0, 80)); ?>
                    <?php echo strlen($service['description'] ?? '') > 80 ? '...' : ''; ?>
                </td>
                <td><?php echo $service['display_order']; ?></td>
                <td>
                    <span class="status-badge <?php echo $service['is_active'] ? 'active' : 'inactive'; ?>">
                        <?php echo $service['is_active'] ? 'Aktif' : 'Pasif'; ?>
                    </span>
                </td>
                <td>
                    <span class="status-badge <?php echo $service['is_featured'] ? 'active' : 'inactive'; ?>">
                        <?php echo $service['is_featured'] ? '⭐ Öne Çıkan' : 'Normal'; ?>
                    </span>
                </td>
                <td>
                    <div class="table-actions">
                        <form method="POST" style="display: inline;">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="toggle_id" value="<?php echo $service['id']; ?>">
                            <button type="submit" class="btn btn-sm <?php echo $service['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
                                <?php echo $service['is_active'] ? '👁️ Gizle' : '✅ Aktif Et'; ?>
                            </button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="toggle_featured_id" value="<?php echo $service['id']; ?>">
                            <button type="submit" class="btn btn-sm <?php echo $service['is_featured'] ? 'btn-secondary' : 'btn-primary'; ?>" title="Öne Çıkan Yap">
                                <?php echo $service['is_featured'] ? '⭐ Kaldır' : '⭐ Öne Çıkar'; ?>
                            </button>
                        </form>
                        <a href="<?php echo admin_url('pages/service-edit.php?id=' . $service['id']); ?>" class="btn btn-warning btn-sm">✏️ Düzenle</a>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Bu hizmeti silmek istediğinizden emin misiniz?');">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="delete_id" value="<?php echo $service['id']; ?>">
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




