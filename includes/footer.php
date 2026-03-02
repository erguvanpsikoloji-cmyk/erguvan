<footer class="footer" style="background-color: var(--primary); color: white; padding: 5rem 0 2rem;">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="footer-logo" style="margin-bottom: 2rem;">
                    <a href="<?php echo url(); ?>" style="text-decoration: none; display: flex; align-items: center;">
                        <img src="<?php echo asset_url('images/logo_icon.png'); ?>" alt="Erguvan Psikoloji"
                            style="height: 50px; width: auto; filter: brightness(0) invert(1);">
                        <div class="logo-text" style="margin-left: 1rem; text-align: left;">
                            <span class="logo-title"
                                style="display: block; font-family: var(--font-heading); font-size: 1.8rem; color: white;">Erguvan
                                Psikoloji</span>
                            <span class="logo-subtitle"
                                style="display: block; font-size: 0.7rem; color: rgba(255,255,255,0.7); letter-spacing: 1px; font-weight: 600;">Uzman
                                Klinik Psikolog Desteği</span>
                        </div>
                    </a>
                </div>
                <p style="color: rgba(255,255,255,0.7); line-height: 1.8; margin-bottom: 2rem; max-width: 400px;">
                    Akademik temelli, etik ve profesyonel psikolojik danışmanlık hizmetleri ile yanınızdayız. Modern
                    bilimin ışığında profesyonel destek sunuyoruz.
                </p>
                <div class="footer-social" style="display:flex; gap:15px;">
                    <a href="https://instagram.com/erguvanpsikoloji" target="_blank" aria-label="Instagram"
                        style="color:white; background: rgba(255,255,255,0.1); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: var(--transition);">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" target="_blank" aria-label="LinkedIn"
                        style="color:white; background: rgba(255,255,255,0.1); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: var(--transition);">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            <div class="footer-nav">
                <h4 style="color:white; font-family: var(--font-heading); margin-bottom: 1.5rem;">Hızlı Menü</h4>
                <ul style="list-style:none; padding:0;">
                    <li><a href="<?php echo url(); ?>"
                            style="color:rgba(255,255,255,0.7); text-decoration:none; transition: var(--transition); display: block; margin-bottom: 0.8rem;">Ana
                            Sayfa</a></li>
                    <li><a href="<?php echo url('#hizmetler'); ?>"
                            style="color:rgba(255,255,255,0.7); text-decoration:none; transition: var(--transition); display: block; margin-bottom: 0.8rem;">Hizmetlerimiz</a>
                    </li>
                    <li><a href="<?php echo page_url('blog.php'); ?>"
                            style="color:rgba(255,255,255,0.7); text-decoration:none; transition: var(--transition); display: block; margin-bottom: 0.8rem;">Blog</a>
                    </li>
                    <li><a href="<?php echo url('#iletisim'); ?>"
                            style="color:rgba(255,255,255,0.7); text-decoration:none; transition: var(--transition); display: block; margin-bottom: 0.8rem;">İletişim</a>
                    </li>
                </ul>
            </div>

            <div class="footer-services">
                <h4 style="color:white; font-family: var(--font-heading); margin-bottom: 1.5rem;">Uzmanlıklar</h4>
                <ul style="list-style:none; padding:0;">
                    <li><a href="#"
                            style="color:rgba(255,255,255,0.7); text-decoration:none; transition: var(--transition); display: block; margin-bottom: 0.8rem;">Bireysel
                            Terapi</a></li>
                    <li><a href="#"
                            style="color:rgba(255,255,255,0.7); text-decoration:none; transition: var(--transition); display: block; margin-bottom: 0.8rem;">Çift
                            Terapisi</a></li>
                    <li><a href="#"
                            style="color:rgba(255,255,255,0.7); text-decoration:none; transition: var(--transition); display: block; margin-bottom: 0.8rem;">Çocuk
                            ve Ergen</a></li>
                    <li><a href="#"
                            style="color:rgba(255,255,255,0.7); text-decoration:none; transition: var(--transition); display: block; margin-bottom: 0.8rem;">Online
                            Danışmanlık</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h4 style="color:white; font-family: var(--font-heading); margin-bottom: 1.5rem;">İletişim</h4>
                <ul style="list-style:none; padding:0;">
                    <li style="color:rgba(255,255,255,0.7); margin-bottom:12px;"><i class="fas fa-phone-alt"
                            style="color: white; margin-right: 10px;"></i> <a href="tel:+905511765285"
                            style="color:inherit; text-decoration:none;">05511765285</a>
                    </li>
                    <li style="color:rgba(255,255,255,0.7); margin-bottom:12px;"><i class="fas fa-envelope"
                            style="color: white; margin-right: 10px;"></i> <a href="mailto:info@erguvanpsikoloji.com"
                            style="color:inherit; text-decoration:none;">info@erguvanpsikoloji.com</a></li>
                    <li style="color:rgba(255,255,255,0.7);"><i class="fas fa-map-marker-alt"
                            style="color: white; margin-right: 10px;"></i> Şehremini, Millet Cd. 34098
                        Fatih/İstanbul</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom"
            style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 3rem; padding-top: 2rem;">
            <p style="color: rgba(255,255,255,0.5); font-size: 0.85rem;">&copy; <?php echo date('Y'); ?> Erguvan
                Psikoloji. Tüm hakları saklıdır.</p>
        </div>
    </div>
