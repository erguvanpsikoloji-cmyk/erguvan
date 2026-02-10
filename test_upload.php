<?php
/**
 * SIMPLE UPLOAD TEST
 * Tests basic file upload capability without frameworks or complex logic
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>📤 Upload Test</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Upload Max Filesize: " . ini_get('upload_max_filesize') . "</p>";
echo "<p>Post Max Size: " . ini_get('post_max_size') . "</p>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>Processing Upload...</h3>";

    if (isset($_FILES['test_file'])) {
        $file = $_FILES['test_file'];

        echo "<pre>";
        print_r($file);
        echo "</pre>";

        if ($file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/admin/assets/images/blog/';
            // Ensure dir exists
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
                echo "<p>Directory created: $uploadDir</p>";
            }

            $dest = $uploadDir . 'test_' . time() . '_' . basename($file['name']);

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                echo "<p style='color:green'>✅ File successfully moved to: $dest</p>";
                echo "<p>Size: " . filesize($dest) . " bytes</p>";
            } else {
                echo "<p style='color:red'>❌ move_uploaded_file FAILED</p>";
                echo "<p>Last Error: " . print_r(error_get_last(), true) . "</p>";
            }
        } else {
            echo "<p style='color:red'>❌ Upload Error Code: " . $file['error'] . "</p>";
        }
    } else {
        echo "<p style='color:red'>❌ No file received in POST.</p>";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="test_file" required>
    <button type="submit">Upload Test File</button>
</form>