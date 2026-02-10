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
                <a href="<?php echo page_url('blog.php'); ?>" style="color: var(--primary); text-decoration: underline; margin-left: 10px;">Tüm yazıları görüntüle</a>
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
            <?php foreach($categories as $category): ?>
                <button class="filter-btn" data-category="<?php echo strtolower($category); ?>"><?php echo $category; ?></button>
            <?php endforeach; ?>
        </div>

        <?php if (empty($all_posts) && $keyword_filter): ?>
            <div class="no-results" style="text-align: center; padding: 4rem 2rem; background: var(--bg-light); border-radius: var(--radius); margin: 2rem 0;">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--text-light); margin: 0 auto 1rem;">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                <h3 style="color: var(--text-dark); margin-bottom: 0.5rem;">Sonuç bulunamadı</h3>
                <p style="color: var(--text-medium); margin-bottom: 1.5rem;">"<strong><?php echo htmlspecialchars($keyword_filter); ?></strong>" anahtar kelimesi ile ilgili yazı bulunamadı.</p>
                <a href="<?php echo page_url('blog.php'); ?>" class="btn btn-primary">Tüm Yazıları Görüntüle</a>
            </div>
        <?php else: ?>
        <div class="blog-grid">
            <?php foreach($all_posts as $post): ?>
            <article class="blog-card" data-category="<?php echo strtolower($post['category']); ?>">
                <div class="blog-card-image">
                    <img src="<?php echo $post['image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" width="400" height="250" loading="lazy" decoding="async">
                    <span class="blog-category"><?php echo $post['category']; ?></span>
                </div>
                <div class="blog-card-content">
                    <div class="blog-meta">
                        <span class="blog-date">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <?php echo date('d.m.Y', strtotime($post['created_at'])); ?>
                        </span>
                        <span class="blog-reading-time">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <?php echo $post['reading_time']; ?>
                        </span>
                    </div>
                    <h3 class="blog-card-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p class="blog-card-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                    <a href="<?php echo url('blog/' . $post['slug']); ?>" class="blog-read-more">Devamını Oku →</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
