<?php
// Hata raporlamayı aç
ob_start(); // Start Output Buffering for Minification
// Hata raporlamayı kapat (Production)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Global Exception Handler
set_exception_handler(function ($e) {
    echo "<h1>Kritik Hata (500)</h1>";
    echo "<p><strong>Mesaj:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Dosya:</strong> " . $e->getFile() . " (Satır: " . $e->getLine() . ")</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    exit;
});

require_once __DIR__ . '/config.php';
$page = 'home';
$page_title = 'Ana Sayfa - Erguvan Psikoloji | Uzman Klinik Psikolog';
$page_description = 'Erguvan Psikoloji - İstanbul\'da profesyonel psikolojik danışmanlık ve terapi hizmetleri. Anksiyete, depresyon, çift terapisi ve aile danışmanlığı için uzman psikolog desteği. Online ve yüz yüze terapi seçenekleri.';
$page_keywords = 'psikolog İstanbul, psikolojik danışmanlık, terapi, anksiyete tedavisi, depresyon terapisi, çift terapisi, aile danışmanlığı, online terapi, bireysel terapi, psikolog Fatih, mental sağlık';
$page_type = 'website';

// Path Sanitize Helper
function sanitizePath($path)
{
    return str_replace('\\', '/', $path);
}

require_once __DIR__ . '/database/db.php';

