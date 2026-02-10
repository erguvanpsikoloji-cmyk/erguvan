<?php
require_once __DIR__ . '/../config.php';
$page = 'blog';
$page_title = 'Blog';
$page_description = 'Psikoloji ve mental sağlık konularında güncel blog yazıları. Anksiyete, depresyon, ilişki sorunları ve kişisel gelişim hakkında uzman görüşleri ve öneriler.';
$page_keywords = 'psikoloji blog, mental sağlık, anksiyete, depresyon, ilişki sorunları, kişisel gelişim, psikoloji makaleleri, terapi önerileri';
$page_type = 'website';
include '../includes/header.php';
require_once '../database/db.php';

try {
    $db = getDB();
} catch (Exception $e) {
    error_log('Database error in blog.php: ' . $e->getMessage());
    $db = null;
}

// Anahtar kelime filtresi
$keyword_filter = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// SQL sorgusu oluştur
$where = [];
$params = [];

if ($keyword_filter) {
    // Anahtar kelimeler alanında arama yap (LIKE ile)
    $where[] = "keywords LIKE ?";
    $params[] = '%' . $keyword_filter . '%';
}

$where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Blog yazılarını getir
if ($db) {
    $sql = "SELECT * FROM blog_posts $where_sql ORDER BY created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $all_posts = $stmt->fetchAll();
} else {
    $all_posts = [];
}

$categories = array_unique(array_column($all_posts, 'category'));
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title">Blog</h1>
        <?php if ($keyword_filter): ?>
            <p class="page-description">
                "<strong><?php echo htmlspecialchars($keyword_filter); ?></strong>" anahtar kelimesi ile ilgili yazılar
            </p>
        <?php else: ?>
            <p class="page-description">Psikoloji, kişisel gelişim ve ruh sağlığı hakkında faydalı yazılar</p>
        <?php endif; ?>
    </div>
</section>

<section class="blog-section section">
    <div class="container">
        <div class="blog-filter">
            <button class="filter-btn active" data-category="all">Tümü</button>
            <?php foreach ($categories as $category): ?>
                <button class="filter-btn"
                    data-category="<?php echo strtolower($category); ?>"><?php echo $category; ?></button>
            <?php endforeach; ?>
        </div>

        <?php if (empty($all_posts) && $keyword_filter): ?>
            <div class="no-results"
                style="text-align: center; padding: 4rem 2rem; background: var(--bg-gri); border-radius: 20px; margin: 2rem 0;">
                <i class="fas fa-search" style="font-size: 3rem; color: #cbd5e0; margin-bottom: 1.5rem;"></i>
                <h3 style="color: #2d3748; margin-bottom: 0.5rem;">Sonuç bulunamadı</h3>
                <p style="color: #718096; margin-bottom: 1.5rem;">
                    "<strong><?php echo htmlspecialchars($keyword_filter); ?></strong>" anahtar kelimesi ile ilgili yazı
                    bulunamadı.</p>
                <a href="<?php echo page_url('blog.php'); ?>" class="btn-premium" style="display: inline-block;">Tüm
                    Yazıları Görüntüle</a>
            </div>
        <?php else: ?>
            <div class="blog-grid">
                <?php foreach ($all_posts as $post): ?>
                    <article class="blog-card" data-category="<?php echo strtolower($post['category']); ?>">
                        <div class="blog-card-image">
                            <img src="<?php echo webp_url($post['image']); ?>"
                                alt="<?php echo htmlspecialchars($post['title']); ?>" loading="lazy">
                            <span class="blog-category"><?php echo htmlspecialchars($post['category']); ?></span>
                        </div>
                        <div class="blog-card-content">
                            <div class="blog-meta">
                                <span class="blog-date"><i class="far fa-calendar-alt"></i>
                                    <?php echo date('d.m.Y', strtotime($post['created_at'])); ?></span>
                                <span class="blog-reading-time"><i class="far fa-clock"></i>
                                    <?php echo htmlspecialchars($post['reading_time']); ?></span>
                            </div>
                            <h3 class="blog-card-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p class="blog-card-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                            <a href="<?php echo url('blog/' . $post['slug']); ?>" class="btn-randevu"
                                style="display:inline-block; margin-top:10px;">Devamını Oku</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>