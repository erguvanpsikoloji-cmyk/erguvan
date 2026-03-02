<?php
$source = "c:/Users/ceren/Desktop/erguvan son/assets/images/hero-psikolojik-destek.jpg";
$destination = "c:/Users/ceren/Desktop/erguvan son/assets/images/hero-psikolojik-destek.webp";

if (file_exists($source)) {
    $img = imagecreatefromjpeg($source);
    if ($img) {
        imagewebp($img, $destination, 80);
        imagedestroy($img);
        echo "Successfully converted to WebP: $destination\n";
        echo "Source size: " . filesize($source) . "\n";
        echo "WebP size: " . filesize($destination) . "\n";
    } else {
        echo "Failed to load source image.\n";
    }
} else {
    echo "Source image not found.\n";
}
?>