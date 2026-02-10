<?php
/**
 * TEST DATABASE INSERTION
 * Isolates the SQL part of blog-add.php to see if that causes the 500 error
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/database/db.php';

echo "<h2>🧪 Testing Blog Post Insertion</h2>";

try {
    $db = getDB();
    echo "<p style='color:green'>✅ Database connected</p>";

    // Dummy data
    $title = "Test Blog Post " . date('Y-m-d H:i:s');
    $slug = "test-blog-post-" . time();
    $content = "<p>This is a test post content.</p>";
    $excerpt = "Test excerpt";
    $category = "Genel";

    // SQL from blog-add.php (simplified to match structure)
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

    $params = [
        ':title' => $title,
        ':slug' => $slug,
        ':excerpt' => $excerpt,
        ':meta_description' => 'Test description',
        ':meta_title' => 'Test Meta Title',
        ':tags' => 'test, debug',
        ':toc_data' => '',
        ':faq_data' => '',
        ':content' => $content,
        ':image' => 'assets/images/logo2026.png', // Use existing image
        ':image_alt' => 'Test image',
        ':category' => $category,
        ':reading_time' => '5 dk',
        ':keywords' => 'test',
        ':instagram_share' => 0,
        ':canonical_url' => '',
        ':og_title' => 'Test OG Title',
        ':og_description' => 'Test OG Description',
        ':schema_type' => 'BlogPosting'
    ];

    echo "<h3>Executing INSERT...</h3>";
    $stmt->execute($params);

    echo "<p style='color:green; font-weight:bold'>✅ INSERT SUCCESSFUL!</p>";
    echo "<p>Last Insert ID: " . $db->lastInsertId() . "</p>";

    // Cleanup - delete the test post
    $id = $db->lastInsertId();
    $db->exec("DELETE FROM blog_posts WHERE id = $id");
    echo "<p>Test post deleted.</p>";

} catch (PDOException $e) {
    echo "<p style='color:red; font-weight:bold'>❌ SQL ERROR:</p>";
    echo "<pre>" . $e->getMessage() . "</pre>";
} catch (Exception $e) {
    echo "<p style='color:red; font-weight:bold'>❌ GENERAL ERROR:</p>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>