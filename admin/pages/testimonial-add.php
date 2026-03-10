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

$title = 'Yeni Yorum Ekle';
$error = '';

// Tabloyu oluştur (yoksa)
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    $rating = (int) ($_POST['rating'] ?? 5);
    $source = trim($_POST['source'] ?? 'Google');
    $date_info = trim($_POST['date_info'] ?? '');
    $avatar_char = mb_strtoupper(mb_substr($name, 0, 1));
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $display_order = (int) ($_POST['display_order'] ?? 0);

    if (empty($name) || empty($comment)) {
        $error = 'Ad ve yorum alanları zorunludur.';
    } else {
        $stmt = $db->prepare("INSERT INTO testimonials (name, comment, rating, source, date_info, avatar_char, is_active, display_order) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$name, $comment, $rating, $source, $date_info, $avatar_char, $is_active, $display_order]);
        header("Location: " . admin_url('pages/testimonials.php?added=1'));
        exit;
    }
}

$page = 'testimonials';
$page_title = 'Yeni Yorum Ekle';
require_once __DIR__ . '/../includes/header.php';
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
    <h1 style="font-size:1.8rem; font-weight:800; color:#1e293b; margin:0;">+ Yeni Yorum Ekle</h1>
    <a href="<?php echo admin_url('pages/testimonials.php'); ?>"
        style="color:#64748b;text-decoration:none;font-weight:600;">← Geri</a>
</div>

<?php if ($error): ?>
    <div style="background:#fee2e2;color:#991b1b;padding:12px 20px;border-radius:10px;margin-bottom:1rem;">
        <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div style="background:#fff;border-radius:20px;padding:2rem;box-shadow:0 2px 15px rgba(0,0,0,0.04);">
    <form method="POST">
        <?php echo csrfField(); ?>
        <div style="margin-bottom:1.2rem;">
            <label style="display:block;font-weight:600;margin-bottom:6px;">Ad Soyad *</label>
            <input type="text" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                style="width:100%;padding:10px 16px;border:2px solid #f1f5f9;border-radius:10px;font-size:1rem;">
        </div>
        <div style="margin-bottom:1.2rem;">
            <label style="display:block;font-weight:600;margin-bottom:6px;">Yorum *</label>
            <textarea name="comment" required rows="4"
                style="width:100%;padding:10px 16px;border:2px solid #f1f5f9;border-radius:10px;font-size:1rem;resize:vertical;"><?php echo htmlspecialchars($_POST['comment'] ?? ''); ?></textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1.2rem;">
            <div>
                <label style="display:block;font-weight:600;margin-bottom:6px;">Puan</label>
                <select name="rating" style="width:100%;padding:10px 16px;border:2px solid #f1f5f9;border-radius:10px;">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($i == ($_POST['rating'] ?? 5)) ? 'selected' : ''; ?>>
                            <?php echo $i; ?> Yıldız</option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label style="display:block;font-weight:600;margin-bottom:6px;">Kaynak</label>
                <input type="text" name="source" value="<?php echo htmlspecialchars($_POST['source'] ?? 'Google'); ?>"
                    style="width:100%;padding:10px 16px;border:2px solid #f1f5f9;border-radius:10px;">
            </div>
            <div>
                <label style="display:block;font-weight:600;margin-bottom:6px;">Tarih (ör: "2 ay önce")</label>
                <input type="text" name="date_info" value="<?php echo htmlspecialchars($_POST['date_info'] ?? ''); ?>"
                    style="width:100%;padding:10px 16px;border:2px solid #f1f5f9;border-radius:10px;">
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:2rem;margin-bottom:1.5rem;">
            <div>
                <label style="display:block;font-weight:600;margin-bottom:6px;">Sıra No</label>
                <input type="number" name="display_order" value="<?php echo (int) ($_POST['display_order'] ?? 0); ?>"
                    min="0" style="padding:10px 16px;border:2px solid #f1f5f9;border-radius:10px;width:100px;">
            </div>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;margin-top:24px;">
                <input type="checkbox" name="is_active" value="1" <?php echo (!isset($_POST['is_active']) || $_POST['is_active']) ? 'checked' : ''; ?>>
                Aktif (sitede göster)
            </label>
        </div>
        <button type="submit"
            style="background:#db2777;color:#fff;padding:12px 28px;border:none;border-radius:12px;font-weight:700;font-size:1rem;cursor:pointer;">Kaydet</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>