<?php
// DEBUG: Script started
ini_set('memory_limit', '256M');
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
        echo "<div style='background:red;color:white;padding:20px;z-index:99999;position:fixed;top:0;left:0;width:100%;'>";
        echo "<h3>🛑 FATAL ERROR DETECTED</h3>";
        echo "<p><strong>Message:</strong> " . $error['message'] . "</p>";
        echo "<p><strong>File:</strong> " . $error['file'] . " (" . $error['line'] . ")</p>";
        echo "</div>";
    }
});

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
// require_once __DIR__ . '/../includes/csrf.php'; // DISABLED
require_once __DIR__ . '/../../database/db.php';
// require_once __DIR__ . '/../includes/upload-handler.php'; // DISABLED

// Dummy CSRF
if (!function_exists('csrfField')) {
    function csrfField()
    {
        return '<input type="hidden" name="csrf_token" value="debug_bypass">';
    }
}

$db = getDB();
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents(__DIR__ . '/blog_debug_trace.txt', "POST Started at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

    // Bypass size check for now
    if (false) {
        $error = 'Size Error';
    } else {
        try {
            file_put_contents(__DIR__ . '/blog_debug_trace.txt', "Processing fields (Image SKIPPED)...\n", FILE_APPEND);

            $title = trim($_POST['title'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $excerpt = trim($_POST['excerpt'] ?? '');
            $meta_description = trim($_POST['meta_description'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $reading_time = '';
            $keywords = trim($_POST['keywords'] ?? '');
            $image_alt = trim($_POST['image_alt'] ?? '');
            $instagram_share = isset($_POST['instagram_share']) ? 1 : 0;
            $tags = trim($_POST['tags'] ?? '');
            $toc_data = $_POST['toc_data'] ?? '';
            $faq_data = $_POST['faq_data'] ?? '';

            // Auto-Canonical Logic
            $canonical_url = trim($_POST['canonical_url'] ?? '');
            if (empty($canonical_url) && !empty($slug)) {
                $canonical_url = url('blog/' . $slug);
            }

            // IMAGE LOGIC DISABLED
            $image = 'assets/images/logo2026.png'; // HARDCODED
            file_put_contents(__DIR__ . '/blog_debug_trace.txt', "Image Hardcoded: $image\n", FILE_APPEND);

            $checkStmt = $db->prepare("SELECT id FROM blog_posts WHERE slug = :slug");
            $checkStmt->execute([':slug' => $slug]);
            if ($checkStmt->fetch()) {
                //   throw new Exception('Bu slug zaten kullanılıyor! Lütfen farklı bir slug girin.');
                $slug .= '-' . time(); // Auto-fix slug
            }

            file_put_contents(__DIR__ . '/blog_debug_trace.txt', "Preparing DB Insert...\n", FILE_APPEND);
            $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, meta_description, meta_title, tags, toc_data, faq_data, content, image, image_alt, category, reading_time, keywords, instagram_share, canonical_url, og_title, og_description, schema_type, created_at) 
                                   VALUES (:title, :slug, :excerpt, :meta_description, :meta_title, :tags, :toc_data, :faq_data, :content, :image, :image_alt, :category, :reading_time, :keywords, :instagram_share, :canonical_url, :og_title, :og_description, :schema_type, NOW())");

            file_put_contents(__DIR__ . '/blog_debug_trace.txt', "Executing DB Insert...\n", FILE_APPEND);
            $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':excerpt' => $excerpt,
                ':meta_description' => $meta_description,
                ':meta_title' => $_POST['meta_title'] ?? null,
                ':tags' => $tags,
                ':toc_data' => $toc_data,
                ':faq_data' => $faq_data,
                ':content' => $content,
                ':image' => $image,
                ':image_alt' => $image_alt,
                ':category' => $category,
                ':reading_time' => $reading_time,
                ':keywords' => $keywords,
                ':instagram_share' => $instagram_share,
                ':canonical_url' => $canonical_url,
                ':og_title' => $_POST['og_title'] ?? null,
                ':og_description' => $_POST['og_description'] ?? null,
                ':schema_type' => $_POST['schema_type'] ?? 'BlogPosting'
            ]);
            file_put_contents(__DIR__ . '/blog_debug_trace.txt', "✅ DB Insert Success!\n", FILE_APPEND);

            $success = true;
            // redirect(admin_url('pages/blog.php')); // Disable redirect to see output on CURL
            echo "SUCCESS_POST_CREATED";

        } catch (\Throwable $e) {
            $error = 'Hata: ' . $e->getMessage();
            file_put_contents(__DIR__ . '/blog_debug_trace.txt', "❌ ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
        }
    }
}
// ... (rest of the file is mainly HTML)
?>