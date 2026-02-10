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

<section class="hero-modern">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-text-area">
                <span class="hero-label">Erguvan Psikoloji'ye Hoş Geldiniz</span>
                <h1 class="hero-main-title">Modern & Minimalist <br><span class="highlight">Terapi Deneyimi</span></h1>
                <p class="hero-subtext">Profesyonel psikolojik danışmanlık ile daha mutlu ve dengeli bir yaşam için
                    yanınızdayız. Online ve yüz yüze terapi seçenekleriyle size özel çözümler sunuyoruz.</p>
                <div class="hero-cta-box">
                    <a href="#randevu" class="btn btn-primary btn-lg">Randevu Al</a>
                    <a href="#hizmetler" class="btn btn-outline">Hizmetlerimiz</a>
                </div>
            </div>
            <div class="hero-image-area">
                <div class="image-stack">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=800&q=80"
                        alt="Psikolojik Destek" class="main-hero-img">
                    <div class="floating-card">
                        <i class="icon">✓</i>
                        <span>Uzman Desteği</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="hizmetler" class="services section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Hizmetlerimiz</h2>
            <p class="section-description">Size en uygun terapi yöntemini birlikte belirleyerek, kişiselleştirilmiş
                destek sunuyoruz.</p>
        </div>
        <div class="services-grid">
            <?php if (empty($services)): ?>
                <div class="service-card" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                    <p style="color: #64748b; margin: 0;">Henüz hizmet eklenmemiş.</p>
                </div>
            <?php else: ?>
                <?php foreach ($services as $service):
                    $is_featured = !empty($service['is_featured']);
                    ?>
                    <div class="service-card <?php echo $is_featured ? 'featured' : ''; ?>">
                        <?php if ($is_featured): ?>
                            <div class="service-badge">Popüler</div>
                        <?php endif; ?>
                        <div class="service-icon">
                            <?php if ($service['icon']): ?>
                                <?php echo $service['icon']; ?>
                            <?php else: ?>
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                        <?php if ($service['description']): ?>
                            <p><?php echo htmlspecialchars($service['description']); ?></p>
                        <?php endif; ?>
                        <?php if ($service['features']): ?>
                            <ul class="service-features">
                                <?php
                                $features = explode("\n", $service['features']);
                                foreach ($features as $feature):
                                    $feature = trim($feature);
                                    if (!empty($feature)):
                                        ?>
                                        <li><?php echo htmlspecialchars($feature); ?></li>
                                        <?php
                                    endif;
                                endforeach;
                                ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>



