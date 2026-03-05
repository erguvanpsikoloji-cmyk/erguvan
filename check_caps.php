<?php
echo "PHP Version: " . phpversion() . "\n";
echo "GD Support: " . (extension_loaded('gd') ? 'Yes' : 'No') . "\n";
if (extension_loaded('gd')) {
    $gd_info = gd_info();
    echo "WebP Support (GD): " . ($gd_info['WebP Support'] ? 'Yes' : 'No') . "\n";
}
echo "Imagick Support: " . (extension_loaded('imagick') ? 'Yes' : 'No') . "\n";
if (extension_loaded('imagick')) {
    $im = new Imagick();
    $formats = $im->queryFormats('WEBP');
    echo "WebP Support (Imagick): " . (in_array('WEBP', $formats) ? 'Yes' : 'No') . "\n";
}
?>