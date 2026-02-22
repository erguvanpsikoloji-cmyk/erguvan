<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-about">
                <a href="#" class="logo">Erguvan <span>Psikoloji</span></a>
                <p>Ruh sağlığı yolculuğunuzda yanınızdayız.</p>
            </div>
            <div class="footer-links">
                <h4>Hızlı Bağlantılar</h4>
                <ul>
                    <li><a href="#home">Ana Sayfa</a></li>
                    <li><a href="#services">Hizmetlerimiz</a></li>
                    <li><a href="#about">Ekibimiz</a></li>
                    <li><a href="#blog">Blog</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h4>İletişim</h4>
                <p><i class="fas fa-phone"></i> <a href="tel:+905511765285"
                        style="color:inherit; text-decoration:none;">05511765285</a></p>
                <p><i class="fas fa-envelope"></i> info@uzmanpsikologsenaceren.com</p>
                <p><i class="fas fa-map-marker-alt"></i> Şehremini, Millet Cd. 34098 Fatih/İstanbul</p>
            </div>
            <div class="footer-social">
                <h4>Takip Edin</h4>
                <div class="social-links">
                    <a href="https://instagram.com/uzm.psk.senaceren" target="_blank"><i
                            class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Erguvan Psikoloji. Tüm Hakları Saklıdır.</p>
        </div>
    </div>
</footer>

<!-- Main Script (Defer for performance) -->
<script src="<?php echo asset_url('js/script.js'); ?>" defer></script>

<!-- Floating Action Buttons -->
<div class="floating-buttons">
    <a href="tel:+905511765285" class="floating-btn phone-btn" aria-label="Hemen Ara" title="Hemen Ara">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path
                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
            </path>
        </svg>
    </a>
    <a href="https://wa.me/905511765285" target="_blank" rel="nofollow noopener noreferrer"
        class="floating-btn whatsapp-btn" aria-label="WhatsApp ile İletişim" title="WhatsApp ile İletişim">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path
                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
        </svg>
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
    "logo" => (file_exists(__DIR__ . '/../assets/images/logo.webp') ? $site_url . asset_url('images/logo.webp') : (file_exists(__DIR__ . '/../assets/images/logo.png') ? $site_url . asset_url('images/logo.png') : '')),
    "image" => (file_exists(__DIR__ . '/../assets/images/logo.webp') ? $site_url . asset_url('images/logo.webp') : (file_exists(__DIR__ . '/../assets/images/logo.png') ? $site_url . asset_url('images/logo.png') : '')),
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