<section id="ekibimiz" class="team section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Uzman Ekibimiz</h2>
            <p class="section-description">Deneyimli ve uzman psikolog kadromuzla yanınızdayız.</p>
        </div>

        <?php
        // Veritabanından aktif ekip üyelerini al (Yukarıda çekildi, buradaki tekrarı kaldırıyoruz)
        // $team_members zaten mevcut
        
        // Mock verileri güncelle (Eğer veritabanı boşsa veya test için)
        if (empty($team_members)) {
            $team_members = [
                [
                    'name' => 'Uzman Psikolog Sedat Parmaksız',
                    'title' => 'Uzman Klinik Psikolog',
                    'image' => 'assets/images/team/sedat-parmaksiz.webp',
                    'education' => "Üsküdar Üniversitesi Psikoloji Bölümü (Lisans)\nÜsküdar Üniversitesi Klinik Psikoloji (Yüksek Lisans)",
                    'certificates' => "Marmara Üniversitesi Pedagojik Formasyon Eğitimi\nOyun Terapisi Eğitimi\nÇocuk Ergen Bilişsel Davranışçı Terapi Eğitimi\nİleri Düzey Bilişsel Terapi Eğitimi",
                    'specialties' => [
                        'Oyun Terapisi',
                        'Çocuk Terapisi',
                        'Ergen Terapisi',
                        'Yetişkin Terapisi',
                        'Aile ve Çift Terapisi',
                        'Anksiyete Bozuklukları',
                        'Depresyon Tedavisi',
                        'Şema Terapi'
                    ]
                ],
                [
                    'name' => 'Uzman Psikolog Sena Ceren Parmaksız',
                    'title' => 'Uzman Klinik Psikolog',
                    'image' => 'assets/images/team/sena.jpg',
                    'education' => "Üsküdar Üniversitesi Psikoloji Bölümü (Lisans)\nÜsküdar Üniversitesi Klinik Psikoloji (Yüksek Lisans)",
                    'certificates' => "Oyun Terapisi Eğitimi\nBilişsel Davranışçı Terapi Eğitimi\nÇocuk ve Ergen Terapisi Eğitimi",
                    'specialties' => [
                        'Oyun Terapisi',
                        'Çocuk Terapisi',
                        'Ergen Terapisi',
                        'Yetişkin Terapisi',
                        'Bilişsel Davranışçı Terapi',
                        'Ebeveyn Danışmanlığı',
                        'Sınav Kaygısı',
                        'Dikkat Eksikliği'
                    ]
                ]
            ];
        }
        ?>

        <div class="team-grid">
            <?php if (empty($team_members)): ?>
                <div class="team-card"
                    style="grid-column: 1 / -1; text-align: center; padding: 40px; justify-content: center;">
                    <p style="color: #64748b; margin: 0;">Henüz ekip üyesi eklenmemiş.</p>
                </div>
            <?php else: ?>
                <?php foreach ($team_members as $member):
                    // Veritabanı ile mock data arasındaki uyumsuzluğu gidermek için varsayılan değerler
                    $badge = $member['badge'] ?? 'Uzman';
                    $approaches_title = $member['approaches_title'] ?? 'Bilimsel Yaklaşım';
                    $approaches_desc = $member['approaches_desc'] ?? 'Kanıta dayalı terapötik yöntemler kullanıyoruz.';
                    $values_title = $member['values_title'] ?? 'Etik Değerler';
                    $values_desc = $member['values_desc'] ?? 'Meslek ettiğine bağlı kalarak hizmet veriyoruz.';
                    $specialties = $member['specialties'] ?? ['Bireysel Terapi', 'Çift Terapisi', 'Online Terapi'];
                    if (is_string($specialties)) {
                        $specialties = array_filter(array_map('trim', explode("\n", $specialties)));
                    }
                    ?>
                    <div class="team-card">
                        <div class="team-image-wrapper">
                            <?php
                            $img_url = webp_url($member['image']);
                            // Eğer görsel yoksa veya boşsa fallback göster
                            if (empty($member['image'])) {
                                $img_url = 'https://ui-avatars.com/api/?name=' . urlencode($member['name']) . '&background=f9a8d4&color=fff&size=500';
                            }
                            ?>
                            <img src="<?php echo $img_url; ?>" alt="<?php echo htmlspecialchars($member['name']); ?>"
                                class="team-image aesthetic-image" loading="lazy" decoding="async" width="400" height="500"
                                onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($member['name']); ?>&background=fce7f3&color=ec4899&size=500'">
                        </div>

                        <div class="team-content">
                            <!-- Header -->
                            <div class="team-header">
                                <h3 class="team-name"><?php echo htmlspecialchars($member['name']); ?></h3>
                                <p class="team-title"><?php echo htmlspecialchars($member['title']); ?></p>
                            </div>

                            <!-- Features Grid -->
                            <div class="team-features-grid">
                                <div class="feature-box">
                                    <div class="feature-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path
                                                d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z">
                                            </path>
                                            <path d="M12 6v6l4 2"></path>
                                        </svg>
                                    </div>
                                    <h4 class="feature-title"><?php echo htmlspecialchars($approaches_title); ?></h4>
                                    <p class="feature-description"><?php echo htmlspecialchars($approaches_desc); ?></p>
                                </div>
                                <div class="feature-box">
                                    <div class="feature-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="feature-title"><?php echo htmlspecialchars($values_title); ?></h4>
                                    <p class="feature-description"><?php echo htmlspecialchars($values_desc); ?></p>
                                </div>
                            </div>

                            <!-- Specialties -->
                            <div class="specialties-section">
                                <h4 class="section-subtitle">Uzmanlık Alanları</h4>
                                <ul class="specialty-list">
                                    <?php foreach ($specialties as $specialty): ?>
                                        <li class="specialty-item">
                                            <svg class="check-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                            </svg>
                                            <?php echo htmlspecialchars($specialty); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <!-- Education & Certificates -->
                            <div class="team-credentials">
                                <h4 class="section-subtitle">Eğitim ve Sertifikalar</h4>
                                <div class="credentials-list-wrapper">
                                    <?php
                                    $all_credentials = [];
                                    if (!empty($member['education'])) {
                                        $edus = explode("\n", $member['education']);
                                        foreach ($edus as $edu) {
                                            if (trim($edu))
                                                $all_credentials[] = ['type' => 'edu', 'text' => trim($edu)];
                                        }
                                    }
                                    if (!empty($member['certificates'])) {
                                        $certs = explode("\n", $member['certificates']);
                                        $count = 0;
                                        foreach ($certs as $cert) {
                                            if (trim($cert) && $count < 3) { // Biraz daha fazla, 3 sertifika göster
                                                $all_credentials[] = ['type' => 'cert', 'text' => trim($cert)];
                                                $count++;
                                            }
                                        }
                                    }
                                    ?>

                                    <?php foreach ($all_credentials as $cred): ?>
                                        <div class="credential-item">
                                            <div class="credential-icon-leaf">
                                                <?php if ($cred['type'] == 'edu'): ?>
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                                    </svg>
                                                <?php else: ?>
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path
                                                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z">
                                                        </path>
                                                    </svg>
                                                <?php endif; ?>
                                            </div>
                                            <div class="credential-content">
                                                <span
                                                    class="credential-school"><?php echo htmlspecialchars($cred['text']); ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section id="sertifikalar" class="certificates section bg-light">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Sertifikalarımız</h2>
            <p class="section-description">Profesyonel eğitimlerimiz ve aldığımız sertifikalar.</p>
        </div>
        <?php
        // Veritabanından aktif sertifikaları al (Yukarıda çekildi)
        // $certificates zaten mevcut
        
        // Görselleri rastgele sırala
        if (!empty($certificates)) {
            shuffle($certificates);
        }

        // İlk 3 görseli al
        $displayed_certificates = array_slice($certificates, 0, 3);
        $hidden_certificates = array_slice($certificates, 3);
        $total_count = count($certificates);
        ?>
        <?php if (!empty($certificates)): ?>
            <div class="certificates-grid" id="certificatesGrid">
                <?php foreach ($displayed_certificates as $cert): ?>
                    <div class="certificate-item">
                        <a href="<?php echo url($cert['image']); ?>" target="_blank" class="certificate-link"
                            data-lightbox="certificates"
                            aria-label="<?php echo htmlspecialchars($cert['title']); ?> sertifikasını görüntüle">
                            <img src="<?php echo url($cert['image']); ?>" alt="<?php echo htmlspecialchars($cert['title']); ?>"
                                class="certificate-image aesthetic-image" width="300" height="200" loading="lazy"
                                decoding="async">
                            <div class="certificate-overlay">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"></path>
                                </svg>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
                <?php if (!empty($hidden_certificates)): ?>
                    <?php foreach ($hidden_certificates as $cert): ?>
                        <div class="certificate-item hidden-certificate" style="display: none;">
                            <a href="<?php echo url($cert['image']); ?>" target="_blank" class="certificate-link"
                                data-lightbox="certificates"
                                aria-label="<?php echo htmlspecialchars($cert['title']); ?> sertifikasını görüntüle">
                                <img data-src="<?php echo url($cert['image']); ?>"
                                    src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect fill='%23f1f5f9' width='400' height='300'/%3E%3C/svg%3E"
                                    alt="<?php echo htmlspecialchars($cert['title']); ?>" class="certificate-image aesthetic-image"
                                    loading="lazy" decoding="async" width="400" height="300"
                                    sizes="(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw">
                                <div class="certificate-overlay">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"></path>
                                    </svg>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php if (!empty($hidden_certificates)): ?>
                <div class="text-center" style="margin-top: 3rem;">
                    <button type="button" class="btn btn-primary" id="showAllCertificates">
                        Tüm Sertifikaları Gör (<?php echo $total_count; ?>)
                    </button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state" style="text-align: center; padding: 3rem 0; color: var(--text-light);">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    style="margin: 0 auto 1rem; opacity: 0.5;">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="9" y1="3" x2="9" y2="21"></line>
                </svg>
                <p>Sertifika görselleri henüz eklenmemiş.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section id="ofisimiz" class="office section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Ofisimiz</h2>
            <p class="section-description">Sıcak ve konforlu ortamımızda sizleri ağırlamaktan mutluluk duyarız.</p>
        </div>
        <?php
        // Veritabanından aktif ofis görsellerini al
        // Kullanıcı isteği üzerine: 'ofis' klasöründeki özel resimler kullanılacak (Database bypass)
        $office_images = [
            [
                'image' => 'ofis/ofis-1.webp',
                'title' => 'Ofisimizden Görünüm',
                'description' => 'Huzurlu ve profesyonel bir ortam.'
            ],
            [
                'image' => 'ofis/ofis-2.webp',
                'title' => 'Ofisimizden Görünüm',
                'description' => ''
            ],
            [
                'image' => 'ofis/ofis.webp',
                'title' => 'Ofisimizden Görünüm',
                'description' => ''
            ],
            [
                'image' => 'ofis/beeetül.webp',
                'title' => 'Ofisimizden Görünüm',
                'description' => ''
            ]
        ];
        ?>
        <?php if (!empty($office_images)): ?>
            <div class="office-grid">
                <?php foreach ($office_images as $img): ?>
                    <div class="office-card">
                        <img src="<?php echo webp_url($img['image']); ?>"
                            alt="<?php echo htmlspecialchars($img['title'] ?? 'Ofis Görseli'); ?>"
                            class="office-card-image aesthetic-image" width="600" height="400" loading="lazy" decoding="async">

                        <?php if (!empty($img['title'])): ?>
                            <div class="office-card-overlay">
                                <h3 class="office-card-title"><?php echo htmlspecialchars($img['title']); ?></h3>
                                <?php if (!empty($img['description'])): ?>
                                    <p class="office-card-desc"><?php echo htmlspecialchars($img['description']); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- .office-dots element removed as it is part of the slider -->

        <?php else: ?>
            <div class="empty-state" style="text-align: center; padding: 3rem 0; color: var(--text-light);">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    style="margin: 0 auto 1rem; opacity: 0.5;">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
                <p>Ofis görselleri henüz eklenmemiş.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="blog-preview section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Son Blog Yazıları</h2>
            <p class="section-description">Psikoloji ve kişisel gelişim hakkında güncel yazılarımı okuyun.</p>
        </div>
        <?php
        try {
            $recent_posts = $db->query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 3")->fetchAll();
        } catch (Exception $e) {
            $recent_posts = [];
        }
        ?>
        <div class="blog-grid">
            <?php foreach ($recent_posts as $post): ?>
                <article class="blog-card">
                    <div class="blog-card-image">
                        <img src="<?php echo webp_url($post['image']); ?>"
                            alt="<?php echo htmlspecialchars($post['title']); ?>" width="400" height="250" loading="lazy"
                            decoding="async" class="aesthetic-image">
                        <span class="blog-category"><?php echo $post['category']; ?></span>
                    </div>
                    <div class="blog-card-content">
                        <div class="blog-meta">
                            <span class="blog-date">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2"
                                    style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <?php echo date('d.m.Y', strtotime($post['created_at'])); ?>
                            </span>
                            <span class="blog-reading-time">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2"
                                    style="display: inline-block; vertical-align: middle; margin-right: 4px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <?php echo $post['reading_time']; ?>
                            </span>
                        </div>
                        <h3 class="blog-card-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p class="blog-card-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        <a href="<?php echo url('blog/' . $post['slug']); ?>" class="blog-read-more"
                            aria-label="<?php echo htmlspecialchars($post['title']); ?> yazısını oku">Devamını Oku →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="text-center" style="margin-top: 3rem;">
            <a href="<?php echo page_url('blog.php'); ?>" class="btn btn-primary"
                aria-label="Tüm blog yazılarını görüntüle">Tüm Yazıları Gör</a>
        </div>
    </div>
