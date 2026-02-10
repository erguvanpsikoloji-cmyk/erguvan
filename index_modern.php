<?php
// Modern tasarım entegrasyonu
ob_start();
ini_set('display_errors', 0);
error_reporting(0);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/db.php';

try {
    $db = getDB();

    // Verileri çek
    $services = $db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
    $team_members = $db->query("SELECT * FROM team_members WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
    $recent_posts = $db->query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 3")->fetchAll();
    $testimonials = $db->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
    $faqs = $db->query("SELECT * FROM faqs WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
    $office_images = $db->query("SELECT * FROM office_images WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();

} catch (Exception $e) {
    error_log('Database error: ' . $e->getMessage());
}

// Fallback data if DB empty
if (empty($services)) {
    $services = [
        ['title' => 'Bireysel Terapi', 'description' => 'Kendinizi keşfetme yolculuğunda yanınızdayız.', 'icon' => 'fas fa-user'],
        ['title' => 'Oyun Terapisi', 'description' => 'Çocukların dünyasına oyunla dokunuyoruz.', 'icon' => 'fas fa-puzzle-piece']
    ];
}

?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erguvan Psikoloji | Modern & Minimalist Terapi</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern-style.css">
</head>

<body>

    <header class="navbar">
        <div class="container">
            <div class="logo-wrapper">
                <a href="/" class="logo-link">
                    <img src="assets/images/logo.webp" alt="Erguvan Psikoloji Logo" class="main-logo">
                </a>
            </div>
            <nav class="nav-links">
                <a href="#home" class="active">Ana Sayfa</a>
                <a href="#services">Hizmetlerimiz</a>
                <a href="#about">Ekibimiz</a>
                <a href="#blog">Blog</a>
                <a href="#office">Ofisimiz</a>
                <a href="#contact">İletişim</a>
                <a href="#contact" class="btn btn-premium">Randevu Al</a>
            </nav>
            <div class="menu-toggle"><i class="fas fa-bars"></i></div>
        </div>
    </header>

    <main>
        <section id="home" class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Zihninizdeki Düğümü <br><span>Birlikte Çözelim.</span></h1>
                    <p>Modern yaklaşımlar ve bilimsel temelli terapi yöntemleriyle, daha sağlıklı bir gelecek için
                        yanınızdayız.</p>
                    <div class="hero-btns">
                        <a href="#contact" class="btn btn-primary">Randevu Al</a>
                        <a href="#services" class="btn btn-secondary">Hizmetlerimiz</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="assets/images/hero-psikolojik-destek.jpg" alt="Psikolojik Destek">
                </div>
            </div>
        </section>

        <section id="services" class="services">
            <div class="container">
                <div class="section-title">
                    <h2>Hizmetlerimiz</h2>
                    <p>Size en uygun terapi yöntemini birlikte belirleyelim.</p>
                </div>
                <div class="services-grid">
                    <?php foreach ($services as $s): ?>
                        <div class="service-card">
                            <div class="card-icon"><i class="<?php echo $s['icon'] ?: 'fas fa-heart'; ?>"></i></div>
                            <h3>
                                <?php echo htmlspecialchars($s['title']); ?>
                            </h3>
                            <p>
                                <?php echo htmlspecialchars($s['description']); ?>
                            </p>
                            <a href="#contact" class="card-cta">Detaylı Bilgi <i class="fas fa-arrow-right"></i></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="about" class="about">
            <div class="container">
                <div class="section-title">
                    <h2>Uzman Ekibimiz</h2>
                </div>
                <div class="team-grid">
                    <?php foreach ($team_members as $m): ?>
                        <div class="team-card">
                            <img src="<?php echo $m['image']; ?>" alt="<?php echo $m['name']; ?>">
                            <h3>
                                <?php echo htmlspecialchars($m['name']); ?>
                            </h3>
                            <p>
                                <?php echo htmlspecialchars($m['title']); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="blog" class="blog">
            <div class="container">
                <div class="section-title">
                    <h2>Son Blog Yazıları</h2>
                </div>
                <div class="blog-grid">
                    <?php foreach ($recent_posts as $post): ?>
                        <article class="blog-card">
                            <div class="blog-img"><img src="<?php echo $post['image']; ?>" alt=""></div>
                            <div class="blog-content">
                                <h3>
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </h3>
                                <p>
                                    <?php echo htmlspecialchars($post['excerpt']); ?>
                                </p>
                                <a href="blog/<?php echo $post['slug']; ?>" class="read-more">Devamını Oku</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="office" class="office">
            <div class="container">
                <div class="section-title">
                    <h2>Ofisimiz</h2>
                </div>
                <div class="office-zigzag">
                    <?php foreach ($office_images as $index => $img): ?>
                        <div class="office-row">
                            <div class="office-img"><img src="<?php echo $img['image']; ?>" alt=""></div>
                            <div class="office-text">
                                <h3>
                                    <?php echo htmlspecialchars($img['title']); ?>
                                </h3>
                                <p>
                                    <?php echo htmlspecialchars($img['description']); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="testimonials">
            <div class="container">
                <div class="section-title">
                    <h2>Danışan Yorumları</h2>
                </div>
                <div class="swiper testimonial-slider">
                    <div class="swiper-wrapper">
                        <?php foreach ($testimonials as $t): ?>
                            <div class="swiper-slide">
                                <div class="testimonial-card">
                                    <div class="stars">★★★★★</div>
                                    <p>"
                                        <?php echo htmlspecialchars($t['comment']); ?>"
                                    </p>
                                    <div class="user-info">
                                        <strong>
                                            <?php echo htmlspecialchars($t['name']); ?>
                                        </strong>
                                        <span>
                                            <?php echo htmlspecialchars($t['source']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>

        <section id="contact" class="contact">
            <div class="container">
                <div class="contact-wrapper">
                    <div class="contact-info">
                        <h3>İletişim Bilgilerimiz</h3>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <p>Şehremini, Millet Cd. Aydın apt No:131 Daire 4 Fatih/İstanbul</p>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone-alt"></i>
                            <p>0551 176 52 85</p>
                        </div>
                    </div>
                    <div class="contact-form-container">
                        <form class="appointment-form">
                            <h3>Randevu Talebi</h3>
                            <input type="text" placeholder="Adınız Soyadınız" required>
                            <input type="tel" placeholder="Telefon Numaranız" required>
                            <select required>
                                <option value="" disabled selected>Hizmet Seçiniz</option>
                                <option value="yetiskin">Yetişkin Terapisi</option>
                                <option value="oyun">Oyun Terapisi</option>
                            </select>
                            <button type="submit" class="btn btn-premium btn-block">Gönder</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 Erguvan Psikoloji. Tüm Hakları Saklıdır.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Swiper('.testimonial-slider', {
                pagination: { el: '.swiper-pagination', clickable: true },
                autoplay: { delay: 5000 },
                loop: true
            });
        });
    </script>
</body>

</html>