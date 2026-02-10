<?php
\ = 'logo_erguvan_final.png';
\ = 'logo_erguvan_final.webp';
if (file_exists(\)) {
    \ = imagecreatefrompng(\);
    if (\) {
        imagepalettetotruecolor(\);
        imagealphablending(\, true);
        imagesavealpha(\, true);
        imagewebp(\, \, 90);
        imagedestroy(\);
        echo 'Conversion successful';
    } else {
        echo 'GD Load Failed';
    }
} else {
    echo 'Source not found';
}
?>
