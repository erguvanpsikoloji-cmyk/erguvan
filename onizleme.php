<?php
require_once 'config.php';

// Mock data for preview
$team_members = [
    [
        'name' => 'Uzman Psikolog Sedat Parmaksız',
        'title' => 'Uzman Klinik Psikolog',
        'image' => 'assets/images/team/sedat.jpg'
    ],
    [
        'name' => 'Uzman Psikolog Sena Ceren Parmaksız',
        'title' => 'Uzman Klinik Psikolog',
        'image' => 'assets/images/team/ceren.jpg'
    ]
];
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erguvan Psikoloji | Önizleme</title>
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap"
        rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="assets/css/style-preview.css">
</head>

<body>

    <header class="navbar">
        <div class="container">
            <div class="logo-wrapper">
                <a href="#" class="logo-link"><img src="assets/images/logo.png" alt="Logo"></a>
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
        <!-- Hero Section -->
        <section id="home" class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Ruhsal Dengeni <br><span>Birlikte Keşfedelim.</span></h1>
                    <p>Erguvan Psikoloji'nin modern yaklaşımı ve güven veren uzman kadrosuyla, içsel yolculuğunuzda size
                        rehberlik ediyoruz.</p>
                    <div class="hero-btns">
                        <a href="#contact" class="btn btn-primary">Randevu Al</a>
                        <a href="#services" class="btn btn-secondary">Hizmetlerimiz</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="assets/images/hero-psikolojik-destek.jpg" alt="Hero Image">
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="services">
            <div class="container">
                <div class="section-title">
                    <h2>Hizmetlerimiz</h2>
                    <p>Size en uygun terapi yöntemini belirleyelim.</p>
                </div>
                <div class="services-grid">
                    <div class="service-card">
                        <div class="card-icon"><i class="fas fa-user"></i></div>
                        <h3>Yetişkin Terapisi</h3>
                        <p>Yetişkinlik döneminde karşılaşılan duygusal zorluklar ve yaşam krizleri üzerine profesyonel
                            destek.</p>
                        <a href="#contact" class="card-cta">Detaylı Bilgi <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="service-card">
                        <div class="card-icon"><i class="fas fa-puzzle-piece"></i></div>
                        <h3>Oyun Terapisi</h3>
                        <p>Çocukların kendilerini oyun yoluyla ifade etmelerine ve iyileşmelerine yardımcı olan özel
                            yöntem.</p>
                        <a href="#contact" class="card-cta">Detaylı Bilgi <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="service-card">
                        <div class="card-icon"><i class="fas fa-clipboard-check"></i></div>
                        <h3>Psikolojik Testler</h3>
                        <p>Bilişsel, duygusal ve kişilik gelişimini değerlendirmeye yönelik bilimsel temelli ölçme ve
                            değerlendirmeler.</p>
                        <a href="#contact" class="card-cta">Detaylı Bilgi <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="service-card">
                        <div class="card-icon"><i class="fas fa-users"></i></div>
                        <h3>Aile Çift Terapisi</h3>
                        <p>İlişkilerdeki iletişimi güçlendirmek ve çatışmaları sağlıklı şekilde çözümlemek için
                            profesyonel rehberlik.</p>
                        <a href="#contact" class="card-cta">Detaylı Bilgi <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="service-card">
                        <div class="card-icon"><i class="fas fa-hands-helping"></i></div>
                        <h3>Ebeveyn Danışmanlığı</h3>
                        <p>Çocuk yetiştirme sürecindeki zorluklar ve sağlıklı ebeveyn-çocuk ilişkisi kurma üzerine
                            danışmanlık.</p>
                        <a href="#contact" class="card-cta">Detaylı Bilgi <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="service-card">
                        <div class="card-icon"><i class="fas fa-child"></i></div>
                        <h3>Çocuk Ergen Terapisi</h3>
                        <p>Gelişim dönemindeki çocuk ve gençlerin duygusal, davranışsal ve akademik süreçlerine yönelik
                            destek.</p>
                        <a href="#contact" class="card-cta">Detaylı Bilgi <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Expert Profiles -->
        <section id="about" class="about">
            <div class="container">
                <div class="section-title">
                    <h2>Uzman Ekibimiz</h2>
                </div>
                <div class="expert-profile-container">
                    <?php foreach ($team_members as $m): ?>
                        <div class="expert-card" style="margin-bottom: 50px;">
                            <div class="expert-image-side">
                                <img src="<?php echo $m['image']; ?>" alt="<?php echo $m['name']; ?>">
                            </div>
                            <div class="expert-content-side">
                                <div class="expert-header">
                                    <h1><?php echo htmlspecialchars($m['name']); ?></h1>
                                    <div class="title"><?php echo htmlspecialchars($m['title']); ?></div>
                                </div>
                                <div class="expert-approach"
                                    style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                                    <div>
                                        <h3 class="expert-section-title">Bilimsel Yaklaşım</h3>
                                        <p style="font-size: 0.9rem; color: var(--text-light);">Kanıta dayalı terapötik
                                            yöntemler kullanıyoruz.</p>
                                    </div>
                                    <div>
                                        <h3 class="expert-section-title">Etik Değerler</h3>
                                        <p style="font-size: 0.9rem; color: var(--text-light);">Meslek etiğine bağlı kalarak
                                            hizmet veriyoruz.</p>
                                    </div>
                                </div>
                                <div class="expert-expertise">
                                    <h3 class="expert-section-title">Uzmanlık Alanları</h3>
                                    <ul class="expertise-list">
                                        <li><i class="fas fa-check-circle"></i> Oyun Terapisi</li>
                                        <li><i class="fas fa-check-circle"></i> Çocuk Terapisi</li>
                                        <li><i class="fas fa-check-circle"></i> Ergen Terapisi</li>
                                        <li><i class="fas fa-check-circle"></i> Yetişkin Terapisi</li>
                                        <li><i class="fas fa-check-circle"></i> Bilişsel Davranışçı Terapi</li>
                                        <li><i class="fas fa-check-circle"></i> Ebeveyn Danışmanlığı</li>
                                        <li><i class="fas fa-check-circle"></i> Sınav Kaygısı</li>
                                        <li><i class="fas fa-check-circle"></i> Dikkat Eksikliği</li>
                                    </ul>
                                </div>
                                <div class="expert-education">
                                    <h3 class="expert-section-title">Eğitim ve Sertifikalar</h3>
                                    <ul class="education-list">
                                        <li><i class="fas fa-graduation-cap"></i> Üsküdar Üni. Psikoloji Bölümü (Lisans)
                                        </li>
                                        <li><i class="fas fa-graduation-cap"></i> Üsküdar Üni. Klinik Psikoloji (Y. Lisans)
                                        </li>
                                        <li><i class="fas fa-star"></i> Oyun Terapisi Eğitimi</li>
                                        <li><i class="fas fa-star"></i> Bilişsel Davranışçı Terapi Eğitimi</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Office Section (Zigzag) -->
        <section id="office" class="office">
            <div class="container">
                <div class="section-title">
                    <h2>Ofisimiz</h2>
                    <p>Huzurlu ve güvenli bir ortamda seanslarımızı gerçekleştiriyoruz.</p>
                </div>
                <div class="office-zigzag">
                    <div class="office-row">
                        <div class="office-img"><img src="assets/images/office/ofis-1.jpg" alt="Ofis"></div>
                        <div class="office-text">
                            <h3>Konforlu Terapi Odası</h3>
                            <p>Danışanlarımızın kendilerini rahat ve güvende hissetmeleri için tasarlanmış modern ve
                                ferah terapi alanımız.</p>
                        </div>
                    </div>
                    <div class="office-row">
                        <div class="office-img"><img src="assets/images/office/ofis-2.jpg" alt="Ofis 2"></div>
                        <div class="office-text">
                            <h3>Profesyonel Görüşme Alanı</h3>
                            <p>Sakin bir atmosferde, gizlilik prensiplerine uygun şekilde seanslarımızı
                                gerçekleştiriyoruz.</p>
                        </div>
                    </div>
                    <div class="office-row">
                        <div class="office-img"><img src="assets/images/office/ofis-3.jpg" alt="Ofis 3"></div>
                        <div class="office-text">
                            <h3>Bekleme ve Dinlenme Alanı</h3>
                            <p>Seans öncesi huzurla bekleyebileceğiniz konforlu dinlenme alanımız.</p>
                        </div>
                    </div>
                    <div class="office-row">
                        <div class="office-img"><img src="assets/images/office/ofis-4.jpg" alt="Ofis 4"></div>
                        <div class="office-text">
                            <h3>Çocuk Oyun ve Terapi Odası</h3>
                            <p>Çocukların dünyasına hitap eden, oyun terapisi için özel olarak donatılmış renkli
                                dünyamız.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section (Swiper) -->
        <section class="testimonials">
            <div class="container">
                <div class="section-title">
                    <h2>Danışan Yorumları</h2>
                </div>
                <div class="swiper testimonial-slider">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <div class="stars">★★★★★</div>
                                <p>"Sena Hanım ile yaptığımız seanslar hayatıma bakış açımı değiştirdi. Çok teşekkür
                                    ederim."</p>
                                <div class="user-info">
                                    <strong>Ahmet Y.</strong>
                                    <span>Google Yorumları</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <div class="stars">★★★★★</div>
                                <p>"Profesyonel yaklaşımı ve samimiyeti sayesinde kendimi çok rahat hissettim."</p>
                                <div class="user-info">
                                    <strong>Melis K.</strong>
                                    <span>Google Yorumları</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Blog Section -->
        <section id="blog" class="blog">
            <div class="container">
                <div class="section-title">
                    <h2>Blog</h2>
                </div>
                <div class="blog-grid">
                    <article class="blog-card">
                        <div class="blog-img"><img src="assets/images/hero-bdt.jpg" alt="Blog"></div>
                        <div class="blog-content">
                            <h3>Kaygı ile Baş Etme</h3>
                            <p>Günlük hayattaki kaygıların üstesinden gelmek için ipuçları.</p>
                            <a href="#" class="read-more">Devamını Oku</a>
                        </div>
                    </article>
                    <article class="blog-card">
                        <div class="blog-img"><img src="assets/images/hero-oyun-terapisi.jpg" alt="Blog"></div>
                        <div class="blog-content">
                            <h3>Sağlıklı İletişim</h3>
                            <p>İlişkilerde yapılan yaygın hatalar ve çözüm yolları.</p>
                            <a href="#" class="read-more">Devamını Oku</a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="faq">
            <div class="container">
                <div class="section-title">
                    <h2>Sık Sorulan Sorular</h2>
                </div>
                <div class="faq-accordion">
                    <div class="faq-item" onclick="this.classList.toggle('active')">
                        <div class="faq-question">Seanslar ne kadar sürüyor? <i class="fas fa-plus"></i></div>
                        <div class="faq-answer">
                            <p>Bireysel terapi seanslarımız ortalama 45-50 dakika sürmektedir. İlk görüşme değerlendirme
                                odaklı olup süresi değişkenlik gösterebilir.</p>
                        </div>
                    </div>
                    <div class="faq-item" onclick="this.classList.toggle('active')">
                        <div class="faq-question">Kaç seans gelmem gerekiyor? <i class="fas fa-plus"></i></div>
                        <div class="faq-answer">
                            <p>Seans sayısı, çalışılan konuya, yaşanılan zorluğun derinliğine ve danışanın ihtiyacına
                                göre değişkenlik gösterir. İlk seanslarda bir süreç planlaması yapılır.</p>
                        </div>
                    </div>
                    <div class="faq-item" onclick="this.classList.toggle('active')">
                        <div class="faq-question">Online terapi ile yüz yüze terapi arasında fark var mı? <i
                                class="fas fa-plus"></i></div>
                        <div class="faq-answer">
                            <p>Bilimsel araştırmalar, uygun teknolojik koşullar sağlandığında online terapinin yüz yüze
                                terapi kadar etkili olduğunu göstermektedir.</p>
                        </div>
                    </div>
                    <div class="faq-item" onclick="this.classList.toggle('active')">
                        <div class="faq-question">Gizlilik ilkesine nasıl uyuluyor? <i class="fas fa-plus"></i></div>
                        <div class="faq-answer">
                            <p>Terapide paylaşılan tüm bilgiler etik kurallar çerçevesinde tamamen gizli tutulur.
                                Danışanın rızası olmadan kesinlikle üçüncü kişilerle paylaşılmaz.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="contact">
            <div class="container">
                <div class="section-title">
                    <h2>İletişim</h2>
                </div>
                <div class="contact-wrapper">
                    <div class="contact-info">
                        <h3>Bize Ulaşın</h3>
                        <p>Adres: Şehremini, Millet Cd. Aydın apt No:131 Daire 4</p>
                        <p>Telefon: 0551 176 52 85</p>
                    </div>
                    <div class="contact-form-container">
                        <form class="appointment-form">
                            <h3>Randevu Talebi</h3>
                            <div class="form-group">
                                <label>Adınız Soyadınız</label>
                                <input type="text" placeholder="Adınız Soyadınız">
                            </div>
                            <button type="submit" class="btn btn-premium">Gönder</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <h3>Erguvan Psikoloji</h3>
                    <p>Ruh sağlığı yolculuğunuzda yanınızdayız.</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Erguvan Psikoloji. Tüm Hakları Saklıdır.</p>
            </div>
        </div>
    </footer>

    <!-- Floating Buttons -->
    <div class="floating-wrapper">
        <a href="https://wa.me/905511765285" class="float-btn whatsapp" target="_blank"><i
                class="fab fa-whatsapp"></i></a>
        <a href="tel:05511765285" class="float-btn phone"><i class="fas fa-phone"></i></a>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // FAQ Accordion
        document.querySelectorAll('.faq-item').forEach(item => {
            item.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
                if (!isActive) item.classList.add('active');
            });
        });

        // Swiper Initialization
        new Swiper('.testimonial-slider', {
            slidesPerView: 1,
            spaceBetween: 30,
            pagination: { el: '.swiper-pagination', clickable: true },
            breakpoints: {
                768: { slidesPerView: 2 }
            }
        });
        document.querySelector('.menu-toggle').addEventListener('click', () => {
            // Placeholder: Implement mobile menu drawer if needed
            alert('Mobil menü açılıyor...');
        });
    </script>
</body>

</html>