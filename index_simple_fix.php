<?php
// HTML Minification Function
function sanitize_output($buffer)
{
    if (strpos($buffer, '<pre>') !== false)
        return $buffer; // Skip minification if <pre> exists to allow formatting
    $search = array(
        '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
        '/[^\S ]+\</s',     // strip whitespaces before tags, except space
        '/(\s)+/s',         // shorten multiple whitespace sequences
        '/<!--(.|\s)*?-->/' // Remove HTML comments
    );
    $replace = array('>', '<', '\\1', '');
    $buffer = preg_replace($search, $replace, $buffer);
    return $buffer;
}
ob_start("sanitize_output");
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erguvan Psikoterapi Merkezi | Akademik & Premium Destek</title>

    <!-- Meta Tags -->
    <meta name="description"
        content="Erguvan Psikoterapi Merkezi - Modern, akademik ve insan odaklı psikoterapi hizmetleri.">

    <!-- Performance Hints -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

    <!-- Fonts (Async Load) -->
    <link rel="stylesheet" href="assets/css/style2.css"> <!-- Main Critical CSS -->

    <!-- Icons (Deferred) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </noscript>
    <!-- Mobile & Performance Optimizations -->
    <style>
        /* Mobile Menu Styles */
        .hamburger {
            display: none;
            cursor: pointer;
            font-size: 1.5rem;
            color: var(--primary);
            z-index: 1001;
        }

        @media (max-width: 900px) {
            .hero-image-wrapper img {
                height: 300px !important;
            }

            .navbar .logo img {
                max-height: 50px !important;
            }

            .navbar .logo span:first-child {
                font-size: 1.2rem !important;
            }

            .navbar .logo span:last-child {
                font-size: 0.7rem !important;
            }

            .section {
                padding: 4rem 0 !important;
            }

            .contact-grid iframe {
                height: 300px !important;
            }

            /* Hamburger Menu Logic */
            .hamburger {
                display: block;
            }

            .nav-links {
                position: fixed;
                top: 70px;
                /* Adjust based on navbar height */
                left: 0;
                width: 100%;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(10px);
                flex-direction: column;
                align-items: center;
                padding: 2rem 0;
                gap: 1.5rem;
                transform: translateY(-150%);
                transition: transform 0.4s ease-in-out;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                z-index: 999;
            }

            .nav-links.active {
                transform: translateY(0);
            }

            .nav-phone {
                display: block;
                margin-top: 1rem;
            }

            /* Hide 'Hemen Ara' button on mobile if it clutters, or keep it */
            .navbar .btn-premium {
                display: none;
                /* Hide desktop button, show inside menu if needed */
            }
        }
    </style>
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container nav-container">
            <a href="#" class="logo" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
                <img src="assets/images/logo_icon.png" alt="Erguvan Psikoloji Logo"
                    style="max-height: 65px; width: auto; object-fit: contain;">
                <div style="display: flex; flex-direction: column; justify-content: center;">
                    <span
                        style="font-family: 'Playfair Display', serif; font-size: 1.6rem; font-weight: 700; color: var(--primary); line-height: 1.1;">Erguvan
                        Psikoloji</span>
                    <span
                        style="font-family: 'Inter', sans-serif; font-size: 0.85rem; color: #555; letter-spacing: 0.5px; font-weight: 500;">Uzman
                        Klinik Psikolog Desteği</span>
                </div>
            </a>

            <div class="hamburger" id="hamburger">
                <i class="fas fa-bars"></i>
            </div>

            <ul class="nav-links" id="nav-links">
                <li><a href="#home">Ana Sayfa</a></li>
                <li><a href="#team">Ekibimiz</a></li>
                <li><a href="#office">Ofisimiz</a></li>
                <li><a href="#services">Hizmetler</a></li>
                <li><a href="#blog">Blog</a></li>
                <li><a href="#contact">İletişim</a></li>
                <li class="nav-phone"><a href="tel:+905511765285"><i class="fas fa-phone-alt"></i> 0551 176 52 85</a>
                </li>
            </ul>
            <a href="tel:+905511765285" class="btn-premium" style="padding: 0.8rem 1.5rem; font-size: 0.9rem;">Hemen
                Ara</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero" id="home">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>
                    Erguvan <span>Psikoloji</span>
                </h1>
                <p
                    style="font-weight: 600; color: var(--primary); font-family: var(--font-body); margin-bottom: 1rem; font-size: 1.4rem;">
                    Uzman Klinik Psikolog Desteği
                </p>
                <p>
                    Modern bilimin ışığında, insan ruhunun derinliklerine saygı duyan bir yaklaşımla profesyonel
                    psikoterapi hizmeti sunuyoruz.
                </p>
                <div class="hero-btns">
                    <a href="#contact" class="btn-premium">Randevu Al</a>
                    <a href="#services" class="btn-premium"
                        style="background: transparent; color: var(--primary); border: 2px solid var(--primary); box-shadow: none; margin-left: 1rem;">Hizmetlerimiz</a>
                </div>
            </div>
            <div class="hero-image-wrapper">
                <img src="assets/images/hero-psikolojik-destek.jpg" alt="Erguvan Psikoloji Profesyonel Destek"
                    style="width: 100%; height: 500px; object-fit: cover; border-radius: var(--radius-lg); box-shadow: var(--shadow-xl);"
                    fetchpriority="high">
            </div>
        </div>
    </header>

    <!-- Team Section -->
    <section class="section" id="team">
        <div class="container">
            <div class="section-title">
                <h2>Uzman Ekibimiz</h2>
                <p>Akademik birikim ve klinik tecrübeyi harmanlayan profesyonel kadromuz.</p>
                <div></div>
            </div>
            <div class="team-grid">
                <div class="team-card">
                    <img src="assets/images/team/sena.jpg" alt="Uzm. Klinik Psk. Sena Ceren Parmaksız" class="card-img"
                        style="height: 450px; object-position: center;" loading="lazy">
                    <div class="card-info">
                        <h3>Sena Ceren Parmaksız</h3>
                        <p style="color: var(--secondary); font-weight: 600;">Uzman Klinik Psikolog</p>
                    </div>
                </div>
                <div class="team-card">
                    <img src="assets/images/team/sedat.jpg" alt="Uzm. Psikolog Sedat Parmaksız" class="card-img"
                        style="height: 450px; object-position: center top;" loading="lazy">
                    <div class="card-info">
                        <h3>Sedat Parmaksız</h3>
                        <p style="color: var(--secondary); font-weight: 600;">Uzman Psikolog</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Office Section -->
    <section class="section" id="office" style="background-color: var(--luxe-bg);">
        <div class="container">
            <div class="section-title">
                <h2>Ofisimiz</h2>
                <p>Huzurlu, güvenli ve profesyonel bir terapi ortamı.</p>
                <div></div>
            </div>
            <div class="office-grid">
                <div class="office-card">
                    <img src="assets/images/office/ofis-1.jpg" alt="Ofisimiz 1" class="card-img"
                        style="height: 300px; width: 100%; object-fit: cover;" loading="lazy">
                </div>
                <div class="office-card">
                    <img src="assets/images/office/office2.jpg" alt="Ofisimiz 2" class="card-img"
                        style="height: 300px; width: 100%; object-fit: cover;" loading="lazy">
                </div>
                <div class="office-card">
                    <img src="assets/images/office/office3.jpg" alt="Ofisimiz 3" class="card-img"
                        style="height: 300px; width: 100%; object-fit: cover;" loading="lazy">
                </div>
                <div class="office-card">
                    <img src="assets/images/office/office4.jpg" alt="Ofisimiz 4" class="card-img"
                        style="height: 300px; width: 100%; object-fit: cover;" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="section" id="services" style="background-color: var(--luxe-bg);">
        <div class="container">
            <div class="section-title">
                <h2>Hizmetlerimiz</h2>
                <p>Modern ve bilimsel methodsal yöntemlerle sunduğumuz profesyonel destek alanları.</p>
                <div></div>
            </div>
            <div class="services-grid">
                <!-- Bireysel Terapi -->
                <div class="service-card">
                    <i class="fas fa-user"></i>
                    <h3>Bireysel Terapi</h3>
                    <p style="color: var(--text-muted);">Kişinin kendini tanıması, duygusal zorluklarla baş etmesi ve
                        içsel potansiyelini gerçekleştirmesi için birebir psikolojik destek süreci.</p>
                </div>

                <!-- Çocuk Terapisi -->
                <div class="service-card">
                    <i class="fas fa-child"></i>
                    <h3>Çocuk Terapisi</h3>
                    <p style="color: var(--text-muted);">Çocukların gelişimi, duygusal ve davranışsal sorunları üzerine
                        odaklanan, yaş gruplarına özel yapılandırılmış terapi hizmeti.</p>
                </div>

                <!-- Ergen Terapisi -->
                <div class="service-card">
                    <i class="fas fa-user-graduate"></i>
                    <h3>Ergen Terapisi</h3>
                    <p style="color: var(--text-muted);">Ergenlik döneminin getirdiği kimlik karmaşası, sınav kaygısı ve
                        aile içi çatışmalar gibi konularda gençlere yönelik profesyonel destek.</p>
                </div>

                <!-- Çift Terapisi -->
                <div class="service-card">
                    <i class="fas fa-heart"></i>
                    <h3>Çift Terapisi</h3>
                    <p style="color: var(--text-muted);">İlişki dinamiklerini iyileştirmek, iletişimi güçlendirmek ve
                        yaşanan çatışmaları sağlıklı bir zeminde çözmek için çiftlere özel danışmanlık.</p>
                </div>

                <!-- Oyun Terapisi -->
                <div class="service-card">
                    <i class="fas fa-puzzle-piece"></i>
                    <h3>Oyun Terapisi</h3>
                    <p style="color: var(--text-muted);">Çocukların iç dünyalarını, korkularını ve ihtiyaçlarını oyun ve
                        oyuncaklar aracılığıyla ifade etmelerini sağlayan iyileştirici yöntem.</p>
                </div>

                <!-- Ebeveyn Danışmanlığı -->
                <div class="service-card">
                    <i class="fas fa-users"></i>
                    <h3>Ebeveyn Danışmanlığı</h3>
                    <p style="color: var(--text-muted);">Çocuk yetiştirme sürecinde karşılaşılan zorluklar, sınır koyma
                        ve sağlıklı ebeveyn-çocuk ilişkisi kurma konularında rehberlik.</p>
                </div>

                <!-- Psikolojik Ölçekler -->
                <div class="service-card">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>Psikolojik Ölçekler</h3>
                    <p style="color: var(--text-muted);">Tanı ve tedavi sürecini desteklemek amacıyla uygulanan,
                        bilimsel geçerliliği ve güvenilirliği kanıtlanmış gelişim ve zeka testleri.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section" id="testimonials" style="background-color: #FFFFFF;">
        <div class="container">
            <div class="section-title">
                <h2 style="color: var(--secondary);">Danışan Yorumları</h2>
                <div style="margin-bottom: 1.5rem;"></div>
                <p>Yolculuğumuza eşlik edenlerin deneyimleri.</p>
            </div>

            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <i class="fas fa-quote-left quote-icon"></i>
                    <p class="testimonial-text">
                        "Terapi süreci boyunca kendimi keşfetme yolculuğumda bana sağladıkları profesyonel destek ve
                        akademik yaklaşım, hayata bakış açımı tamamen değiştirdi. Güven veren bir ortamda olduğumu her
                        an hissettim."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-info">
                            <h4>H. A.</h4>
                            <span>Bireysel Terapi Danışanı</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <i class="fas fa-quote-left quote-icon"></i>
                    <p class="testimonial-text">
                        "Eşimle yaşadığımız iletişim sorunlarını çözmemizde, uzman kadronun objektif ve yapıcı yaklaşımı
                        çok etkili oldu. İlişkimizde yeni ve sağlıklı bir temel kurmamıza yardımcı oldular."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-info">
                            <h4>M. & E. Ş.</h4>
                            <span>Aile & Çift Terapisi Danışanı</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <i class="fas fa-quote-left quote-icon"></i>
                    <p class="testimonial-text">
                        "Kızımızın gelişim sürecinde karşılaştığımız zorluklarda, ekibin çocuk dünyasına olan derin
                        anlayışı ve ebeveyn olarak bize sundukları rehberlik paha biçilemezdi. Çok teşekkür ederiz."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-info">
                            <h4>S. K.</h4>
                            <span>Ebeveyn / Danışan Yakını</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="section" id="blog" style="background-color: var(--luxe-bg);">
        <div class="container">
            <div class="section-title">
                <h2>Güncel Blog Yazılarımız</h2>
                <div style="margin-bottom: 1.5rem;"></div>
                <p>Psikoloji dünyasından akademik ve güncel paylaşımlar.</p>
            </div>

            <div class="office-grid"> <!-- Reusing grid layout for consistent spacing -->
                <div class="blog-card">
                    <div class="blog-date">12 Şub 2026</div>
                    <div
                        style="height: 250px; background: #eee; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-brain fa-3x" style="color: #ccc;"></i>
                    </div>
                    <div class="card-info">
                        <h3>Kaygı ile Başa Çıkma Yöntemleri</h3>
                        <p>Modern yaşamın getirdiği stres faktörlerini akademik bir bakış açısıyla analiz ediyoruz...
                        </p>
                        <a href="#" class="read-more">Devamını Oku <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="blog-card">
                    <div class="blog-date">10 Şub 2026</div>
                    <div
                        style="height: 250px; background: #eee; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-heart fa-3x" style="color: #ccc;"></i>
                    </div>
                    <div class="card-info">
                        <h3>İlişkilerde Sağlıklı İletişim</h3>
                        <p>Çift terapisi ekollerinin önerdiği pratik iletişim teknikleri ve uygulama yöntemleri...</p>
                        <a href="#" class="read-more">Devamını Oku <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="blog-card">
                    <div class="blog-date">08 Şub 2026</div>
                    <div
                        style="height: 250px; background: #eee; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-child fa-3x" style="color: #ccc;"></i>
                    </div>
                    <div class="card-info">
                        <h3>Çocuklarda Oyunun Önemi</h3>
                        <p>Oyun terapisinin çocuk gelişimindeki kritik rolü ve ebeveynlere öneriler...</p>
                        <a href="#" class="read-more">Devamını Oku <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <div style="text-align: center; margin-top: 4rem;">
                <a href="blog.php" class="btn-premium"
                    style="background: transparent; color: var(--primary); border: 2px solid var(--primary); box-shadow: none;">Tüm
                    Yazıları Gör</a>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section" id="faq" style="background-color: #FFFFFF;">
        <div class="container">
            <div class="section-title">
                <h2 style="color: var(--secondary);">Sıkça Sorulan Sorular</h2>
                <div style="margin-bottom: 1.5rem;"></div>
                <p>Terapi süreci ve işleyişimiz hakkında merak edilenler.</p>
            </div>

            <div class="faq-container">
                <div class="faq-item">
                    <button class="faq-header">
                        <span class="faq-question">Seanslar ne kadar sürüyor?</span>
                        <i class="fas fa-plus faq-icon"></i>
                    </button>
                    <div class="faq-content">
                        <p class="faq-answer">
                            Bireysel terapi seanslarımız standart olarak 45-50 dakika sürmektedir. Çift ve aile terapisi
                            seansları ihtiyaca göre daha uzun planlanabilmektedir.
                        </p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-header">
                        <span class="faq-question">Kaç seans gelmem gerekiyor?</span>
                        <i class="fas fa-plus faq-icon"></i>
                    </button>
                    <div class="faq-content">
                        <p class="faq-answer">
                            Terapi süresi, çalışılan konunun derinliğine, kişinin ihtiyaçlarına ve belirlenen hedeflere
                            göre kişiden kişiye farklılık gösterir. İlk seanslarda bu konuda genel bir yol haritası
                            oluşturulur.
                        </p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-header">
                        <span class="faq-question">Online terapi ile yüz yüze terapi arasında fark var mı?</span>
                        <i class="fas fa-plus faq-icon"></i>
                    </button>
                    <div class="faq-content">
                        <p class="faq-answer">
                            Bilimsel çalışmalar, online terapinin birçok psikolojik zorlukta yüz yüze terapi kadar
                            etkili olduğunu göstermektedir. Önemli olan terapötik bağın kurulması ve gizliliğin
                            sağlanmasıdır.
                        </p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-header">
                        <span class="faq-question">Gizlilik ilkesine nasıl uyuluyor?</span>
                        <i class="fas fa-plus faq-icon"></i>
                    </button>
                    <div class="faq-content">
                        <p class="faq-answer">
                            Terapide paylaşılan tüm bilgiler etik kurallar ve yasalar çerçevesinde tamamen gizli
                            tutulur. Danışanın onayı olmadan hiçbir bilgi üçüncü şahıslarla paylaşılmaz.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section" id="contact">
        <div class="container">
            <div class="section-title">
                <h2 style="color: var(--secondary);">İletişime Geçin</h2>
                <div style="margin-bottom: 1.5rem;"></div>
                <p>Size nasıl yardımcı olabileceğimizi konuşmak için bizimle iletişime geçin.</p>
            </div>

            <div class="contact-grid">
                <!-- Contact Info -->
                <div class="contact-info-panel">
                    <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--primary);">İletişim Bilgilerimiz
                    </h3>
                    <p style="color: var(--text-muted); margin-bottom: 3rem;">Bize her zaman ulaşabilirsiniz.</p>

                    <div class="contact-info-list">
                        <div class="contact-info-item">
                            <i class="fas fa-map-marker-alt" style="color: #8B3D48;"></i>
                            <div class="contact-info-text">
                                <h4>Adres</h4>
                                <p>Şehremini, Millet Cd. Aydın apt No:131 Daire 4<br>34098 Fatih/İstanbul</p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <i class="fas fa-phone" style="color: #EC4899;"></i>
                            <div class="contact-info-text">
                                <h4>Telefon</h4>
                                <p>0551 176 52 84</p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <i class="fas fa-envelope" style="color: #E9D5FF;"></i>
                            <div class="contact-info-text">
                                <h4>E-posta</h4>
                                <p>info@uzmanpsikologsenaceren.com</p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <i class="fas fa-clock" style="color: #F87171;"></i>
                            <div class="contact-info-text">
                                <h4>Çalışma Saatleri</h4>
                                <p>Hafta içi: 09:00 - 22:00<br>Hafta sonu: 09:00 - 21:00</p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <i class="fab fa-instagram" style="color: #DB2777;"></i>
                            <div class="contact-info-text">
                                <h4>Instagram</h4>
                                <p>@uzmanpsikologsenaceren</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="form-premium">
                    <h3>Randevu Talebi</h3>
                    <form action="#" method="POST">
                        <div class="form-group">
                            <label>Adınız Soyadınız *</label>
                            <input type="text" class="form-control" placeholder="Adınız Soyadınız" required>
                        </div>
                        <div class="form-group">
                            <label>E-posta Adresiniz *</label>
                            <input type="email" class="form-control" placeholder="E-posta Adresiniz" required>
                        </div>
                        <div class="form-group">
                            <label>Telefon Numaranız *</label>
                            <input type="tel" class="form-control" placeholder="Telefon Numaranız" required>
                        </div>
                        <div class="form-group">
                            <label>Hizmet *</label>
                            <select class="form-control" required>
                                <option value="" disabled selected>Hizmet Seçiniz</option>
                                <option>Bireysel Terapi</option>
                                <option>Çocuk & Ergen Terapisi</option>
                                <option>Çift & Aile Terapisi</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Mesajınız</label>
                            <textarea class="form-control" rows="4" placeholder="Mesajınız"></textarea>
                        </div>
                        <button type="submit" class="btn-premium"
                            style="width: 100%; border: none; cursor: pointer;">Randevu Talebi Gönder</button>
                    </form>
                </div>
            </div>

            <!-- Google Map -->
            <div
                style="margin-top: 3rem; border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-lg);">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3010.596008878848!2d28.9330173!3d41.0151072!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cabbbebb427bb9%3A0x3fec28c0f52032ea!2sFatih%20Psikolog-Erguvan%20Psikoloji%20ve%20Dan%C4%B1%C5%9Fmanl%C4%B1k%20Merkezi-Uzman%20Klinik%20Psikolog-!5e0!3m2!1str!2str!4v1707200000000!5m2!1str!2str"
                    width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>

            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="https://www.google.com/maps/dir//Fatih+Psikolog-Erguvan+Psikoloji+ve+Dan%C4%B1%C5%9Fmanl%C4%B1k+Merkezi-Uzman+Klinik+Psikolog-,+%C5%9Eehremini,+Turgut+%C3%96zal+Millet+Cd.+Ayd%C4%B1n+apt+No:131+Daire+4,+34098+Fatih%2F%C4%B0stanbul/@40.9952764,28.9412191,15z/data=!4m8!4m7!1m0!1m5!1m1!1s0x14cabbbebb427bb9:0x3fec28c0f52032ea!2m2!1d28.9330173!2d41.0151072?entry=ttu&g_ep=EgoyMDI2MDIwMy4wIKXMDSoKLDEwMDc5MjA2OUgBUAM%3D"
                    target="_blank" class="btn-premium">
                    <i class="fas fa-location-arrow"></i> Yol Tarifi Al
                </a>
            </div>
        </div>
    </section>

    <footer class="footer-modern">
        <div class="container">
            <div class="footer-grid">
                <!-- Brand Column -->
                <div class="footer-col">
                    <h4>Erguvan Psikoloji</h4>
                    <p>
                        Profesyonel psikolojik danışmanlık ve terapi hizmetleri ile sizlere destek olmak için buradayız.
                        Akademik ve etik değerlere bağlı kalarak yanınızdayız.
                    </p>
                    <div class="social-links">
                        <a href="https://www.instagram.com/uzmanpsikologsenaceren/" class="social-icon instagram"
                            target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon linkedin"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-col">
                    <h4>Hızlı Linkler</h4>
                    <ul class="footer-links">
                        <li><a href="#home">Ana Sayfa</a></li>
                        <li><a href="#services">Hizmetlerimiz</a></li>
                        <li><a href="#team">Hakkımızda</a></li>
                        <li><a href="#blog">Blog</a></li>
                        <li><a href="#contact">İletişim</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div class="footer-col">
                    <h4>Hizmetler</h4>
                    <ul class="footer-links">
                        <li><a href="#">Bireysel Terapi</a></li>
                        <li><a href="#">Çift Terapisi</a></li>
                        <li><a href="#">Online Terapi</a></li>
                        <li><a href="#">Aile Danışmanlığı</a></li>
                        <li><a href="#">Çocuk & Ergen</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="footer-col">
                    <h4>İletişim</h4>
                    <ul class="footer-links">
                        <li>Şehremini, Millet Cd. Aydın apt<br>No:131 Daire 4, 34098 Fatih/İstanbul</li>
                        <li><br></li>
                        <li>Hafta içi her gün: 09:00-22:00</li>
                        <li>Hafta sonu her gün: 09:00-21:00</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2026 Erguvan Psikoterapi Merkezi. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <!-- Floating Actions -->
    <div class="floating-actions">
        <a href="https://wa.me/905511765285" class="float-btn whatsapp" target="_blank" title="WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="tel:+905511765285" class="float-btn phone" title="Hemen Ara">
            <i class="fas fa-phone"></i>
        </a>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Hamburger Menu Toggle
            const hamburger = document.getElementById('hamburger');
            const navLinks = document.getElementById('nav-links');

            if (hamburger && navLinks) {
                hamburger.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    navLinks.classList.toggle('active');

                    const icon = hamburger.querySelector('i');
                    if (navLinks.classList.contains('active')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    } else {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                });

                // Close menu when clicking outside
                document.addEventListener('click', function (e) {
                    if (!hamburger.contains(e.target) && !navLinks.contains(e.target) && navLinks.classList.contains('active')) {
                        navLinks.classList.remove('active');
                        const icon = hamburger.querySelector('i');
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                });

                // Close menu when a link is clicked
                navLinks.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        navLinks.classList.remove('active');
                        const icon = hamburger.querySelector('i');
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    });
                });
            }

            // Navbar Scroll Effect
            window.addEventListener('scroll', function () {
                const navbar = document.getElementById('navbar');
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // FAQ Toggle
            document.querySelectorAll('.faq-header').forEach(header => {
                header.addEventListener('click', () => {
                    const item = header.parentElement;
                    const isActive = item.classList.contains('active');

                    document.querySelectorAll('.faq-item').forEach(otherItem => {
                        otherItem.classList.remove('active');
                    });

                    if (!isActive) {
                        item.classList.add('active');
                    }
                });
            });
        });
    </script>
</body>

</html>