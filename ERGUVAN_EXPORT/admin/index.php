<?php
// Session'ı sadece bir kez başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';
require_once 'includes/auth.php';
requireLogin();
require_once __DIR__ . '/../database/db.php';

$db = getDB();

$page = 'dashboard';
$page_title = 'Dashboard';
require_once 'includes/header.php';

// İstatistikleri al
$blog_count = $db->query("SELECT COUNT(*) as count FROM blog_posts")->fetch()['count'];
$slider_count = $db->query("SELECT COUNT(*) as count FROM sliders WHERE is_active = 1")->fetch()['count'];
$total_sliders = $db->query("SELECT COUNT(*) as count FROM sliders")->fetch()['count'];
$appointment_count = $db->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'")->fetch()['count'];
$total_appointments = $db->query("SELECT COUNT(*) as count FROM appointments")->fetch()['count'];

// Son eklenen blog yazıları
$recent_posts = $db->query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<div class="dashboard-welcome">
    <h3>Genel Bakış</h3>
    <p class="text-muted">Sitenizin anlık durum raporu ve önemli metrikler.</p>
</div>

<div class="dashboard-stats">
    <!-- Blog Yazıları -->
    <div class="stat-card">
        <div class="stat-icon-wrapper">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-card-title">Toplam Blog Yazısı</div>
            <div class="stat-card-value"><?php echo $blog_count; ?></div>
        </div>
    </div>

    <!-- Sliderlar -->
    <div class="stat-card green">
        <div class="stat-icon-wrapper">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-card-title">Aktif Sliderlar</div>
            <div class="stat-card-value"><?php echo $slider_count; ?></div>
        </div>
    </div>

    <!-- Bekleyen Randevular -->
    <div class="stat-card orange">
        <div class="stat-icon-wrapper">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-card-title">Bekleyen Randevular</div>
            <div class="stat-card-value"><?php echo $appointment_count; ?></div>
        </div>
    </div>

    <!-- Toplam Randevu -->
    <div class="stat-card purple" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
        <div class="stat-icon-wrapper">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-card-title">Toplam Randevu</div>
            <div class="stat-card-value"><?php echo $total_appointments; ?></div>
        </div>
    </div>
</div>

<div class="content-section">
    <div class="section-header">
        <div class="header-text">
            <h2>Son Eklenen Blog Yazıları</h2>
            <p class="text-muted">En son yayınladığınız içeriklerin listesi</p>
        </div>
        <a href="<?php echo admin_url('pages/appointments.php'); ?>" class="btn btn-primary btn-sm btn-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            Randevu Talepleri (<?php echo $appointment_count; ?>)
        </a>
    </div>

    <div class="table-container card-style">
        <table class="data-table">
            <thead>
                <tr>
                    <th width="40%">Başlık</th>
                    <th>Kategori</th>
                    <th>Tarih</th>
                    <th class="text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent_posts)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4">Henüz hiç blog yazısı eklenmemiş.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recent_posts as $post): ?>
                        <tr>
                            <td>
                                <div class="post-title-wrapper">
                                    <span class="post-title"><?php echo htmlspecialchars($post['title']); ?></span>
                                </div>
                            </td>
                            <td><span class="category-badge"><?php echo htmlspecialchars($post['category']); ?></span></td>
                            <td><?php echo date('d.m.Y', strtotime($post['created_at'])); ?></td>
                            <td class="text-right">
                                <div class="table-actions justify-end">
                                    <a href="<?php echo admin_url('pages/blog-edit.php?id=' . $post['id']); ?>"
                                        class="btn btn-warning btn-sm icon-btn" title="Düzenle">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                        Düzenle
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>