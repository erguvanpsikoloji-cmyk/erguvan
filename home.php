<?php

ob_start();

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);


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


function sanitizePath($path)
{
    return str_replace('\\', '/', $path);
}

require_once __DIR__ . '/database/db.php';


try {
    $db = getDB();


    $sliders = $db->query("SELECT * FROM sliders WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
    foreach ($sliders as &$item) {
        if (isset($item['image']))
            $item['image'] = sanitizePath($item['image']);
    }


    $services = $db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC, created_at ASC")->fetchAll();
    foreach ($services as &$item) {
        if (isset($item['image']))
            $item['image'] = sanitizePath($item['image']);
    }


    $team_members = $db->query("SELECT * FROM team_members WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
    foreach ($team_members as &$item) {
        if (isset($item['image']))
            $item['image'] = sanitizePath($item['image']);
    }


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


    $office_images_query = $db->query("SELECT * FROM office_images WHERE is_active = 1 ORDER BY display_order ASC");
    if ($office_images_query) {
        $office_images = $office_images_query->fetchAll();
        foreach ($office_images as &$item) {
            if (isset($item['image']))
                $item['image'] = sanitizePath($item['image']);
        }
    }


    $about_query = $db->query("SELECT * FROM about_us LIMIT 1");
    $about_data = $about_query->fetch();


    $settings_query = $db->query("SELECT setting_key, setting_value FROM site_settings");
    $site_settings = [];
    while ($row = $settings_query->fetch()) {
        $site_settings[$row['setting_key']] = $row['setting_value'];
    }


    $testimonials = $db->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC")->fetchAll();


    $faqs = $db->query("SELECT * FROM faqs WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();

} catch (Exception $e) {
    error_log('Database error in index.php: ' . $e->getMessage());
    $sliders = [];
    $services = [];
    $team_members = [];
    $certificates = [];
}


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
            'description' => 'Kendinizi anlama ve hayat kalitenizi artırma yolculuğunda yanınızdayız.',
            'icon' => '<i class="fas fa-user-heart"></i>',
            'features' => "Anksiyete ve Depresyon\nStres Yönetimi\nÖzgüven Sorunları",
            'is_featured' => 1
        ],
        [
            'title' => 'Aile ve Çift Terapisi',
            'description' => 'İlişkilerinizde daha sağlıklı iletişim ve güçlü bağlar kurmanız için yanınızdayız.',
            'icon' => '<i class="fas fa-hand-holding-heart"></i>',
            'features' => "İletişim Sorunları\nÇatışma Çözme\nEvlilik Öncesi Danışmanlık",
            'is_featured' => 1
        ],
        [
            'title' => 'Oyun Terapisi',
            'description' => 'Çocukların kendilerini ifade etme dili olan oyun ile duygusal iyileşme sağlıyoruz.',
            'icon' => '<i class="fas fa-shapes"></i>',
            'features' => "Duygusal Düzenleme\nTravma ve Yas\nDavranış Sorunları",
            'is_featured' => 1
        ],
        [
            'title' => 'Yetişkin Terapisi',
            'description' => 'Yetişkinlik döneminin getirdiği zorluklarla başa çıkmak için profesyonel destek.',
            'icon' => '<i class="fas fa-user-tie"></i>',
            'features' => "Yaşam Krizi\nİlişki Sorunları\nKariyer Danışmanlığı",
            'is_featured' => 1
        ],
        [
            'title' => 'Çocuk Terapisi',
            'description' => 'Çocukların gelişimsel süreçlerinde karşılaştıkları güçlükleri birlikte aşıyoruz.',
            'icon' => '<i class="fas fa-child-reaching"></i>',
            'features' => "Gelişim Takibi\nKorku ve Kaygılar\nSosyal Beceriler",
            'is_featured' => 1
        ],
        [
            'title' => 'Ebeveyn Danışmanlığı',
            'description' => 'Ebeveynlik yolculuğunda karşılaşılan sorulara bilimsel cevaplar ve rehberlik.',
            'icon' => '<i class="fas fa-hands-holding-child"></i>',
            'features' => "Pozitif Ebeveynlik\nSınır Koyma\nİletişim Kurma",
            'is_featured' => 1
        ],
        [
            'title' => 'Bilişsel Davranışçı Terapi',
            'description' => 'Düşünce ve davranış kalıplarını değiştirerek kalıcı iyileşmeyi hedefleyen yöntem.',
            'icon' => '<i class="fas fa-brain"></i>',
            'features' => "Düşünce Yapılanması\nMaruz Bırakma\nProblem Çözme",
            'is_featured' => 1
        ],
        [
            'title' => 'Masal Terapisi',
            'description' => 'Masalların iyileştirici gücü ile çocukların iç dünyasına sembolik yolculuklar.',
            'icon' => '<i class="fas fa-wand-magic-sparkles"></i>',
            'features' => "Sembolik Anlatım\nDuygusal Aktarım\nYaratıcı Çözümler",
            'is_featured' => 1
        ],
        [
            'title' => 'Şema Terapi',
            'description' => 'Kökü çocukluğa dayanan olumsuz yaşam kalıplarını (şemaları) fark etme ve değiştirme.',
            'icon' => '<i class="fas fa-seedling"></i>',
            'features' => "Şema Farkındalığı\nMod Çalışması\nYaşam Kalıpları",
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



$slider = !empty($sliders) ? $sliders[0] : null;


if (!empty($sliders)) {

    $preload_image = webp_url($sliders[0]['image']);
    $preload_image_mobile = webp_url($sliders[0]['image'], 'mobile');
} else {

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

<style>
/* Service Card Soft & Elegant Styles for home.php */
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2.5rem;
    margin-top: 3rem;
    padding: 1rem;
}

.service-card {
    background: #ffffff;
    padding: 4rem 2rem;
    border-radius: 40px;
    border: none;
    transition: all 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
    text-align: center;
    position: relative;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.02);
    display: flex;
    flex-direction: column;
    align-items: center;
}

.service-icon-wrapper {
    position: relative;
    width: 100px;
    height: 100px;
    margin: 0 auto 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.service-icon-blob {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #fdf2f8; /* Soft pink/creme */
    border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%;
    animation: blob-morph 8s linear infinite;
    transition: all 0.5s ease;
    z-index: 1;
}

@keyframes blob-morph {
    0%, 100% { border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%; }
    25% { border-radius: 70% 30% 46% 54% / 30% 29% 71% 70%; }
    50% { border-radius: 50% 50% 34% 66% / 56% 68% 32% 44%; }
    75% { border-radius: 46% 54% 50% 50% / 35% 61% 39% 65%; }
}

.service-card .service-icon {
    position: relative !important;
    z-index: 2 !important;
    background: transparent !important;
    box-shadow: none !important;
    margin-bottom: 0 !important;
    width: auto !important;
    height: auto !important;
    color: #2c3e50 !important;
    font-size: 2.2rem !important;
}

.service-card h3 {
    font-size: 1.6rem;
    color: #2c3e50;
    margin-bottom: 1.2rem;
    font-family: 'Playfair Display', serif;
    font-weight: 700;
}

.service-card p {
    font-size: 1rem;
    color: #64748b;
    margin-bottom: 2rem;
    line-height: 1.8;
}

.service-features {
    list-style: none;
    padding: 0;
    margin: 0 0 2.5rem 0;
    text-align: left;
    width: 100%;
}

.service-features li {
    padding: 0.5rem 0;
    font-size: 0.95rem;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 10px;
}

.service-features li i {
    color: #915f78;
    font-size: 0.8rem;
}

.btn-card {
    margin-top: auto;
    background: rgba(145, 95, 120, 0.05);
    color: #915f78;
    padding: 0.8rem 2.2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.4s ease;
    border: 1px solid rgba(145, 95, 120, 0.1);
}

.service-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 30px 60px rgba(145, 95, 120, 0.1);
    background: #fffcfd;
}

.service-card:hover .service-icon-blob {
    background: #2c3e50;
    transform: scale(1.1);
}

.service-card:hover .service-icon {
    color: white !important;
}

.service-card:hover .btn-card {
    background: #915f78;
    color: white;
}
</style>

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
                                href="tel:<?php echo str_replace(' ', '', $site_settings['phone'] ?? '05511765285'); ?>"><?php echo htmlspecialchars($site_settings['phone'] ?? '0551 176 52 85'); ?></a>
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

$html = ob_get_clean();


function minify_html($html)
{

    $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);


    $html = preg_replace('/>\s+</', '><', $html);


    $html = trim($html);

    return $html;
}


echo minify_html($html);
?>