// Veritabanı bağlantısı ve veri çekme işlemleri
try {
    $db = getDB();

    // Sliderları çek
    $sliders = $db->query("SELECT * FROM sliders WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
    foreach ($sliders as &$item) {
        if (isset($item['image']))
            $item['image'] = sanitizePath($item['image']);
    }

    // Hizmetleri çek
    $services = $db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC, created_at ASC")->fetchAll();
    foreach ($services as &$item) {
        if (isset($item['image']))
            $item['image'] = sanitizePath($item['image']);
    }

    // Ekip üyelerini çek
    $team_members = $db->query("SELECT * FROM team_members WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
    foreach ($team_members as &$item) {
        if (isset($item['image']))
            $item['image'] = sanitizePath($item['image']);
    }

    // Sertifikaları çek
    $certificates_query = $db->query("SELECT * FROM certificates WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC");
    if ($certificates_query) {
        $certificates = $certificates_query->fetchAll();
        foreach ($certificates as &$item) {
            if (isset($item['image']))
                $item['image'] = sanitizePath($item['image']);
        }
    } else {
        $certificates = [];
    }

    // Ofis resimlerini çek (varsa)
    $office_images_query = $db->query("SELECT * FROM office_images WHERE is_active = 1 ORDER BY display_order ASC");
    if ($office_images_query) {
        $office_images = $office_images_query->fetchAll();
        foreach ($office_images as &$item) {
            if (isset($item['image']))
                $item['image'] = sanitizePath($item['image']);
        }
    }

    // Hakkımızda bilgisini çek
    $about_query = $db->query("SELECT * FROM about_us LIMIT 1");
    $about_data = $about_query->fetch();

    // Site Ayarlarını çek
    $settings_query = $db->query("SELECT setting_key, setting_value FROM site_settings");
    $site_settings = [];
    while ($row = $settings_query->fetch()) {
        $site_settings[$row['setting_key']] = $row['setting_value'];
    }

    // Aktif yorumları çek
    $testimonials = $db->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC")->fetchAll();

    // SSS'leri çek
    $faqs = $db->query("SELECT * FROM faqs WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();

} catch (Exception $e) {
    error_log('Database error in index.php: ' . $e->getMessage());
    $sliders = [];
    $services = [];
    $team_members = [];
    $certificates = [];
}

// Fallback Data (MockPDO veya boş veri durumunda)
if (empty($sliders)) {
    $sliders = [
        [
            'title' => 'Oyun Terapisi',
            'subtitle' => 'Kelimelerin yetmediği yerde oyun konuşur',
            'description' => 'Çocukların kendini ifade etme dili oyundur. Oyun terapisi, çocuğun duygularını, yaşadığı güçlükleri ve iç dünyasını oyun yoluyla güvenli bir ortamda ortaya koymasını sağlar. Terapist rehberliğinde gerçekleştirilen bu süreç; duygusal düzenlemeyi, problem çözme becerilerini, özgüveni ve sağlıklı davranış gelişimini destekler.',
            'image' => 'assets/images/hero-oyun-terapisi.jpg',
            'button_text' => 'Randevu Al',
            'button_link' => '#iletisim',
            'btn_text' => 'Randevu Al',
            'btn_link' => '#iletisim'
        ],
        [
            'title' => 'Psikolojik Desteğe',
            'subtitle' => 'Adım Atın',
            'description' => 'Profesyonel psikolojik danışmanlık ile daha mutlu ve dengeli bir yaşam için size yardımcı oluyorum. Online ve yüz yüze terapi seçenekleriyle hizmetinizdeyim.',
            'image' => 'assets/images/hero-psikolojik-destek.jpg',
            'button_text' => 'Randevu Al',
            'button_link' => '#iletisim',
            'btn_text' => 'Randevu Al',
            'btn_link' => '#iletisim'
        ],
        [
            'title' => 'Bilişsel Davranışçı Terapi',
            'subtitle' => 'Bilişsel Davranışçı Terapi İle Psikoterapi',
            'description' => 'Bilişsel Davranışçı Terapi düşünce-duygu-davranış ilişkisini temel alarak bireyin işlevsel olmayan düşünce kalıplarını fark etmesine ve değiştirmesine yardımcı olan kanıta dayalı bir psikoterapi yaklaşımıdır. Çocuk ve ergenlerde oyun, metaforlar ve gelişim düzeyine uygun etkinliklerle beceri öğretimi merkezdedir. Yetişkinlerde ise bilişsel yeniden yapılandırma, duygu düzenleme ve davranışsal müdahaleler süreçte aktif olarak kullanılır. BDT, her yaş grubunda sorun çözme ve sağlıklı baş etme becerilerini güçlendirerek kalıcı değişim sağlamayı hedefler.',
            'image' => 'assets/images/hero-bdt.jpg',
            'button_text' => 'Randevu Al',
            'button_link' => '#iletisim',
            'btn_text' => 'Randevu Al',
            'btn_link' => '#iletisim'
        ]
    ];
}

if (empty($services)) {
    $services = [
        [
            'title' => 'Bireysel Terapi',
            'description' => 'Kişisel zorluklarla başa çıkmanıza yardımcı olan birebir görüşmeler.',
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
            'features' => "Anksiyete ve Depresyon\nStres Yönetimi\nÖzgüven Sorunları",
            'is_featured' => 1
        ],
        [
            'title' => 'Aile Çift Terapisi',
            'description' => 'İlişkinizdeki sorunları çözmek ve aile içi iletişimi güçlendirmek için destek.',
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
            'features' => "İletişim Problemleri\nEvlilikte Uyum Sorunları\nBoşanma Süreci",
            'is_featured' => 1
        ],
        [
            'title' => 'Online Terapi',
            'description' => 'Dilediğiniz yerden, konforlu bir şekilde uzman psikolog desteğine ulaşın.',
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>',
            'features' => "Görüntülü Görüşme\nEsnek Planlama\nDünyanın Her Yerinden",
            'is_featured' => 1
        ],
        [
            'title' => 'Psikolojik Ölçekler',
            'description' => 'Bilimsel geçerliliği olan test ve envanterlerle detaylı analiz ve değerlendirme.',
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
            'features' => "Kişilik Testleri\nGelişim Testleri\nKlinik Ölçekler",
            'is_featured' => 1
        ],
        [
            'title' => 'Oyun Terapisi',
            'description' => 'Çocukların duygusal ve davranışsal sorunlarını oyun yoluyla ifade etmelerini sağlayan terapi yöntemi.',
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>',
            'features' => "Duygusal Düzenleme\nTravma ve Yas\nDavranış Sorunları",
            'is_featured' => 1
        ],
        [
            'title' => 'Çocuk ve Ergen Terapisi',
            'description' => 'Gençlerin gelişimsel süreçlerinde karşılaştıkları zorluklara yönelik destek.',
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path></svg>',
            'features' => "Okul Problemleri\nSınav Kaygısı\nDavranış Sorunları",
            'is_featured' => 1
        ]
    ];
}

if (empty($team_members)) {
    $team_members = [
        [
            'name' => 'Uzman Psikolog Sena Ceren Parmaksız',
            'title' => 'Uzman Klinik Psikolog',
            'image' => 'assets/images/team/sena.jpg',
            'education' => "Üsküdar Üniversitesi Psikoloji Bölümü (Lisans)\nÜsküdar Üniversitesi Klinik Psikoloji (Yüksek Lisans)",
            'certificates' => "Oyun Terapisi Eğitimi\nBilişsel Davranışçı Terapi Eğitimi\nÇocuk ve Ergen Terapisi Eğitimi",
            'specialties' => "Oyun Terapisi\nÇocuk Terapisi\nErgen Terapisi\nYetişkin Terapisi\nBilişsel Davranışçı Terapi\nEbeveyn Danışmanlığı\nSınav Kaygısı\nDikkat Eksikliği"
        ]
    ];
}

if (empty($testimonials)) {
    $testimonials = [
        [
            'name' => 'Nihat Duman',
            'comment' => 'Sena Hanım\'a tavsiye üzerine gittim ilgi ve alakası çok iyi. Gerçek bir psikolog arıyorsanız doğru adrestesiniz',
            'rating' => 5,
            'source' => 'Google',
            'date_info' => 'Bir ay önce',
            'avatar_char' => 'N'
        ],
        [
            'name' => 'Ayşe Yılmaz',
            'comment' => 'Sena Hanım\'ı kesinlikle tavsiye ederim. Çok anlayışlı ve profesyonel. Online terapi seansları çok verimli geçti.',
            'rating' => 5,
            'source' => 'Google',
            'date_info' => '3 ay önce',
            'avatar_char' => 'A'
        ]
    ];
}

if (empty($faqs)) {
    $faqs = [
        [
            'question' => 'Seanslar ne kadar sürüyor?',
            'answer' => 'Bireysel terapi seanslarımız ortalama 45-50 dakika sürmektedir.'
        ],
        [
            'question' => 'Kaç seans gelmem gerekiyor?',
            'answer' => 'Terapi süreci, çalışılan konuya ve kişinin ihtiyaçlarına göre değişiklik gösterir.'
        ],
        [
            'question' => 'Online terapi ile yüz yüze terapi arasında fark var mı?',
            'answer' => 'Bilimsel çalışmalar, online terapinin birçok konuda yüz yüze terapi kadar etkili olduğunu göstermektedir.'
        ],
        [
            'question' => 'Gizlilik ilkesine nasıl uyuluyor?',
            'answer' => 'Terapi odasında konuşulan her şey, mesleki etik kurallar ve yasalar çerçevesinde terapist ile danışan arasında gizli tutulur.'
        ]
    ];
}


// Slider değişkenini ayarla (kullanılan kod şablonuna uyum için)
$slider = !empty($sliders) ? $sliders[0] : null;

// LCP Optimizasyonu için Preload Görselini Belirle (v26)
if (!empty($sliders)) {
    // İlk slider görseli LCP adayıdır
    $preload_image = webp_url($sliders[0]['image']);
    $preload_image_mobile = webp_url($sliders[0]['image'], 'mobile');
} else {
    // Fallback image (Static Hero default)
    $preload_image = webp_url('assets/img/hero-1.webp');
    $preload_image_mobile = webp_url('assets/img/hero-1.webp', 'mobile');
}

include __DIR__ . '/includes/header.php';
?>

<section id="home" class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <span class="hero-subtitle">Birlikte, Daha İyiye.</span>
                <h1 class="hero-title">
                    Erguvan Psikoloji <br>
                    <span class="highlight">Danışmanlık Merkezi</span>
                </h1>
                <p class="hero-description">
                    Modern yaklaşımlar ve bilimsel temelli terapi yöntemleriyle, daha sağlıklı ve dengeli bir gelecek
                    için yanınızdayız. Profesyonel kadromuzla kendinizi keşfetme yolculuğunuzda size rehberlik ediyoruz.
                </p>
                <div class="hero-buttons">
                    <a href="#randevu" class="btn btn-primary btn-lg" aria-label="Randevu Al">Randevu Al</a>
                    <a href="#hizmetler" class="btn btn-secondary btn-lg" aria-label="Hizmetlerimiz">Hizmetlerimiz</a>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-image-wrapper">
                    <picture>
                        <source media="(max-width: 600px)"
                            srcset="<?php echo webp_url($slider['image'] ?? 'assets/images/hero-psikolojik-destek.jpg', 'mobile'); ?>">
                        <img src="<?php echo webp_url($slider['image'] ?? 'assets/images/hero-psikolojik-destek.jpg'); ?>"
                            alt="Erguvan Psikoloji Danışmanlık Merkezi" class="aesthetic-image" width="600" height="411"
                            loading="eager" fetchpriority="high" decoding="async">
                    </picture>
                    <div class="hero-trust-badge">
                        <div class="badge-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="badge-text">Uzman Klinik <br>Psikolog Desteği</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="hizmetler" class="services section bg-soft">
    <div class="container">
        <div class="section-title text-center">
            <span class="subtitle">Size Nasıl Yardımcı Olabiliriz?</span>
            <h2>Hizmetlerimiz</h2>
            <p>Size en uygun terapi yöntemini birlikte belirleyerek, kişiselleştirilmiş destek sunuyoruz.</p>
        </div>
        <div class="services-grid">
            <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <div class="service-icon">
                        <?php if ($service['icon']): ?>
                            <?php echo $service['icon']; ?>
                        <?php else: ?>
                            <i class="fas fa-heart"></i>
                        <?php endif; ?>
                    </div>
                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                    <ul class="service-features">
                        <?php
                        $features = explode("\n", $service['features']);
                        foreach (array_slice($features, 0, 3) as $feature):
                            if (trim($feature)): ?>
                                <li><i class="fas fa-check"></i> <?php echo htmlspecialchars(trim($feature)); ?></li>
                            <?php endif;
                        endforeach;
                        ?>
                    </ul>
                    <a href="#randevu" class="btn-card">Bilgi Al <i class="fas fa-arrow-right"></i></a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>



<section id="ekibimiz" class="team section">
    <div class="container">
        <div class="section-title text-center">
            <span class="subtitle">Güvenilir Uzman Kadromuz</span>
            <h2>Uzman Ekibimiz</h2>
            <p>Deneyimli ve uzman psikolog kadromuzla, her adımda yanınızdayız.</p>
        </div>
        <div class="team-grid">
            <?php foreach ($team_members as $member): ?>
                <div class="team-card">
                    <div class="team-image-wrapper">
                        <img src="<?php echo webp_url($member['image']); ?>"
                            alt="<?php echo htmlspecialchars($member['name']); ?>" class="team-image" loading="lazy"
                            width="400" height="500">
                    </div>
                    <div class="team-content">
                        <h3 class="team-name"><?php echo htmlspecialchars($member['name']); ?></h3>
                        <p class="team-title"><?php echo htmlspecialchars($member['title']); ?></p>

                        <div class="team-specialties">
                            <?php
                            $specialties = is_array($member['specialties']) ? $member['specialties'] : array_filter(array_map('trim', explode("\n", $member['specialties'])));
                            foreach (array_slice($specialties, 0, 4) as $specialty): ?>
                                <span class="specialty-pill"><?php echo htmlspecialchars($specialty); ?></span>
                            <?php endforeach; ?>
                        </div>

                        <div class="team-education">
                            <i class="fas fa-graduation-cap"></i>
                            <div class="edu-text">
                                <?php echo nl2br(htmlspecialchars($member['education'])); ?>
                            </div>
                        </div>

                        <a href="#iletisim" class="btn-team">Randevu Al <i class="fas fa-calendar-alt"></i></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="sertifikalar" class="certificates section bg-white">
    <div class="container">
        <div class="section-title text-center">
            <span class="subtitle">Akademik Yetkinlik</span>
            <h2>Sertifikalarımız</h2>
            <p>Sürekli gelişim ilkemiz doğrultusunda aldığımız güncel eğitimler ve uzmanlık sertifikalarımız.</p>
        </div>

        <div class="certificates-grid">
            <?php
            foreach (array_slice($certificates, 0, 4) as $cert): ?>
                <div class="certificate-card">
                    <div class="certificate-inner">
                        <img src="<?php echo webp_url($cert['image']); ?>"
                            alt="<?php echo htmlspecialchars($cert['title']); ?>" class="certificate-img" loading="lazy">
                        <div class="certificate-overlay">
                            <a href="<?php echo webp_url($cert['image']); ?>" target="_blank" class="zoom-btn"
                                aria-label="Büyüt">
                                <i class="fas fa-search-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="ofisimiz" class="office section">
    <div class="container">
        <div class="section-title text-center">
            <span class="subtitle">Çalışma Ortamımız</span>
            <h2>Ofisimiz</h2>
            <p>Sıcak ve konforlu ortamımızda sizleri ağırlamaktan mutluluk duyarız.</p>
        </div>
        <div class="office-grid-2x2">
            <?php
            $office_imgs = ['ofis/ofis-1.webp', 'ofis/ofis-2.webp', 'ofis/ofis.webp', 'ofis/beeetül.webp'];
            foreach ($office_imgs as $img): ?>
                <div class="office-item">
                    <img src="<?php echo webp_url($img); ?>" alt="Ofisimiz" class="office-img" loading="lazy">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="blog-preview section bg-white">
    <div class="container">
        <div class="section-title text-center">
            <span class="subtitle">Güncel Paylaşımlar</span>
            <h2>Blog Yazıları</h2>
            <p>Psikoloji ve kişisel gelişim hakkında güncel yazılarımızı okuyun.</p>
        </div>
        <div class="blog-grid">
            <?php foreach ($recent_posts as $post): ?>
                <article class="blog-card">
                    <div class="blog-image">
                        <img src="<?php echo webp_url($post['image']); ?>"
                            alt="<?php echo htmlspecialchars($post['title']); ?>" loading="lazy">
                        <span class="category"><?php echo htmlspecialchars($post['category']); ?></span>
                    </div>
                    <div class="blog-content">
                        <div class="meta">
                            <span><i class="far fa-calendar"></i>
                                <?php echo date('d.m.Y', strtotime($post['created_at'])); ?></span>
                            <span><i class="far fa-clock"></i> <?php echo htmlspecialchars($post['reading_time']); ?>
                                dk</span>
                        </div>
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        <a href="<?php echo url('blog/' . $post['slug']); ?>" class="read-more">Devamını Oku <i
                                class="fas fa-chevron-right"></i></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="<?php echo page_url('blog.php'); ?>" class="btn btn-secondary">Tüm Yazıları Gör</a>
        </div>
    </div>
</section>

<section class="testimonials section bg-soft">
    <div class="container">
        <div class="section-title text-center">
            <span class="subtitle">Danışan Deneyimleri</span>
            <h2>Danışanlarımız Ne Diyor?</h2>
            <p>Google yorumlarından gerçek danışan deneyimleri ve geri bildirimler.</p>
        </div>

        <div class="testimonials-slider-wrapper">
            <div class="swiper testimonials-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($testimonials as $t): ?>
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <div class="testimonial-header">
                                    <div class="avatar">
                                        <?php echo htmlspecialchars($t['avatar_char'] ?: substr($t['name'], 0, 1)); ?>
                                    </div>
                                    <div class="info">
                                        <div class="name"><?php echo htmlspecialchars($t['name']); ?></div>
                                        <div class="date"><?php echo htmlspecialchars($t['date_info']); ?></div>
                                    </div>
                                </div>
                                <div class="rating">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i < ($t['rating'] ?: 5) ? 'active' : ''; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="comment"><?php echo htmlspecialchars($t['comment']); ?></p>
                                <div class="source"><i class="fab fa-google"></i> Google Yorumu</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials initialization -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swiper !== 'undefined') {
            const testimonialsSwiper = new Swiper('.testimonials-swiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    768: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 }
                }
            });
        }
    });

    // Sertifika Tümünü Gör butonu için opsiyonel script
    const loadMoreBtn = document.getElementById('loadMoreCerts');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', () => {
            const hiddenCerts = document.querySelectorAll('.certificate-card.hidden');
            hiddenCerts.forEach(cert => {
                cert.style.display = 'block';
                setTimeout(() => cert.classList.remove('hidden'), 10);
            });
            loadMoreBtn.style.display = 'none';
        });
    }
