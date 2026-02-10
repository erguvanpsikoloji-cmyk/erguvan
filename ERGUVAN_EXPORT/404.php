<?php
require_once __DIR__ . '/config.php';
$page_title = '404 - Sayfa Bulunamadı';
$page_description = 'Aradığınız sayfa bulunamadı. Ana sayfaya dönerek veya blog sayfasını ziyaret ederek devam edebilirsiniz.';
$page_keywords = '404, sayfa bulunamadı, hata';
$robots_content = 'noindex, nofollow';
include 'includes/header.php';
?>

<section class="section" style="min-height: 60vh; display: flex; align-items: center;">
    <div class="container">
        <div class="text-center">
            <div style="font-size: 120px; font-weight: 700; color: #e2e8f0; margin-bottom: 20px;">404</div>
            <h1 style="font-size: 36px; margin-bottom: 15px; color: #1e293b;">Sayfa Bulunamadı</h1>
            <p style="font-size: 18px; color: #64748b; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
                Aradığınız sayfa mevcut değil veya taşınmış olabilir.
            </p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo url(); ?>" class="btn btn-primary">Ana Sayfaya Dön</a>
                <a href="<?php echo page_url('blog.php'); ?>" class="btn btn-secondary">Blog'a Git</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

