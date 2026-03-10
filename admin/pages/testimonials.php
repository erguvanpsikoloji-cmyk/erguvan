<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../database/db.php';

requireLogin();
$db = getDB();

$title = 'Danışan Yorumları';

// Silme işlemi
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $db->prepare("DELETE FROM testimonials WHERE id = ?")->execute([$id]);
    header("Location: " . admin_url('pages/testimonials.php?success=1'));
    exit;
}

// Aktiflik değiştirme
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    $db->prepare("UPDATE testimonials SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
    header("Location: " . admin_url('pages/testimonials.php?status_updated=1'));
    exit;
}

// Toplu sıra güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_order'])) {
    foreach ($_POST['order'] as $id => $order) {
        $db->prepare("UPDATE testimonials SET display_order = ? WHERE id = ?")
            ->execute([(int) $order, (int) $id]);
    }
    header("Location: " . admin_url('pages/testimonials.php?order_updated=1'));
    exit;
}


// Yorumları çek
try {
    $testimonials = $db->query("SELECT * FROM testimonials ORDER BY display_order ASC, created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Tablo yoksa oluştur
    $db->exec("CREATE TABLE IF NOT EXISTS testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        comment TEXT NOT NULL,
        rating TINYINT DEFAULT 5,
        source VARCHAR(50) DEFAULT 'Google',
        date_info VARCHAR(50) DEFAULT NULL,
        avatar_char CHAR(2) DEFAULT NULL,
        is_active TINYINT(1) DEFAULT 1,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $testimonials = [];
}

$page = 'testimonials';
$page_title = 'Danışan Yorumları';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
    .testimonial-table {
        width: 100%;
        border-collapse: collapse;
    }

    .testimonial-table th,
    .testimonial-table td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #f1f5f9;
    }

    .testimonial-table th {
        background: #fdf2f8;
        color: #db2777;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
    }

    .status-badge.active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge.inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .action-btns {
        display: flex;
        gap: 8px;
    }

    .btn-edit,
    .btn-delete {
        padding: 6px 14px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .btn-edit {
        background: #fdf2f8;
        color: #db2777;
    }

    .btn-delete {
        background: #fff1f2;
        color: #ef4444;
    }

    .stars {
        color: #f59e0b;
        letter-spacing: 1px;
    }
</style>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
    <h1 style="font-size:1.8rem; font-weight:800; color:#1e293b; margin:0;">💬 Danışan Yorumları</h1>
    <a href="<?php echo admin_url('pages/testimonial-add.php'); ?>"
        style="background:#db2777; color:#fff; padding:10px 22px; border-radius:12px; text-decoration:none; font-weight:700;">+
        Yeni Ekle</a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div style="background:#d1fae5;color:#065f46;padding:12px 20px;border-radius:10px;margin-bottom:1rem;">✅ Yorum silindi.</div>
<?php endif; ?>
<?php if (isset($_GET['added'])): ?>
    <div style="background:#d1fae5;color:#065f46;padding:12px 20px;border-radius:10px;margin-bottom:1rem;">✅ Yorum eklendi.</div>
<?php endif; ?>
<?php if (isset($_GET['updated'])): ?>
    <div style="background:#d1fae5;color:#065f46;padding:12px 20px;border-radius:10px;margin-bottom:1rem;">✅ Yorum güncellendi.</div>
<?php endif; ?>
<?php if (isset($_GET['order_updated'])): ?>
    <div style="background:#dbeafe;color:#1e40af;padding:12px 20px;border-radius:10px;margin-bottom:1rem;">🔢 Sıralama güncellendi.</div>
<?php endif; ?>

<?php if (empty($testimonials)): ?>
    <div style="text-align:center;padding:60px;background:#fff;border-radius:20px;border:2px dashed #e2e8f0;">
        <p style="color:#64748b;font-size:1.1rem;">Henüz yorum yok. <a href="<?php echo admin_url('pages/testimonial-add.php'); ?>" style="color:#db2777;">Yorum ekle →</a></p>
    </div>
<?php else: ?>
    <form method="POST">
        <?php echo csrfField(); ?>
        <input type="hidden" name="bulk_order" value="1">

        <div style="background:#fff;border-radius:20px;overflow:hidden;border:1px solid #f1f5f9;box-shadow:0 2px 15px rgba(0,0,0,0.04);">
            <table class="testimonial-table">
                <thead>
                    <tr>
                        <th style="width:60px;">Sıra</th>
                        <th>Ad Soyad</th>
                        <th>Yorum</th>
                        <th>Puan</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($testimonials as $t): ?>
                        <tr>
                            <td>
                                <input type="number" name="order[<?php echo $t['id']; ?>]"
                                    value="<?php echo (int)($t['display_order'] ?? 0); ?>"
                                    min="0" max="999"
                                    style="width:60px;padding:6px 8px;border:2px solid #e2e8f0;border-radius:8px;text-align:center;font-weight:700;color:#db2777;">
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($t['name']); ?></strong><br>
                                <small style="color:#94a3b8;"><?php echo htmlspecialchars($t['date_info'] ?? ''); ?></small>
                            </td>
                            <td style="max-width:250px;color:#475569;"><?php echo htmlspecialchars(mb_strimwidth($t['comment'], 0, 80, '...')); ?></td>
                            <td><span class="stars"><?php echo str_repeat('★', (int)$t['rating']); ?></span></td>
                            <td>
                                <a href="?toggle=<?php echo $t['id']; ?>" class="status-badge <?php echo $t['is_active'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $t['is_active'] ? 'Aktif' : 'Pasif'; ?>
                                </a>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="testimonial-edit.php?id=<?php echo $t['id']; ?>" class="btn-edit">Düzenle</a>
                                    <a href="?delete=<?php echo $t['id']; ?>" class="btn-delete" onclick="return confirm('Emin misiniz?')">Sil</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:1.2rem;text-align:right;">
            <button type="submit" style="background:#1e40af;color:#fff;padding:12px 28px;border:none;border-radius:12px;font-weight:700;font-size:0.95rem;cursor:pointer;">
                🔢 Sıralamayı Kaydet
            </button>
        </div>
    </form>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>