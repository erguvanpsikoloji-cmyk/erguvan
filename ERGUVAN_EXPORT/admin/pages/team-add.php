<?php
// admin/pages/team-add.php
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

        $image = '';
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
                $stmt = $db->prepare("INSERT INTO team_members (name, title, image, bio, specialties, education, certificates, is_active, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $title, $image, $bio, $specialties, $education, $certificates, $is_active, $display_order]);
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
$page_title = 'Yeni Uzman Ekle';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="edit-header">
    <h2>Yeni Uzman Ekle</h2>
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
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="form-group" style="flex: 2;">
            <label for="title">Ünvan *</label>
            <input type="text" id="title" name="title" class="form-control" placeholder="Örn: Uzman Klinik Psikolog"
                required>
        </div>
        <div class="form-group" style="flex: 1;">
            <label for="display_order">Sıralama</label>
            <input type="number" id="display_order" name="display_order" class="form-control" value="0">
        </div>
    </div>

    <div class="form-group">
        <label for="image">Profil Fotoğrafı</label>
        <input type="file" id="image" name="image" class="form-control" accept="image/*"
            onchange="previewImageFile(this)">
        <div id="imagePreview" style="margin-top: 10px; display: none;">
            <img id="previewImg" src="" alt="Önizleme" style="max-width: 200px; max-height: 200px; border-radius: 8px;">
        </div>
    </div>

    <div class="form-group">
        <label for="bio">Biyografi / Hakkında</label>
        <textarea id="bio" name="bio" class="form-control" rows="5"></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="specialties">Uzmanlık Alanları</label>
            <textarea id="specialties" name="specialties" class="form-control" rows="6" placeholder="Her satıra bir uzmanlık yazın. Örn:
Depresyon
Anksiyete
Oyun Terapisi"></textarea>
            <small style="color: #64748b;">Her satıra bir tane yazınız.</small>
        </div>
        <div class="form-group">
            <label for="education">Eğitim Bilgileri</label>
            <textarea id="education" name="education" class="form-control" rows="6"
                placeholder="Her satıra bir okul/bölüm yazın."></textarea>
            <small style="color: #64748b;">Her satıra bir tane yazınız.</small>
        </div>
    </div>

    <div class="form-group">
        <label for="certificates">Sertifikalar</label>
        <textarea id="certificates" name="certificates" class="form-control" rows="4"
            placeholder="Her satıra bir sertifika yazın."></textarea>
    </div>

    <div class="form-group">
        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
            <input type="checkbox" name="is_active" checked style="width: 20px; height: 20px;">
            <span>Aktif (Sitede Göster)</span>
        </label>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Kaydet</button>
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
