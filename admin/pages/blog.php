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

// Silme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $id = (int) $_POST['delete_id'];
        $db->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([$id]);
    }
    header("Location: " . admin_url('pages/blog.php'));
    exit;
}

// Arama ve filtreleme
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'date_desc';

$where = [];
$params = [];
if ($search) {
    $where[] = "(title LIKE ? OR excerpt LIKE ?)";
    $p = "%$search%";
    $params = [$p, $p];
}
if ($category_filter) {
    $where[] = "category = ?";
    $params[] = $category_filter;
}

$where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
$order_by = ($sort === 'date_asc') ? "ORDER BY created_at ASC" : "ORDER BY created_at DESC";

// Yazıları getir
$stmt = $db->prepare("SELECT * FROM blog_posts $where_sql $order_by");
$stmt->execute($params);
$posts = $stmt->fetchAll();

$categories = $db->query("SELECT DISTINCT category FROM blog_posts ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

$page = 'blog';
$page_title = 'Blog Yönetimi';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
    :root {
        --primary-pink: #ec4899;
        --dark-slate: #1e293b;
        --border-color: #f1f5f9;
    }

    .admin-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
    }

    .page-main-title {
        font-size: 2rem;
        font-weight: 800;
        color: var(--dark-slate);
        margin: 0;
    }

    .filter-card {
        background: #fff;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }

    .search-input {
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        padding: 12px 20px;
        width: 100%;
        transition: all 0.3s;
    }

    .search-input:focus {
        border-color: var(--primary-pink);
        outline: none;
        background: #fff;
    }

    .blog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
    }

    .blog-card {
        background: #fff;
        border-radius: 24px;
        border: 1px solid var(--border-color);
        overflow: hidden;
        transition: all 0.3s;
        position: relative;
    }

    .blog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-pink);
    }

    .card-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .card-body {
        padding: 25px;
    }

    .card-cat {
        color: var(--primary-pink);
        font-weight: 800;
        font-size: 0.75rem;
        text-transform: uppercase;
        margin-bottom: 10px;
        display: block;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark-slate);
        margin-bottom: 12px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 3.5rem;
    }

    .card-footer {
        padding: 0 25px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-action {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-edit {
        background: #fdf2f8;
        color: var(--primary-pink);
    }

    .btn-edit:hover {
        background: var(--primary-pink);
        color: #fff;
    }

    .btn-view {
        background: #f1f5f9;
        color: var(--dark-slate);
    }

    .btn-view:hover {
        background: var(--dark-slate);
        color: #fff;
    }

    .btn-delete {
        background: #fff1f2;
        color: #ef4444;
    }

    .btn-delete:hover {
        background: #ef4444;
        color: #fff;
    }

    .btn-add-new {
        background: var(--primary-pink);
        color: #fff;
        padding: 12px 25px;
        border-radius: 14px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
    }

    .btn-add-new:hover {
        background: #db2777;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(236, 72, 153, 0.4);
    }

    .empty-state {
        text-align: center;
        padding: 100px 20px;
        background: #fff;
        border-radius: 30px;
        border: 2px dashed #e2e8f0;
    }
</style>

<div class="admin-header-flex">
    <h1 class="page-main-title">📚 Blog Yazıları</h1>
    <a href="blog-add.php" class="btn-add-new">✨ Yeni Yazı Ekle</a>
</div>

<div class="filter-card">
    <form method="GET" style="display: flex; gap: 15px; align-items: center;">
        <div style="flex:1; position:relative;">
            <input type="text" name="search" class="search-input" placeholder="Yazılarda ara..."
                value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <select name="category" class="search-input" style="width: 200px;">
            <option value="">Tüm Kategoriler</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?php echo $c; ?>" <?php echo $category_filter === $c ? 'selected' : ''; ?>><?php echo $c; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-add-new" style="padding: 12px 20px;">Filtrele</button>
        <?php if ($search || $category_filter): ?>
            <a href="blog.php" style="color: var(--text-muted); text-decoration:none; font-weight:600;">Temizle</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($posts)): ?>
    <div class="empty-state">
        <h2 style="color:var(--dark-slate);">Henüz yazı bulunamadı.</h2>
        <p style="color:var(--text-muted);">Arama kriterlerinizi değiştirmeyi veya yeni bir yazı eklemeyi deneyin.</p>
    </div>
<?php else: ?>
    <div class="blog-grid">
        <?php foreach ($posts as $p): ?>
            <div class="blog-card">
                <img src="<?php echo webp_url($p['image']); ?>" class="card-img" alt="">
                <div class="card-body">
                    <span class="card-cat"><?php echo htmlspecialchars($p['category']); ?></span>
                    <h3 class="card-title"><?php echo htmlspecialchars($p['title']); ?></h3>
                    <div
                        style="color: var(--text-muted); font-size: 0.85rem; display: flex; align-items: center; gap: 15px; margin-top: 15px;">
                        <span>📅 <?php echo date('d.m.Y', strtotime($p['created_at'])); ?></span>
                        <span>⏱️ <?php echo $p['reading_time']; ?></span>
                    </div>
                </div>
                <div class="card-footer">
                    <div style="display: flex; gap: 10px;">
                        <a href="<?php echo url('blog/' . $p['slug']); ?>" target="_blank" class="btn-action btn-view"
                            title="Görüntüle">👁️</a>
                        <a href="blog-edit.php?id=<?php echo $p['id']; ?>" class="btn-action btn-edit" title="Düzenle">✏️</a>
                    </div>
                    <button type="button" class="btn-action btn-delete" onclick="deletePost(<?php echo $p['id']; ?>)"
                        title="Sil">🗑️</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form id="deleteForm" method="POST" style="display:none;">
    <?php echo csrfField(); ?>
    <input type="hidden" name="delete_id" id="delete_id">
</form>

<script>
    function deletePost(id) {
        if  (confirm('Dikkat! Bu yazıyı tamamen silmek üzeresiniz. Onaylıyor musunuz?')) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteForm').submit();
        }
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>