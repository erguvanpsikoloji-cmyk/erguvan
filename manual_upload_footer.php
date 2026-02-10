<?php
// Bu dosya yasaklı FTP işlemlerini atlatmak için sunucu tarafında dosya yazar.
// Güvenlik için işlem bitince silinmelidir.

$files = [
    'includes/footer.php' => '<?php
<footer class="footer" style="background-color: #2d3748; color: white;">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="footer-logo">
                    <img src="<?php echo asset_url(\'images/logo.webp\'); ?>" alt="Erguvan Psikoloji"
                        style="height: 60px; width: auto; margin-bottom: 20px; filter: brightness(0) invert(1);">
                </div>
                <p style="color: #cbd5e0;">Akademik temelli, etik ve profesyonel psikolojik danışmanlık hizmetleri ile yanınızdayız.</p>
                <div class="footer-social" style="display:flex; gap:10px;">
                    <a href="https://instagram.com/erguvanpsikoloji" target="_blank" aria-label="Instagram" style="color:white;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 448 512" fill="currentColor"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.9 0-184.9zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
                    </a>
                    <a href="#" target="_blank" aria-label="LinkedIn" style="color:white;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 448 512" fill="currentColor"><path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"/></svg>
                    </a>
                </div>
            </div>

            <div class="footer-nav">
                <h4 style="color:white;">Hızlı Menü</h4>
                <ul style="list-style:none; padding:0;">
                    <li><a href="<?php echo url(); ?>" style="color:#cbd5e0; text-decoration:none;">Ana Sayfa</a></li>
                    <li><a href="<?php echo url(\'#hizmetler\'); ?>" style="color:#cbd5e0; text-decoration:none;">Hizmetlerimiz</a></li>
                    <li><a href="<?php echo page_url(\'blog.php\'); ?>" style="color:#cbd5e0; text-decoration:none;">Blog</a></li>
                    <li><a href="<?php echo url(\'#iletisim\'); ?>" style="color:#cbd5e0; text-decoration:none;">İletişim</a></li>
                </ul>
            </div>

            <div class="footer-services">
                <h4 style="color:white;">Uzmanlıklar</h4>
                <ul style="list-style:none; padding:0;">
                    <li><a href="#" style="color:#cbd5e0; text-decoration:none;">Bireysel Terapi</a></li>
                    <li><a href="#" style="color:#cbd5e0; text-decoration:none;">Çift Terapisi</a></li>
                    <li><a href="#" style="color:#cbd5e0; text-decoration:none;">Çocuk ve Ergen</a></li>
                    <li><a href="#" style="color:#cbd5e0; text-decoration:none;">Online Danışmanlık</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h4 style="color:white;">İletişim</h4>
                <ul style="list-style:none; padding:0;">
                    <li style="color:#cbd5e0; margin-bottom:10px;"><strong style="color:white;">Tel:</strong> <a href="tel:+905511765285" style="color:#cbd5e0; text-decoration:none;">0551 176 52 85</a></li>
                    <li style="color:#cbd5e0; margin-bottom:10px;"><strong style="color:white;">Email:</strong> <a href="mailto:info@erguvanpsikoloji.com" style="color:#cbd5e0; text-decoration:none;">info@erguvanpsikoloji.com</a></li>
                    <li style="color:#cbd5e0;"><strong style="color:white;">Adres:</strong> Şehremini, Millet Cd. 34098 Fatih/İstanbul</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom" style="border-top: 1px solid #4a5568; margin-top: 30px; padding-top: 20px;">
            <p style="color: #a0aec0;">&copy; <?php echo date(\'Y\'); ?> Erguvan Psikoloji. Tüm hakları saklıdır.</p>
        </div>
    </div>
</footer>

<!-- Assets -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="<?php echo asset_url(\'js/script.js\'); ?>" defer></script>

<!-- Floating Buttons -->
<div class="floating-cta">
    <a href="https://wa.me/905511765285" class="cta-btn whatsapp" target="_blank" aria-label="WhatsApp">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="24" height="24" fill="currentColor"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-4-10.5-6.7z"/></svg>
    </a>
    <a href="tel:+905511765285" class="cta-btn phone" aria-label="Arayın">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="22" height="22" fill="currentColor"><path d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.9-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.9 464-464 0-11.2-7.7-20.9-18.6-23.4z"/></svg>
    </a>
</div>

<?php
// Config dosyasını yükle (eğer yüklenmemişse)
if (!defined(\'BASE_URL\')) {
    require_once __DIR__ . \'/../config.php\';
}

// Structured Data (JSON-LD)
$protocol = isset($_SERVER[\'HTTPS\']) && $_SERVER[\'HTTPS\'] === \'on\' ? \'https\' : \'http\';
$host = $_SERVER[\'HTTP_HOST\'];
$site_url = $protocol . \'://\' . $host . BASE_URL;
$current_url = $protocol . \'://\' . $host . $_SERVER[\'REQUEST_URI\'];

// Organization Schema
$organization_schema = [
    "@context" => "https://schema.org",
    "@type" => "ProfessionalService",
    "name" => "Uzm. Psk. Sena Ceren",
    "description" => "Profesyonel psikolojik danışmanlık ve terapi hizmetleri",
    "url" => $site_url,
    "logo" => $site_url . asset_url(\'images/logo.webp\'),
    "image" => $site_url . asset_url(\'images/logo.webp\'),
    "telephone" => "+905511765285",
    "email" => "info@uzmanpsikologsenaceren.com",
    "address" => [
        "@type" => "PostalAddress",
        "streetAddress" => "Şehremini, Millet Cd.",
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
if (isset($page_type) && $page_type === \'article\' && isset($post) && is_array($post) && !empty($post)) {
    // Blog post için Article Schema
    $additional_schema = [
        "@context" => "https://schema.org",
        "@type" => "BlogPosting",
        "headline" => htmlspecialchars($post[\'title\']),
        "description" => htmlspecialchars($post[\'excerpt\']),
        "image" => $post[\'image\'] ?? (file_exists(__DIR__ . \'/../assets/images/logo.webp\') ? $site_url . asset_url(\'images/logo.webp\') : (file_exists(__DIR__ . \'/../assets/images/logo.png\') ? $site_url . asset_url(\'images/logo.png\') : \'\')),
        "datePublished" => date(\'c\', strtotime($post[\'created_at\'])),
        "dateModified" => isset($post[\'updated_at\']) ? date(\'c\', strtotime($post[\'updated_at\'])) : date(\'c\', strtotime($post[\'created_at\'])),
        "author" => [
            "@type" => "Person",
            "name" => "Erguvan Psikoloji"
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => "Erguvan Psikoloji",
            "logo" => [
                "@type" => "ImageObject",
                "url" => (file_exists(__DIR__ . \'/../assets/images/logo.webp\') ? $site_url . asset_url(\'images/logo.webp\') : (file_exists(__DIR__ . \'/../assets/images/logo.png\') ? $site_url . asset_url(\'images/logo.png\') : \'\'))
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

</html>'
];

foreach ($files as $path => $content) {
    // Dizin kontrolü
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // Dosyayı yaz
    if (file_put_contents($path, $content)) {
        echo "✅ Created/Updated: $path<br>";
    } else {
        echo "❌ Failed to write: $path (Permission Denied?)<br>";
    }
}
?>