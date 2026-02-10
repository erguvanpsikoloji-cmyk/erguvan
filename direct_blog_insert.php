<?php
/**
 * DIRECT BLOG INSERT (DEBUG)
 * Bypasses Session, Auth, and CSRF to test core insertion logic
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/database/db.php';

echo "<h2>🧪 Direct Blog Insert Test</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = getDB();
        echo "<p style='color:green'>✅ Database connected</p>";

        $title = $_POST['title'] ?? 'Direct Test ' . time();
        $slug = 'direct-test-' . time();
        $content = $_POST['content'] ?? '<p>Test content</p>';

        // Hardcoded values to minimize points of failure
        $image = 'assets/images/logo2026.png';

        $sql = "INSERT INTO blog_posts (
            title, slug, excerpt, meta_description, meta_title, tags, toc_data, faq_data, 
            content, image, image_alt, category, reading_time, keywords, instagram_share, 
            canonical_url, og_title, og_description, schema_type, created_at
        ) VALUES (
            :title, :slug, :excerpt, :meta_description, :meta_title, :tags, :toc_data, :faq_data, 
            :content, :image, :image_alt, :category, :reading_time, :keywords, :instagram_share, 
            :canonical_url, :og_title, :og_description, :schema_type, NOW()
        )";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':slug' => $slug,
            ':excerpt' => 'Direct Excerpt',
            ':meta_description' => 'Desc',
            ':meta_title' => 'Meta Title',
            ':tags' => 'test',
            ':toc_data' => '',
            ':faq_data' => '',
            ':content' => $content,
            ':image' => $image,
            ':image_alt' => 'Alt',
            ':category' => 'Genel',
            ':reading_time' => '5 dk',
            ':keywords' => 'key',
            ':instagram_share' => 0,
            ':canonical_url' => '',
            ':og_title' => 'OG Title',
            ':og_description' => 'OG Desc',
            ':schema_type' => 'BlogPosting'
        ]);

        echo "<h3 style='color:green'>✅ SUCCESS! Blog post inserted.</h3>";
        echo "<p>ID: " . $db->lastInsertId() . "</p>";

    } catch (Exception $e) {
        echo "<h3 style='color:red'>❌ ERROR: " . $e->getMessage() . "</h3>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo '<form method="POST">
        <input type="text" name="title" value="Direct Test Post">
        <textarea name="content">Content</textarea>
        <button type="submit">Insert Post</button>
    </form>';
}
?>