</script>


<section id="iletisim" class="contact section">
    <div class="container">
        <div class="section-title text-center">
            <span class="subtitle">Bize Ulaşın</span>
            <h2>İletişim</h2>
            <p>Size nasıl yardımcı olabileceğimizi konuşmak için bizimle iletişime geçin.</p>
        </div>

        <div class="contact-wrapper">
            <div class="contact-info-grid">
                <div class="info-item">
                    <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="text">
                        <h3>Adres</h3>
                        <p><?php echo htmlspecialchars($site_settings['address'] ?? 'Şehremini, Millet Cd. 34098 Fatih/İstanbul'); ?>
                        </p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="icon"><i class="fas fa-phone"></i></div>
                    <div class="text">
                        <h3>Telefon</h3>
                        <p><a
                                href="tel:<?php echo str_replace(' ', '', $site_settings['phone'] ?? '05511765285'); ?>"><?php echo htmlspecialchars($site_settings['phone'] ?? '05511765285'); ?></a>
                        </p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="icon"><i class="fas fa-envelope"></i></div>
                    <div class="text">
                        <h3>E-posta</h3>
                        <p><a href="mailto:info@erguvanpsikoloji.com">info@erguvanpsikoloji.com</a></p>
                    </div>
                </div>
            </div>

            <div class="appointment-container" id="randevu">
                <div class="appointment-header">
                    <h3>Randevu Formu</h3>
                    <p>En kısa sürede size dönüş sağlayacağız.</p>
                </div>
                <form id="appointmentForm" class="modern-form">
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Adınız Soyadınız" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="E-posta Adresiniz" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="tel" name="phone" placeholder="Telefon Numaranız" required>
                        </div>
                        <div class="form-group">
                            <select name="service" required>
                                <option value="">Hizmet Seçiniz</option>
                                <?php foreach ($services as $s): ?>
                                    <option value="<?php echo htmlspecialchars($s['title']); ?>">
                                        <?php echo htmlspecialchars($s['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea name="message" rows="4" placeholder="Mesajınız (Opsiyonel)"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Gönder</button>
                </form>
            </div>
        </div>
    </div>
</section>

<section id="konumumuz" class="location section">
    <div class="container">
        <div class="section-title text-center">
            <span class="subtitle">Bizi Ziyaret Edin</span>
            <h2>Konumumuz</h2>
            <p><?php echo htmlspecialchars($site_settings['address'] ?? 'Şehremini, Millet Cd. 34098 Fatih/İstanbul'); ?>
            </p>
        </div>
        <div class="map-wrapper mt-5">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3010.512686734138!2d28.930335011680577!3d41.01391597123169!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cabbba784279b9%3A0x3be242b5be4b22f9!2zxZ9laHJlbWluaSwgTWlsbGV0IENkLiAzNDA5OCBGYXRpaC_EsHN0YW5idWw!5e0!3m2!1str!2str!4v1707203000000!5m2!1str!2str"
                width="100%" height="500" style="border:0; border-radius: 30px; box-shadow: var(--shadow-lg);"
                allowfullscreen="" loading="lazy">
            </iframe>
            <div class="text-center mt-4">
                <a href="https://www.google.com/maps?daddr=Şehremini,+Millet+Cd.+34098+Fatih/İstanbul" target="_blank"
                    class="btn btn-secondary">
                    <i class="fas fa-directions mr-2"></i> Yol Tarifi Al
                </a>
            </div>
        </div>
    </div>
</section>

<section id="sss" class="faq section bg-soft">
    <div class="container">
        <div class="section-title text-center">
            <span class="subtitle">Merak Edilenler</span>
            <h2>Sıkça Sorulan Sorular</h2>
            <p>Terapi süreci ve işleyişimiz hakkında merak edilenler.</p>
        </div>

        <div class="faq-accordion">
            <?php foreach ($faqs as $i => $faq): ?>
                <div class="faq-item">
                    <button class="faq-btn">
                        <span><?php echo htmlspecialchars($faq['question']); ?></span>
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="faq-panel">
                        <p><?php echo htmlspecialchars($faq['answer']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<?php
// HTML Minification Logic
$html = ob_get_clean();

// Minify HTML
function minify_html($html)
{
    // Remove HTML comments (but keep conditional comments if any)
    $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);

    // Remove whitespace between tags
    $html = preg_replace('/>\s+</', '><', $html);

    // Remove whitespace at start and end
    $html = trim($html);

    return $html;
}

// Output Minified HTML
echo minify_html($html);
?>