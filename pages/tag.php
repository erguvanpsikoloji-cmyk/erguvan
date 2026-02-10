<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/db.php';

$tag = isset($_GET['tag']) ? trim($_GET['tag']) : '';
$decoded_tag = urldecode($tag);

if (!$tag) {
    header("Location: " . url('blog'));
    exit;
}

$page = 'blog';
$page_title = 'Etiket: ' . htmlspecialchars($decoded_tag);
$page_description = $decoded_tag . ' etiketiyle ilgili blog yazıları.';
$page_keywords = $decoded_tag . ', psikoloji blog, makaleler';

include '../includes/header.php';

try {
    $db = getDB();
} catch (Exception $e) {
    error_log('Database error in tag.php: ' . $e->getMessage());
    $db = null;
}

// Blog yazılarını etiketle filtrele
$all_posts = [];
if ($db) {
    // Virgülle ayrılmış etiketler içinde arama (basit LIKE yapısı, daha gelişmişi için FIND_IN_SET veya JSON gerekir ama text olduğu için LIKE %...% yeterli)
    $stmt = $db->prepare("SELECT * FROM blog_posts WHERE tags LIKE ? ORDER BY created_at DESC");
    $stmt->execute(['%' . $decoded_tag . '%']);
    $all_posts = $stmt->fetchAll();
}

$categories = [];
if (!empty($all_posts)) {
    $categories = array_unique(array_column($all_posts, 'category'));
}
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title">Etiket: <span style="color:var(--primary-pink);">
                <?php echo htmlspecialchars($decoded_tag); ?>
            </span></h1>
        <p class="page-description">Bu konuyla ilgili
            <?php echo count($all_posts); ?> yazı bulundu.
        </p>
    </div>
</section>

<section class="blog-section section">
    <div class="container">
        <?php if (empty($all_posts)): ?>
            <div class="no-results"
                style="text-align: center; padding: 4rem 2rem; background: var(--bg-gri); border-radius: 20px; margin: 2rem 0;">
                <i class="fas fa-tag" style="font-size: 3rem; color: #cbd5e0; margin-bottom: 1.5rem;"></i>
                <h3 style="color: #2d3748; margin-bottom: 0.5rem;">Sonuç bulunamadı</h3>
                <p style="color: #718096; margin-bottom: 1.5rem;">
                    "<strong>
                        <?php echo htmlspecialchars($decoded_tag); ?>
                    </strong>" etiketiyle ilgili yazı henüz eklenmemiş.</p>
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
                            <span class="blog-category">
                                <?php echo htmlspecialchars($post['category']); ?>
                            </span>
                        </div>
                        <div class="blog-card-content">
                            <div class="blog-meta">
                                <span class="blog-date"><i class="far fa-calendar-alt"></i>
                                    <?php echo date('d.m.Y', strtotime($post['created_at'])); ?>
                                </span>
                                <!-- Okuma süresi kaldırıldı -->
                            </div>
                            <h3 class="blog-card-title">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </h3>
                            <p class="blog-card-excerpt">
                                <?php echo htmlspecialchars($post['excerpt']); ?>
                            </p>
                            <a href="<?php echo url('blog/' . $post['slug']); ?>" class="blog-read-more">Devamını Oku <i
                                    class="fas fa-arrow-right"></i></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>