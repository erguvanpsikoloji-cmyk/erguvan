<?php
require_once __DIR__ . '/../../database/db.php';
require_once __DIR__ . '/../check_auth.php';

$title = 'Site Ayarları';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $db->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->execute([$value, $key]);
    }
    $success_msg = "Ayarlar başarıyla güncellendi.";
}

$settings_query = $db->query("SELECT * FROM site_settings ORDER BY setting_group");
$settings = [];
while ($row = $settings_query->fetch()) {
    $settings[$row['setting_group']][] = $row;
}

include __DIR__ . '/../includes/header.php';
?>

<div class="admin-container">
    <div class="admin-header">
        <h1>Site Ayarları</h1>
    </div>

    <?php if (isset($success_msg)): ?>
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>

    <form method="POST" class="admin-form-group">
        <?php foreach ($settings as $group => $items): ?>
            <div class="admin-card mb-4">
                <h2 class="card-title"><?php echo ucfirst($group); ?> Ayarları</h2>
                <?php foreach ($items as $item): ?>
                    <div class="form-item">
                        <label><?php echo ucwords(str_replace('_', ' ', $item['setting_key'])); ?></label>
                        <input type="text" name="settings[<?php echo $item['setting_key']; ?>]"
                            value="<?php echo htmlspecialchars($item['setting_value']); ?>" class="form-control">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <div class="form-actions mt-4">
            <button type="submit" class="btn btn-primary btn-lg">Ayarları Kaydet</button>
        </div>
    </form>
</div>

<style>
    .mb-4 {
        margin-bottom: 2rem;
    }

    .card-title {
        border-bottom: 1px solid #eee;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
        color: var(--primary);
    }

    .form-item {
        margin-bottom: 1.25rem;
    }

    .form-item label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
    }
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
