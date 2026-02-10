<?php
/**
 * TEST IMAGE PROCESSING
 * Checks if GD library can process images without crashing
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '256M');

echo "<h2>🖼️ Image Processing Test</h2>";

// Check GD
if (extension_loaded('gd')) {
    echo "<p style='color:green'>✅ GD Library is LOADED</p>";
    $gdInfo = gd_info();
    echo "<pre>" . print_r($gdInfo, true) . "</pre>";
} else {
    echo "<p style='color:red'>❌ GD Library is NOT LOADED</p>";
    exit;
}

// Create a test image from scratch
try {
    echo "<h3>1. Creating Test Image...</h3>";
    $im = imagecreatetruecolor(1200, 800);
    $text_color = imagecolorallocate($im, 233, 14, 91);
    imagestring($im, 5, 50, 50, 'Test Image', $text_color);
    echo "<p style='color:green'>✅ Image created in memory</p>";

    // Save as WEBP
    $tempFile = __DIR__ . '/test_image.webp';
    imagewebp($im, $tempFile, 80);
    echo "<p style='color:green'>✅ Image saved as WEBP</p>";

    // Check file
    if (file_exists($tempFile)) {
        echo "<p>File size: " . filesize($tempFile) . " bytes</p>";
        echo "<img src='test_image.webp' style='width:300px; border:1px solid #ccc'>";
        // Cleanup
        // unlink($tempFile);
    }

    imagedestroy($im);

} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
?>