</section>

<section class="testimonials section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Danışanlarımız Ne Diyor?</h2>
            <p class="section-description">Google yorumlarından gerçek danışan deneyimleri ve geri bildirimler.</p>
        </div>

        <div class="testimonials-slider-wrapper">
            <button class="testimonials-nav prev" aria-label="Önceki yorumlar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>

            <div class="testimonials-slider">
                <div class="testimonials-track">
                    <?php if (empty($testimonials)): ?>
                        <p style="text-align: center; width: 100%;">Henüz yorum eklenmemiş.</p>
                    <?php else: ?>
                        <?php foreach ($testimonials as $t): ?>
                            <div class="testimonial-slide">
                                <div class="testimonial-card">
                                    <div class="testimonial-header">
                                        <div class="testimonial-avatar">
                                            <?php echo htmlspecialchars($t['avatar_char'] ?: substr($t['name'], 0, 1)); ?>
                                        </div>
                                        <div class="testimonial-info">
                                            <div class="testimonial-name"><?php echo htmlspecialchars($t['name']); ?></div>
                                            <div class="testimonial-date"><?php echo htmlspecialchars($t['date_info']); ?></div>
                                        </div>
                                    </div>
                                    <div class="testimonial-rating">
                                        <?php for ($i = 0; $i < ($t['rating'] ?: 5); $i++): ?>
                                            <span class="star">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="testimonial-text"><?php echo htmlspecialchars($t['comment']); ?></p>
                                    <div class="testimonial-badge">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <?php echo htmlspecialchars($t['source'] ?: 'Google Yorumu'); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <button class="testimonials-nav next" aria-label="Sonraki yorumlar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>

        <div class="testimonials-dots"></div>

        <div class="google-badge">
            <div class="google-badge-content">
                <svg class="google-logo" viewBox="0 0 24 24" fill="none">
                    <path
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                        fill="#4285F4" />
                    <path
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                        fill="#34A853" />
                    <path
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                        fill="#FBBC05" />
                    <path
                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                        fill="#EA4335" />
                </svg>
                <div class="google-rating">
                    <div class="google-rating-stars">
                        <span class="google-rating-number">5.0</span>
                        <div class="testimonial-rating" style="margin-bottom: 0;">
                            <span class="star">★</span>
                            <span class="star">★</span>
                            <span class="star">★</span>
                            <span class="star">★</span>
                            <span class="star">★</span>
                        </div>
                    </div>
                    <div class="google-rating-text">Google Yorumlarına Göre</div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const track = document.querySelector('.testimonials-track');
        const slides = document.querySelectorAll('.testimonial-slide');
        const prevBtn = document.querySelector('.testimonials-nav.prev');
        const nextBtn = document.querySelector('.testimonials-nav.next');
        const dotsContainer = document.querySelector('.testimonials-dots');

        // Eğer slider yoksa çık
        if (!track || !slides.length) return;

        let currentIndex = 0;
        let slidesPerView = 4;
        let autoplayInterval;

        // Responsive slides per view
        function updateSlidesPerView() {
            if (window.innerWidth <= 768) {
                slidesPerView = 1;
            } else if (window.innerWidth <= 1024) {
                slidesPerView = 3;
            } else {
                slidesPerView = 4;
            }
        }

        // Create dots
        function createDots() {
            dotsContainer.innerHTML = '';
            const totalDots = Math.ceil(slides.length / slidesPerView);
            for (let i = 0; i < totalDots; i++) {
                const dot = document.createElement('button');
                dot.classList.add('testimonial-dot');
                dot.setAttribute('aria-label', `Yorum grubu ${i + 1}`);
                if (i === 0) dot.classList.add('active');
                dot.addEventListener('click', () => goToSlide(i * slidesPerView));
                dotsContainer.appendChild(dot);
            }
        }

        // Update dots
        function updateDots() {
            const dots = document.querySelectorAll('.testimonial-dot');
            const activeDotIndex = Math.floor(currentIndex / slidesPerView);
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === activeDotIndex);
            });
        }

        // Go to slide
        function goToSlide(index) {
            const maxIndex = Math.max(0, slides.length - slidesPerView);
            currentIndex = Math.max(0, Math.min(index, maxIndex));
            const offset = -(currentIndex * (100 / slidesPerView));
            track.style.transform = `translateX(${offset}%)`;
            updateDots();
        }

        // Next slide
        function nextSlide() {
            const maxIndex = Math.max(0, slides.length - slidesPerView);
            if (currentIndex < maxIndex) {
                goToSlide(currentIndex + 1);
            } else {
                goToSlide(0);
            }
        }

        // Previous slide
        function prevSlide() {
            const maxIndex = Math.max(0, slides.length - slidesPerView);
            if (currentIndex > 0) {
                goToSlide(currentIndex - 1);
            } else {
                goToSlide(maxIndex);
            }
        }

        // Auto play
        function startAutoplay() {
            autoplayInterval = setInterval(nextSlide, 5000);
        }

        function stopAutoplay() {
            clearInterval(autoplayInterval);
        }

        // Event listeners
        nextBtn.addEventListener('click', () => {
            nextSlide();
            stopAutoplay();
            startAutoplay();
        });

        prevBtn.addEventListener('click', () => {
            prevSlide();
            stopAutoplay();
            startAutoplay();
        });

        // Pause on hover
        track.addEventListener('mouseenter', stopAutoplay);
        track.addEventListener('mouseleave', startAutoplay);

        // Initialize
        updateSlidesPerView();
        createDots();
        startAutoplay();

        // Handle resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                updateSlidesPerView();
                createDots();
                goToSlide(0);
            }, 250);
        });
    });

    // Ofis Slider
    document.addEventListener('DOMContentLoaded', function () {
        const officeTrack = document.querySelector('.office-track');
        if (!officeTrack) return;

        const officeSlides = document.querySelectorAll('.office-slide');
        const prevBtn = document.querySelector('.office-nav.prev');
        const nextBtn = document.querySelector('.office-nav.next');
        const dotsContainer = document.querySelector('.office-dots');

        if (!officeSlides.length) return;

        let currentIndex = 0;
        let autoplayInterval;

        // Create dots
        function createDots() {
            if (!dotsContainer) return;
            dotsContainer.innerHTML = '';
            for (let i = 0; i < officeSlides.length; i++) {
                const dot = document.createElement('button');
                dot.classList.add('office-dot');
                dot.setAttribute('aria-label', `Ofis görseli ${i + 1}`);
                if (i === 0) dot.classList.add('active');
                dot.addEventListener('click', () => goToSlide(i));
                dotsContainer.appendChild(dot);
            }
        }

        // Update dots
        function updateDots() {
            if (!dotsContainer) return;
            const dots = document.querySelectorAll('.office-dot');
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentIndex);
            });
        }

        // Go to slide
        function goToSlide(index) {
            currentIndex = Math.max(0, Math.min(index, officeSlides.length - 1));
            const offset = -(currentIndex * 100);
            officeTrack.style.transform = `translateX(${offset}%)`;
            updateDots();
        }

        // Next slide
        function nextSlide() {
            if (currentIndex < officeSlides.length - 1) {
                goToSlide(currentIndex + 1);
            } else {
                goToSlide(0);
            }
        }

        // Previous slide
        function prevSlide() {
            if (currentIndex > 0) {
                goToSlide(currentIndex - 1);
            } else {
                goToSlide(officeSlides.length - 1);
            }
        }

        // Auto play
        function startAutoplay() {
            autoplayInterval = setInterval(nextSlide, 5000);
        }

        function stopAutoplay() {
            clearInterval(autoplayInterval);
        }

        // Event listeners
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                nextSlide();
                stopAutoplay();
                startAutoplay();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                prevSlide();
                stopAutoplay();
                startAutoplay();
            });
        }

        // Pause on hover
        if (officeTrack) {
            officeTrack.addEventListener('mouseenter', stopAutoplay);
            officeTrack.addEventListener('mouseleave', startAutoplay);
        }

        // Touch events
        let touchStartX = 0;
        let touchEndX = 0;

        if (officeTrack) {
            officeTrack.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
                stopAutoplay();
            });

            officeTrack.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
                startAutoplay();
            });
        }

        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        }

        // Initialize
        createDots();
        startAutoplay();
    });
