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
            $db->prepare("DELETE FROM sliders WHERE id = ?")->execute([$id]);
        }
        // Aktif/Pasif toggle
        elseif (isset($_POST['toggle_id'])) {
            $id = (int)$_POST['toggle_id'];
            $db->prepare("UPDATE sliders SET is_active = 1 - is_active WHERE id = ?")->execute([$id]);
        }
    }
    redirect(admin_url('pages/sliders.php'));
}

// Tüm sliderları getir
$sliders = $db->query("SELECT * FROM sliders ORDER BY display_order ASC, created_at DESC")->fetchAll();

// Şimdi header'ı dahil et
$page = 'sliders';
$page_title = 'Sliderlar';
require_once __DIR__ . '/../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="margin: 0;">Tüm Sliderlar (<?php echo count($sliders); ?>)</h2>
    <a href="<?php echo admin_url('pages/slider-add.php'); ?>" class="btn btn-success">➕ Yeni Slider Ekle</a>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Görsel</th>
                <th>Başlık</th>
                <th>Alt Başlık</th>
                <th>Sıra</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sliders as $slider): ?>
            <tr>
                <td>
                    <img src="<?php echo htmlspecialchars($slider['image']); ?>" alt="" class="slider-image-thumb">
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($slider['title']); ?></strong><br>
                    <?php if ($slider['subtitle']): ?>
                        <small style="color: #64748b;"><?php echo htmlspecialchars($slider['subtitle']); ?></small>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars(substr($slider['description'] ?? '', 0, 60)); ?>...</td>
                <td><?php echo $slider['display_order']; ?></td>
                <td>
                    <span class="status-badge <?php echo $slider['is_active'] ? 'active' : 'inactive'; ?>">
                        <?php echo $slider['is_active'] ? 'Aktif' : 'Pasif'; ?>
                    </span>
                </td>
                <td>
                    <div class="table-actions">
                        <form method="POST" style="display: inline;">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="toggle_id" value="<?php echo $slider['id']; ?>">
                            <button type="submit" class="btn btn-sm <?php echo $slider['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
                                <?php echo $slider['is_active'] ? '👁️ Gizle' : '✅ Aktif Et'; ?>
                            </button>
                        </form>
                        <a href="<?php echo admin_url('pages/slider-edit.php?id=' . $slider['id']); ?>" class="btn btn-warning btn-sm">✏️ Düzenle</a>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Bu slider\'ı silmek istediğinizden emin misiniz?');">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="delete_id" value="<?php echo $slider['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">🗑️ Sil</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
