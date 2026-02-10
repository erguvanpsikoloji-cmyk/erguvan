<?php
require_once __DIR__ . '/../../database/db.php';
require_once __DIR__ . '/../check_auth.php';

$title = 'Danışan Yorumları';

// Silme işlemi
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM testimonials WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: " . admin_url('pages/testimonials.php?success=1'));
    exit;
}

// Aktiflik değiştirme
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    $stmt = $db->prepare("UPDATE testimonials SET is_active = NOT is_active WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: " . admin_url('pages/testimonials.php?status_updated=1'));
    exit;
}

$testimonials = $db->query("SELECT * FROM testimonials ORDER BY display_order ASC, created_at DESC")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="admin-container">
    <div class="admin-header">
        <h1>Danışan Yorumları</h1>
        <a href="<?php echo admin_url('pages/testimonial-add.php'); ?>" class="btn btn-primary">Yeni Yorum Ekle</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Yorum başarıyla silindi.</div>
    <?php endif; ?>

    <div class="admin-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Ad Soyad</th>
                    <th>Yorum</th>
                    <th>Puan</th>
                    <th>Kaynak</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($testimonials as $t): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($t['name']); ?></strong><br><small><?php echo htmlspecialchars($t['date_info']); ?></small>
                        </td>
                        <td><?php echo mb_strimwidth(htmlspecialchars($t['comment']), 0, 100, "..."); ?></td>
                        <td><?php echo $t['rating']; ?> / 5</td>
                        <td><?php echo htmlspecialchars($t['source']); ?></td>
                        <td>
                            <a href="?toggle=<?php echo $t['id']; ?>"
                                class="status-badge <?php echo $t['is_active'] ? 'active' : 'inactive'; ?>">
                                <?php echo $t['is_active'] ? 'Aktif' : 'Pasif'; ?>
                            </a>
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="testimonial-edit.php?id=<?php echo $t['id']; ?>" class="btn-edit">Düzenle</a>
                                <a href="?delete=<?php echo $t['id']; ?>" class="btn-delete"
                                    onclick="return confirm('Emin misiniz?')">Sil</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
