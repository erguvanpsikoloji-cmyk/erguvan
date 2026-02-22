<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="footer-logo">
                    <img src="<?php echo asset_url('images/logo_new.png'); ?>" alt="Erguvan Psikoloji"
                        style="height: 60px; width: auto; margin-bottom: 20px;">
                </div>
                <p>Akademik temelli, etik ve profesyonel psikolojik danışmanlık hizmetleri ile yanınızdayız.</p>
                <div class="footer-social">
                    <a href="https://instagram.com/erguvanpsikoloji" target="_blank" aria-label="Instagram"><i
                            class="fab fa-instagram"></i></a>
                    <a href="#" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="footer-nav">
                <h4>Hızlı Menü</h4>
                <ul>
                    <li><a href="<?php echo url(); ?>">Ana Sayfa</a></li>
                    <li><a href="<?php echo url('#hizmetler'); ?>">Hizmetlerimiz</a></li>
                    <li><a href="<?php echo page_url('blog.php'); ?>">Blog</a></li>
                    <li><a href="<?php echo url('#iletisim'); ?>">İletişim</a></li>
                </ul>
            </div>

            <div class="footer-services">
                <h4>Uzmanlıklar</h4>
                <ul>
                    <li><a href="#">Bireysel Terapi</a></li>
                    <li><a href="#">Çift Terapisi</a></li>
                    <li><a href="#">Çocuk ve Ergen</a></li>
                    <li><a href="#">Online Danışmanlık</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h4>İletişim</h4>
                <ul>
                    <li><i class="fas fa-phone-alt"></i> <a href="tel:+905511765285">05511765285</a></li>
                    <li><i class="fas fa-envelope"></i> <a
                            href="mailto:info@erguvanpsikoloji.com">info@erguvanpsikoloji.com</a></li>
                    <li><i class="fas fa-map-marker-alt"></i> <span>Şehremini, Millet Cd. 34098 Fatih/İstanbul</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Erguvan Psikoloji. Tüm hakları saklıdır.</p>
        </div>
    </div>
</footer>

<!-- Assets -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="<?php echo asset_url('js/script.js'); ?>" defer></script>

<!-- Floating Buttons -->
<div class="floating-cta">
    <a href="https://wa.me/905511765285" class="cta-btn whatsapp" target="_blank" aria-label="WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    <a href="tel:+905511765285" class="cta-btn phone" aria-label="Arayın">
        <i class="fas fa-phone"></i>
    </a>
</div>

<?php
// Config dosyasını yükle (eğer yüklenmemişse)
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
    "logo" => $site_url . asset_url('images/logo_new.png'),
    "image" => $site_url . asset_url('images/logo_new.png'),
    "telephone" => "+905511765285",
    "email" => "info@uzmanpsikologsenaceren.com",
    "address" => [
        "@type" => "PostalAddress",
        "streetAddress" => "Şehremini, Millet Cd. 34098",
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