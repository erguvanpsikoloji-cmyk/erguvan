<?php
// admin/pages/team-edit.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../database/db.php';
require_once __DIR__ . '/../includes/upload-handler.php';

$db = getDB();
$error = '';

$id = (int) ($_GET['id'] ?? 0);
if (!$id)
    redirect(admin_url('pages/team.php'));

$member = $db->prepare("SELECT * FROM team_members WHERE id = ?");
$member->execute([$id]);
$member = $member->fetch();

if (!$member)
    redirect(admin_url('pages/team.php'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Geçersiz istek!';
    } else {
        $name = $_POST['name'] ?? '';
        $title = $_POST['title'] ?? '';
        $bio = $_POST['bio'] ?? '';
        $specialties = $_POST['specialties'] ?? '';
        $education = $_POST['education'] ?? '';
        $certificates = $_POST['certificates'] ?? '';
        $display_order = (int) ($_POST['display_order'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        $image = $_POST['current_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = handleImageUpload($_FILES['image'], 'team');
            if ($uploadResult['success']) {
                $image = $uploadResult['url'];
            } else {
                $error = $uploadResult['message'];
            }
        }

        if (!$error && $name && $title) {
            try {
                $stmt = $db->prepare("UPDATE team_members SET name=?, title=?, image=?, bio=?, specialties=?, education=?, certificates=?, is_active=?, display_order=?, updated_at=NOW() WHERE id=?");
                $stmt->execute([$name, $title, $image, $bio, $specialties, $education, $certificates, $is_active, $display_order, $id]);
                // Güncelleme sonrası veriyi yenile
                $member['name'] = $name;
                $member['title'] = $title;
                $member['image'] = $image;
                // vs.. ama yönlendirelim
                redirect(admin_url('pages/team.php'));
            } catch (PDOException $e) {
                $error = 'Veritabanı hatası: ' . $e->getMessage();
            }
        } elseif (!$error) {
            $error = 'Lütfen ad ve ünvan alanlarını doldurun.';
        }
    }
}

$page = 'team';
$page_title = 'Uzman Düzenle';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="edit-header">
    <h2>Uzman Düzenle: <?php echo htmlspecialchars($member['name']); ?></h2>
    <a href="<?php echo admin_url('pages/team.php'); ?>" class="btn btn-secondary">Geri Dön</a>
</div>

<?php if ($error): ?>
    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data">
    <?php echo csrfField(); ?>

    <div class="form-row">
        <div class="form-group" style="flex: 2;">
            <label for="name">Ad Soyad *</label>
            <input type="text" id="name" name="name" class="form-control"
                value="<?php echo htmlspecialchars($member['name']); ?>" required>
        </div>
        <div class="form-group" style="flex: 2;">
            <label for="title">Ünvan *</label>
            <input type="text" id="title" name="title" class="form-control"
                value="<?php echo htmlspecialchars($member['title']); ?>" required>
        </div>
        <div class="form-group" style="flex: 1;">
            <label for="display_order">Sıralama</label>
            <input type="number" id="display_order" name="display_order" class="form-control"
                value="<?php echo $member['display_order']; ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="image">Profil Fotoğrafı</label>
        <div style="display: flex; gap: 20px; align-items: flex-start;">
            <?php if ($member['image']): ?>
                <div>
                    <img src="<?php echo htmlspecialchars($member['image']); ?>" alt="Mevcut"
                        style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($member['image']); ?>">
                </div>
            <?php endif; ?>
            <div style="flex: 1;">
                <input type="file" id="image" name="image" class="form-control" accept="image/*"
                    onchange="previewImageFile(this)">
                <div id="imagePreview" style="margin-top: 10px; display: none;">
                    <img id="previewImg" src="" alt="Önizleme"
                        style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="bio">Biyografi / Hakkında</label>
        <textarea id="bio" name="bio" class="form-control"
            rows="5"><?php echo htmlspecialchars($member['bio']); ?></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="specialties">Uzmanlık Alanları</label>
            <textarea id="specialties" name="specialties" class="form-control" rows="6"
                placeholder="Her satıra bir uzmanlık yazın."><?php echo htmlspecialchars($member['specialties']); ?></textarea>
            <small style="color: #64748b;">Her satıra bir tane yazınız.</small>
        </div>
        <div class="form-group">
            <label for="education">Eğitim Bilgileri</label>
            <textarea id="education" name="education" class="form-control" rows="6"
                placeholder="Her satıra bir okul/bölüm yazın."><?php echo htmlspecialchars($member['education']); ?></textarea>
            <small style="color: #64748b;">Her satıra bir tane yazınız.</small>
        </div>
    </div>

    <div class="form-group">
        <label for="certificates">Sertifikalar</label>
        <textarea id="certificates" name="certificates" class="form-control" rows="4"
            placeholder="Her satıra bir sertifika yazın."><?php echo htmlspecialchars($member['certificates']); ?></textarea>
    </div>

    <div class="form-group">
        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
            <input type="checkbox" name="is_active" <?php echo $member['is_active'] ? 'checked' : ''; ?>
                style="width: 20px; height: 20px;">
            <span>Aktif (Sitede Göster)</span>
        </label>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Güncelle</button>
    </div>
</form>

<script>
    function previewImageFile(input) {
        const preview = document.getElementById('imagePreview');
        const img = document.getElementById('previewImg');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                img.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
