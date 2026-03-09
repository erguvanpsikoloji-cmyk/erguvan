<?php
$source = 'c:/Users/ceren/Desktop/erguvan son/assets/images/logo_icon.png';
$destination = 'c:/Users/ceren/Desktop/erguvan son/assets/images/logo_icon.webp';

if (file_exists($source)) {
    $image = imagecreatefrompng($source);
    if ($image) {
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
        if (imagewebp($image, $destination, 85)) {
            echo "Success: Created $destination\n";
            echo "Original size: " . filesize($source) . " bytes\n";
            echo "WebP size: " . filesize($destination) . " bytes\n";
        } else {
            echo "Error: Failed to save WebP\n";
        }
        imagedestroy($image);
    } else {
        echo "Error: Failed to load PNG\n";
    }
} else {
    echo "Error: Source not found\n";
}
?>