</footer>

<!-- Assets -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="<?php echo asset_url('js/script.js'); ?>" defer></script>


<!-- Floating Contact — Premium Option B -->
<style>
    .floating-contact {
        position: fixed;
        bottom: 28px;
        right: 24px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
    }

    .fc-options {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-end;
        pointer-events: none;
        opacity: 0;
        transform: translateY(12px);
        transition: opacity 0.35s cubic-bezier(.23, 1, .32, 1), transform 0.35s cubic-bezier(.23, 1, .32, 1);
    }

    .fc-options.open {
        pointer-events: all;
        opacity: 1;
        transform: translateY(0);
    }

    .fc-option-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border-radius: 50px;
        padding: 10px 18px 10px 10px;
        text-decoration: none;
        color: #0F172A;
        font-family: 'Montserrat', sans-serif;
        font-size: 0.82rem;
        font-weight: 600;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.10);
        border: 1.5px solid #f1e8f5;
        white-space: nowrap;
        transition: transform 0.25s, box-shadow 0.25s;
    }

    .fc-option-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(145, 95, 120, 0.18);
    }

    .fc-option-icon {
        width: 32px;
        height: 32px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .fc-option-icon.wa {
        background: linear-gradient(135deg, #915F78 0%, #70475E 100%);
        color: #fff;
    }

    .fc-option-icon.ph {
        background: #0F172A;
        color: #fff;
    }

    .fc-main-btn {
        width: 54px;
        height: 54px;
        border-radius: 18px;
        background: linear-gradient(135deg, #915F78 0%, #70475E 100%);
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        box-shadow: 0 4px 20px rgba(145, 95, 120, 0.28);
        transition: transform 0.3s cubic-bezier(.23, 1, .32, 1), box-shadow 0.3s, border-radius 0.3s, background 0.3s;
        outline: none;
    }

    .fc-main-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(145, 95, 120, 0.36);
    }

    .fc-main-btn.active {
        background: #0F172A;
        border-radius: 50%;
    }
</style>

<div class="floating-contact">
    <div class="fc-options" id="fcOptions">
        <a href="https://wa.me/905511765285" class="fc-option-btn" target="_blank">
            <span class="fc-option-icon wa">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="16" height="16"
                    fill="currentColor">
                    <path
                        d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-4-10.5-6.7z" />
                </svg>
            </span>
            WhatsApp ile Yaz
        </a>
        <a href="tel:+905511765285" class="fc-option-btn">
            <span class="fc-option-icon ph">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="14" height="14"
                    fill="currentColor">
                    <path
                        d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.9-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.9 464-464 0-11.2-7.7-20.9-18.6-23.4z" />
                </svg>
            </span>
            Bizi Arayın
        </a>
    </div>

    <button class="fc-main-btn" id="fcToggle" aria-label="İletişim seçeneklerini göster" aria-expanded="false">
        <!-- Default: chat/contact icon -->
        <svg id="fcIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="22" height="22"
            fill="currentColor">
            <path
                d="M256 448c141.4 0 256-93.1 256-208S397.4 32 256 32S0 125.1 0 240c0 45.1 17.7 86.8 47.7 120.9c-1.9 24.5-11.4 46.3-21.4 62.9c-5.5 9.2-11.1 16.6-15.2 21.6c-2.1 2.5-3.7 4.4-4.9 5.7c-.6 .6-1 1.1-1.3 1.4l-.3 .3 0 0 0 0 0 0 0 0c-4.6 4.6-5.9 11.4-3.4 17.4c2.5 6 8.3 9.9 14.8 9.9c28.7 0 57.6-8.9 81.6-19.3c22.9-10 42.4-21.9 54.3-30.6c31.8 11.5 67 17.9 104.1 17.9z" />
        </svg>
    </button>
</div>

<script>
    (function () {
        const fcToggle = document.getElementById('fcToggle');
        const fcOptions = document.getElementById('fcOptions');
        const fcIcon = document.getElementById('fcIcon');

        const closeIcon = '<path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm-81-337c9.4-9.4 24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9z"/>';
        const chatIcon = '<path d="M256 448c141.4 0 256-93.1 256-208S397.4 32 256 32S0 125.1 0 240c0 45.1 17.7 86.8 47.7 120.9c-1.9 24.5-11.4 46.3-21.4 62.9c-5.5 9.2-11.1 16.6-15.2 21.6c-2.1 2.5-3.7 4.4-4.9 5.7c-.6 .6-1 1.1-1.3 1.4l-.3 .3 0 0 0 0 0 0 0 0c-4.6 4.6-5.9 11.4-3.4 17.4c2.5 6 8.3 9.9 14.8 9.9c28.7 0 57.6-8.9 81.6-19.3c22.9-10 42.4-21.9 54.3-30.6c31.8 11.5 67 17.9 104.1 17.9z"/>';

        if (fcToggle && fcOptions) {
            fcToggle.addEventListener('click', function () {
                const isOpen = fcOptions.classList.toggle('open');
                fcToggle.classList.toggle('active', isOpen);
                fcToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                fcIcon.innerHTML = isOpen ? closeIcon : chatIcon;
            });

            // Close when clicking outside
            document.addEventListener('click', function (e) {
                if (!fcToggle.contains(e.target) && !fcOptions.contains(e.target)) {
                    fcOptions.classList.remove('open');
                    fcToggle.classList.remove('active');
                    fcToggle.setAttribute('aria-expanded', 'false');
                    fcIcon.innerHTML = chatIcon;
                }
            });
        }
    })();
</script>

<?php
// Structured Data already included in header/footer, keeping logic
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config.php';
}

// Structured Data (JSON-LD)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$site_url = $protocol . '://' . $host . BASE_URL;
$current_url = $protocol . '://' . $host . $_SERVER['REQUEST_URI'];

// Organization Schema
$organization_schema = [
    "@context" => "https://schema.org",
    "@type" => "ProfessionalService",
    "name" => "Uzm. Psk. Sena Ceren",
    "description" => "Profesyonel psikolojik danışmanlık ve terapi hizmetleri",
    "url" => $site_url,
    "logo" => $site_url . asset_url('images/logo.webp'),
    "image" => $site_url . asset_url('images/logo.webp'),
    "telephone" => "+905511765285",
    "email" => "info@uzmanpsikologsenaceren.com",
    "address" => [
        "@type" => "PostalAddress",
        "streetAddress" => "Şehremini, Millet Cd. 34098 Fatih/İstanbul",
        "addressLocality" => "Fatih",
        "addressRegion" => "İstanbul",
        "postalCode" => "34098",
        "addressCountry" => "TR"
    ],
    "geo" => [
        "@type" => "GeoCoordinates",
        "latitude" => "41.01528",
        "longitude" => "28.93291"
    ],
    "openingHoursSpecification" => [
        [
            "@type" => "OpeningHoursSpecification",
            "dayOfWeek" => ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
            "opens" => "09:00",
            "closes" => "22:00"
        ],
        [
            "@type" => "OpeningHoursSpecification",
            "dayOfWeek" => ["Saturday", "Sunday"],
            "opens" => "09:00",
            "closes" => "21:00"
        ]
    ],
    "priceRange" => "$$",
    "areaServed" => [
        "@type" => "Country",
        "name" => "Turkey"
    ],
    "serviceType" => [
        "Bireysel Terapi",
        "Çift Terapisi",
        "Online Terapi",
        "Aile Danışmanlığı",
        "Oyun Terapisi",
        "Çocuk ve Ergen Terapisi"
    ],
    "sameAs" => [
        "https://www.instagram.com/uzm.psk.senaceren",
        "https://twitter.com/senaceren"
    ]
];

// WebSite Schema
$website_schema = [
    "@context" => "https://schema.org",
    "@type" => "WebSite",
    "name" => "Erguvan Psikoloji",
    "url" => $site_url,
    "potentialAction" => [
        "@type" => "SearchAction",
        "target" => $site_url . "/pages/blog.php?search={search_term_string}",
        "query-input" => "required name=search_term_string"
    ]
];

// Sayfa tipine göre ek schema
$additional_schema = [];
if (isset($page_type) && $page_type === 'article' && isset($post) && is_array($post) && !empty($post)) {
    // Blog post için Article Schema
    $additional_schema = [
        "@context" => "https://schema.org",
        "@type" => "BlogPosting",
        "headline" => htmlspecialchars($post['title']),
        "description" => htmlspecialchars($post['excerpt']),
        "image" => $post['image'] ?? (file_exists(__DIR__ . '/../assets/images/logo.webp') ? $site_url . asset_url('images/logo.webp') : (file_exists(__DIR__ . '/../assets/images/logo.png') ? $site_url . asset_url('images/logo.png') : '')),
        "datePublished" => date('c', strtotime($post['created_at'])),
        "dateModified" => isset($post['updated_at']) ? date('c', strtotime($post['updated_at'])) : date('c', strtotime($post['created_at'])),
        "author" => [
            "@type" => "Person",
            "name" => "Erguvan Psikoloji"
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => "Erguvan Psikoloji",
            "logo" => [
                "@type" => "ImageObject",
                "url" => (file_exists(__DIR__ . '/../assets/images/logo.webp') ? $site_url . asset_url('images/logo.webp') : (file_exists(__DIR__ . '/../assets/images/logo.png') ? $site_url . asset_url('images/logo.png') : ''))
            ]
        ],
        "mainEntityOfPage" => [
            "@type" => "WebPage",
            "@id" => $current_url
        ]
    ];
}
?>

<!-- Structured Data (JSON-LD) -->
<script type="application/ld+json">
    <?php echo json_encode($organization_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
    </script>

<script type="application/ld+json">
    <?php echo json_encode($website_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
    </script>

<?php if (!empty($additional_schema)): ?>
    <script type="application/ld+json">
                                                                                            <?php echo json_encode($additional_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
                                                                                            </script>
<?php endif; ?>


</body>

</html>