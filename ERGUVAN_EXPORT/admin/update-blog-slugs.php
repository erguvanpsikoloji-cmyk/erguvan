<?php
/**
 * Mevcut blog yazılarının slug'larını güncelle
 * Eksik slug'ları başlıktan otomatik oluşturur
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/../database/db.php';

$db = getDB();
$updated = 0;
$errors = [];

try {
    // Slug'ı olmayan veya boş olan blog yazılarını getir
    $posts = $db->query("SELECT id, title, slug FROM blog_posts WHERE slug IS NULL OR slug = ''")->fetchAll();
    
    foreach ($posts as $post) {
        // Başlıktan slug oluştur (PHP'de)
        $slug = mb_strtolower($post['title'], 'UTF-8');
        $slug = str_replace(['ğ', 'ü', 'ş', 'ı', 'ö', 'ç', 'Ğ', 'Ü', 'Ş', 'İ', 'Ö', 'Ç'], 
                           ['g', 'u', 's', 'i', 'o', 'c', 'g', 'u', 's', 'i', 'o', 'c'], $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Eğer slug hala boşsa, ID kullan
        if (empty($slug)) {
            $slug = 'blog-' . $post['id'];
        }
        
        // Slug'un benzersiz olup olmadığını kontrol et
        $checkStmt = $db->prepare("SELECT id FROM blog_posts WHERE slug = ? AND id != ?");
        $checkStmt->execute([$slug, $post['id']]);
        $counter = 1;
        $originalSlug = $slug;
        
        while ($checkStmt->fetch()) {
            $slug = $originalSlug . '-' . $counter;
            $checkStmt->execute([$slug, $post['id']]);
            $counter++;
        }
        
        // Slug'ı güncelle
        $updateStmt = $db->prepare("UPDATE blog_posts SET slug = ? WHERE id = ?");
        $updateStmt->execute([$slug, $post['id']]);
        $updated++;
    }
    
    // Tüm blog yazılarını listele (kontrol için)
    $all_posts = $db->query("SELECT id, title, slug FROM blog_posts ORDER BY id")->fetchAll();
    
} catch (Exception $e) {
    $errors[] = 'Hata: ' . $e->getMessage();
}

$page = 'blog';
$page_title = 'Blog Slug Güncelleme';
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-card">
    <h2>Blog Slug Güncelleme</h2>
    
    <?php if ($updated > 0): ?>
        <div class="success-message" style="background: #efe; border: 1px solid #cfc; color: #3c3; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <strong>✅ Başarılı:</strong> <?php echo $updated; ?> blog yazısının slug'ı güncellendi.
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="error-message" style="background: #fee; border: 1px solid #fcc; color: #c33; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <?php foreach ($errors as $error): ?>
                <p><strong>❌ Hata:</strong> <?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div style="margin-top: 20px;">
        <h3>Tüm Blog Yazıları ve Slug'ları:</h3>
        <table class="admin-table" style="margin-top: 15px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Başlık</th>
                    <th>Slug</th>
                    <th>URL</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_posts as $post): ?>
                <tr>
                    <td><?php echo $post['id']; ?></td>
                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                    <td><code><?php echo htmlspecialchars($post['slug']); ?></code></td>
                    <td><a href="<?php echo url('blog/' . $post['slug']); ?>" target="_blank"><?php echo url('blog/' . $post['slug']); ?></a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 30px;">
        <a href="<?php echo admin_url('pages/blog.php'); ?>" class="btn btn-primary">Blog Listesine Dön</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

