<?php
require_once __DIR__ . '/../../database/db.php';
require_once __DIR__ . '/../check_auth.php';

$title = 'Yorum Düzenle';
$error = '';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    header("Location: " . admin_url('pages/testimonials.php'));
    exit;
}

$t = $db->prepare("SELECT * FROM testimonials WHERE id = ?");
$t->execute([$id]);
$testimonial = $t->fetch(PDO::FETCH_ASSOC);
if (!$testimonial) {
    header("Location: " . admin_url('pages/testimonials.php'));
    exit;
}

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
        $stmt = $db->prepare("UPDATE testimonials SET name=?, comment=?, rating=?, source=?, date_info=?, avatar_char=?, is_active=?, display_order=? WHERE id=?");
        $stmt->execute([$name, $comment, $rating, $source, $date_info, $avatar_char, $is_active, $display_order, $id]);
        header("Location: " . admin_url('pages/testimonials.php?updated=1'));
        exit;
    }

    // POST verilerini güncelle
    $testimonial = array_merge($testimonial, $_POST);
}

include __DIR__ . '/../includes/header.php';
?>
<div class="admin-container">
    <div class="admin-header">
        <h1>Yorum Düzenle</h1>
        <a href="<?php echo admin_url('pages/testimonials.php'); ?>" class="btn btn-secondary">← Geri</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="admin-card">
        <form method="POST">
            <div class="form-group">
                <label>Ad Soyad *</label>
                <input type="text" name="name" class="form-control" required
                    value="<?php echo htmlspecialchars($testimonial['name']); ?>">
            </div>
            <div class="form-group">
                <label>Yorum *</label>
                <textarea name="comment" class="form-control" rows="4"
                    required><?php echo htmlspecialchars($testimonial['comment']); ?></textarea>
            </div>
            <div class="form-group">
                <label>Puan (1-5)</label>
                <select name="rating" class="form-control">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($i == $testimonial['rating']) ? 'selected' : ''; ?>>
                            <?php echo $i; ?> Yıldız
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Kaynak</label>
                <input type="text" name="source" class="form-control"
                    value="<?php echo htmlspecialchars($testimonial['source'] ?? 'Google'); ?>">
            </div>
            <div class="form-group">
                <label>Tarih Bilgisi (örn: "2 ay önce")</label>
                <input type="text" name="date_info" class="form-control"
                    value="<?php echo htmlspecialchars($testimonial['date_info'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Sıra No</label>
                <input type="number" name="display_order" class="form-control"
                    value="<?php echo (int) ($testimonial['display_order'] ?? 0); ?>" min="0">
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" <?php echo $testimonial['is_active'] ? 'checked' : ''; ?>>
                    Aktif (sitede göster)
                </label>
            </div>
            <button type="submit" class="btn btn-primary">Güncelle</button>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>