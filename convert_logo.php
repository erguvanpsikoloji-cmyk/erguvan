<?php
$source = 'd:\Erguvan antigravity hosting\assets\images\logo-v38.png';
$destination = 'd:\Erguvan antigravity hosting\assets\images\logo.webp';

if (file_exists($source)) {
    $image = imagecreatefrompng($source);
    if ($image) {
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
        imagewebp($image, $destination, 90);
        imagedestroy($image);
        echo "Converted to WebP: $destination";
    } else {
        echo "Failed to load PNG";
    }
} else {
    echo "Source not found: $source";
}
?>