</script>

<section id="iletisim" class="contact section bg-light">


    <div class="container">
        <div class="section-header">
            <h2 class="section-title">İletişime Geçin</h2>
            <p class="section-description">Size nasıl yardımcı olabileceğimizi konuşmak için bizimle iletişime geçin.
            </p>
        </div>
        <div class="contact-content">
            <div class="contact-info">
                <div class="contact-card">
                    <div class="contact-header">
                        <h3>İletişim Bilgilerimiz</h3>
                        <p>Bize her zaman ulaşabilirsiniz.</p>
                    </div>
                    <div class="contact-details">
                        <div class="contact-item">
                            <div class="contact-icon">📍</div>
                            <div>
                                <strong>Adres</strong>
                                <p><?php echo htmlspecialchars($site_settings['address'] ?? 'Şehremini, Millet Cd. Aydın apt No:131 Daire 4'); ?><br>
                                    <?php echo htmlspecialchars($site_settings['address_city'] ?? '34098 Fatih/İstanbul'); ?>
                                </p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">📞</div>
                            <div>
                                <strong>Telefon</strong>
                                <p><a
                                        href="tel:<?php echo str_replace(' ', '', $site_settings['phone'] ?? '05511765285'); ?>"><?php echo htmlspecialchars($site_settings['phone'] ?? '0551 176 52 85'); ?></a>
                                </p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">✉️</div>
                            <div>
                                <strong>E-posta</strong>
                                <p><a
                                        href="mailto:<?php echo htmlspecialchars($site_settings['email'] ?? 'info@erguvanpsikoloji.com'); ?>"><?php echo htmlspecialchars($site_settings['email'] ?? 'info@erguvanpsikoloji.com'); ?></a>
                                </p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">⏰</div>
                            <div>
                                <strong>Çalışma Saatleri</strong>
                                <p>Hafta içi:
                                    <?php echo htmlspecialchars($site_settings['working_hours_weekdays'] ?? '09:00 - 22:00'); ?><br>
                                    Hafta sonu:
                                    <?php echo htmlspecialchars($site_settings['working_hours_weekends'] ?? '09:00 - 21:00'); ?>
                                </p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon"
                                style="display: flex; align-items: flex-start; justify-content: center;">
                                <span class="icon-circle"
                                    style="width: 32px; height: 32px; background: #ec4899; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-top: 2px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                    </svg>
                                </span>
                            </div>
                            <div>
                                <strong>Instagram</strong>
                                <p><a href="<?php echo htmlspecialchars($site_settings['instagram_url'] ?? 'https://www.instagram.com/erguvanpsikoloji'); ?>"
                                        target="_blank" rel="noopener">@erguvanpsikoloji</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="randevu" class="contact-form-wrapper">
                <h3>Randevu Talebi</h3>
                <div id="form-message" style="display: none; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                </div>
                <form class="contact-form" id="appointmentForm" action="<?php echo page_url('contact-handler.php'); ?>"
                    method="post">
                    <div class="form-group">
                        <label for="name">Adınız Soyadınız *</label>
                        <input type="text" name="name" id="name" placeholder="Adınız Soyadınız" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-posta Adresiniz *</label>
                        <input type="email" name="email" id="email" placeholder="E-posta Adresiniz" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Telefon Numaranız *</label>
                        <input type="tel" name="phone" id="phone" placeholder="Telefon Numaranız" required>
                    </div>
                    <div class="form-group">
                        <label for="service">Hizmet *</label>
                        <select name="service" id="service" required aria-label="Randevu için hizmet seçiniz">
                            <option value="">Hizmet Seçiniz</option>
                            <option value="bireysel">Bireysel Terapi</option>
                            <option value="online">Online Terapi</option>
                            <option value="cift">Çift Terapisi</option>
                            <option value="aile">Aile Danışmanlığı</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="message">Mesajınız</label>
                        <textarea name="message" id="message" rows="4" placeholder="Mesajınız (Opsiyonel)"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" id="submitBtn"
                        aria-label="Randevu talebini gönder">
                        <span class="btn-text">Randevu Talebi Gönder</span>
                        <span class="btn-loading" style="display: none;">Gönderiliyor...</span>
                    </button>
                    <p class="form-note">* En kısa sürede size dönüş yapacağız.</p>
                </form>
            </div>
        </div>
        <div class="contact-map"
            style="margin-top: 3rem; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-md); height: 500px;">
            <div id="map-placeholder"
                style="width:100%; height:100%; background:#f8fafc; display:flex; flex-direction:column; align-items:center; justify-content:center; cursor:pointer; min-height: 400px;"
                onclick="loadMap(this)">
                <div
                    style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); text-align: center;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2"
                        style="margin-bottom: 1rem;">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <h3 style="margin-bottom: 1rem; color: #1e293b;">Konumu Göster</h3>
                    <button class="btn btn-primary" type="button">Haritayı Yükle</button>
                    <p style="margin-top: 0.5rem; color: #64748b; font-size: 0.875rem;">Google Maps</p>
                </div>
            </div>
            <script>
                function loadMap(element) {
                    var iframe = document.createElement('iframe');
                    iframe.setAttribute('src', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3010.512551001328!2d28.928251275858734!3d41.014041121349706!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cabbbebb427bb9%3A0x3fec28c0f52032ea!2sErguvan%20Psikoloji%20ve%20Dan%C4%B1%C5%9Fmanl%C4%B1k%20Merkezi-Uzman%20Klinik%20Psikolog!5e0!3m2!1str!2str!4v1763742242753!5m2!1str!2str&key=<?php echo defined('GOOGLE_MAPS_API_KEY') ? GOOGLE_MAPS_API_KEY : ''; ?>');
                    iframe.style.width = "100%";
                    iframe.style.height = "100%";
                    iframe.style.border = "0";
                    iframe.setAttribute('allowfullscreen', '');
                    iframe.setAttribute('loading', 'lazy');
                    iframe.setAttribute('referrerpolicy', 'no-referrer-when-downgrade');
                    element.innerHTML = '';
                    element.appendChild(iframe);
                    element.removeAttribute('onclick');
                    element.style.cursor = 'default';
                }
            </script>
        </div>
    </div>
</section>

<section id="sss" class="faq-section section bg-white">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Sıkça Sorulan Sorular</h2>
            <p class="section-description">Terapi süreci ve işleyişimiz hakkında merak edilenler.</p>
        </div>

        <div class="faq-grid">
            <?php if (empty($faqs)): ?>
                <p>Henüz soru eklenmemiş.</p>
            <?php else: ?>
                <?php foreach ($faqs as $i => $faq): ?>
                    <div class="faq-item" style="animation-delay: <?php echo ($i * 0.1); ?>s">
                        <button class="faq-question">
                            <span><?php echo htmlspecialchars($faq['question']); ?></span>
                            <span class="faq-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <p><?php echo htmlspecialchars($faq['answer']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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