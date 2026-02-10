<?php
/**
 * DATABASE UPDATE - ADD MISSING BLOG COLUMNS
 * Fixes HTTP 500 error in blog-add.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/database/db.php';

echo "<h2>🔧 Blog Posts Table Update</h2>";
echo "<style>body{font-family:Arial;padding:20px;} .ok{color:green;} .error{color:red;}</style>";

try {
    $db = getDB();
    echo "<p class='ok'>✅ Database connected</p>";

    // Check current columns
    $stmt = $db->query("SHOW COLUMNS FROM blog_posts");
    $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<h3>Current Columns:</h3>";
    echo "<ul>";
    foreach ($existingColumns as $col) {
        echo "<li>$col</li>";
    }
    echo "</ul>";

    // Columns needed for blog-add.php
    $neededColumns = [
        'og_title' => "VARCHAR(255) DEFAULT NULL",
        'og_description' => "TEXT DEFAULT NULL",
        'schema_type' => "VARCHAR(50) DEFAULT 'BlogPosting'",
        'meta_title' => "VARCHAR(255) DEFAULT NULL",
        'tags' => "TEXT DEFAULT NULL",
        'toc_data' => "TEXT DEFAULT NULL",
        'faq_data' => "TEXT DEFAULT NULL"
    ];

    echo "<h3>Adding Missing Columns:</h3>";
    $added = 0;

    foreach ($neededColumns as $columnName => $columnDef) {
        if (!in_array($columnName, $existingColumns)) {
            try {
                $sql = "ALTER TABLE blog_posts ADD COLUMN $columnName $columnDef";
                $db->exec($sql);
                echo "<p class='ok'>✅ Added: $columnName</p>";
                $added++;
            } catch (Exception $e) {
                echo "<p class='error'>❌ Failed to add $columnName: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p>ℹ️ Already exists: $columnName</p>";
        }
    }

    if ($added > 0) {
        echo "<h3 class='ok'>✅ Success! Added $added new columns.</h3>";
    } else {
        echo "<h3>ℹ️ All columns already exist.</h3>";
    }

    echo "<p><strong>Blog ekleme artık çalışmalı!</strong></p>";

} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}
?>