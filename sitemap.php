<?php
/**
 * Dinamik Sitemap Oluşturucu
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/db.php';

header('Content-Type: application/xml; charset=utf-8');

$site_url = 'https://www.uzmanpsikologsenaceren.com' . BASE_URL;
$db = getDB();

// Ana sayfa
$urls = [
    [
        'loc' => $site_url . '/',
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'daily',
        'priority' => '1.0'
    ],
    [
        'loc' => $site_url . '/pages/blog.php',
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'daily',
        'priority' => '0.8'
    ]
];

// Blog yazıları
try {
    $posts = $db->query("SELECT id, slug, created_at, updated_at FROM blog_posts ORDER BY created_at DESC")->fetchAll();
    foreach ($posts as $post) {
        $lastmod = isset($post['updated_at']) && $post['updated_at'] ? date('Y-m-d', strtotime($post['updated_at'])) : date('Y-m-d', strtotime($post['created_at']));
        $urls[] = [
            'loc' => $site_url . '/blog/' . $post['slug'],
            'lastmod' => $lastmod,
            'changefreq' => 'weekly',
            'priority' => '0.7'
        ];
    }
} catch (Exception $e) {
    // Hata durumunda devam et
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($urls as $url) {
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . "\n";
    echo '    <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
    echo '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
    echo '    <priority>' . $url['priority'] . '</priority>' . "\n";
    echo '  </url>' . "\n";
}

echo '</urlset>';


