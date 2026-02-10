<?php
require_once __DIR__ . '/../../database/db.php';
require_once __DIR__ . '/../check_auth.php';

$title = 'Sıkça Sorulan Sorular';

// Silme işlemi
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM faqs WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: " . admin_url('pages/faqs.php?deleted=1'));
    exit;
}

// Yeni SSS Ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_faq'])) {
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $order = (int) $_POST['display_order'];

    $stmt = $db->prepare("INSERT INTO faqs (question, answer, display_order) VALUES (?, ?, ?)");
    $stmt->execute([$question, $answer, $order]);
    header("Location: " . admin_url('pages/faqs.php?success=1'));
    exit;
}

$faqs = $db->query("SELECT * FROM faqs ORDER BY display_order ASC, created_at DESC")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="admin-container">
    <div class="admin-header">
        <h1>Sıkça Sorulan Sorular</h1>
    </div>

    <!-- Ekleme Formu -->
    <div class="admin-card mb-4">
        <h2 class="card-title">Yeni SSS Ekle</h2>
        <form method="POST">
            <div class="form-item">
                <label>Soru</label>
                <input type="text" name="question" required class="form-control"
                    placeholder="Örn: Terapi süreci ne kadar sürer?">
            </div>
            <div class="form-item">
                <label>Cevap</label>
                <textarea name="answer" required class="form-control" rows="3"></textarea>
            </div>
            <div class="form-item">
                <label>Sıralama (Küçükten büyüğe)</label>
                <input type="number" name="display_order" value="0" class="form-control">
            </div>
            <button type="submit" name="add_faq" class="btn btn-primary">Kaydet</button>
        </form>
    </div>

    <!-- Liste -->
    <div class="admin-card">
        <h2 class="card-title">Mevcut Sorular</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Sıra</th>
                    <th>Soru</th>
                    <th>Cevap</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($faqs as $f): ?>
                    <tr>
                        <td width="50"><?php echo $f['display_order']; ?></td>
                        <td><strong><?php echo htmlspecialchars($f['question']); ?></strong></td>
                        <td><?php echo mb_strimwidth(htmlspecialchars($f['answer']), 0, 80, "..."); ?></td>
                        <td>
                            <a href="?delete=<?php echo $f['id']; ?>" class="btn-delete"
                                onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .mb-4 {
        margin-bottom: 2rem;
    }

    .card-title {
        font-size: 1.25rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .form-item {
        margin-bottom: 1rem;
    }

    .form-item label {
        display: block;
        margin-bottom: 0.4rem;
        font-weight: 600;
    }

    .form-control {
        width: 100%;
        padding: 0.6rem;
        border: 1px solid #ddd;
        border-radius: 6px;
    }
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
