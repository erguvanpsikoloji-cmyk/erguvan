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
    <div class="alert alert-success"
        style="background:#d1fae5;color:#065f46;padding:12px 20px;border-radius:10px;margin-bottom:1rem;">✅ Yorum silindi.
    </div>
<?php endif; ?>
<?php if (isset($_GET['added'])): ?>
    <div class="alert alert-success"
        style="background:#d1fae5;color:#065f46;padding:12px 20px;border-radius:10px;margin-bottom:1rem;">✅ Yorum eklendi.
    </div>
<?php endif; ?>
<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success"
        style="background:#d1fae5;color:#065f46;padding:12px 20px;border-radius:10px;margin-bottom:1rem;">✅ Yorum
        güncellendi.</div>
<?php endif; ?>

<?php if (empty($testimonials)): ?>
    <div style="text-align:center;padding:60px;background:#fff;border-radius:20px;border:2px dashed #e2e8f0;">
        <p style="color:#64748b;font-size:1.1rem;">Henüz yorum yok. <a
                href="<?php echo admin_url('pages/testimonial-add.php'); ?>" style="color:#db2777;">Yorum ekle →</a></p>
        <p style="color:#94a3b8;font-size:0.85rem;margin-top:8px;">Yorumları ilk kez yüklemek için: <a
                href="<?php echo url('setup_testimonials.php'); ?>" target="_blank"
                style="color:#db2777;">setup_testimonials.php</a></p>
    </div>
<?php else: ?>
    <div
        style="background:#fff;border-radius:20px;overflow:hidden;border:1px solid #f1f5f9;box-shadow:0 2px 15px rgba(0,0,0,0.04);">
        <table class="testimonial-table">
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
                        <td>
                            <strong><?php echo htmlspecialchars($t['name']); ?></strong><br>
                            <small style="color:#94a3b8;"><?php echo htmlspecialchars($t['date_info'] ?? ''); ?></small>
                        </td>
                        <td style="max-width:280px;color:#475569;">
                            <?php echo htmlspecialchars(mb_strimwidth($t['comment'], 0, 90, '...')); ?></td>
                        <td><span class="stars"><?php echo str_repeat('★', (int) $t['rating']); ?></span></td>
                        <td style="color:#64748b;"><?php echo htmlspecialchars($t['source'] ?? 'Google'); ?></td>
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
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>