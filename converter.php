<?php
function convertToWebP($source, $dest, $quality = 80, $maxWidth = null)
{
    if (!file_exists($source))
        return "Error: $source not found\n";
    $info = getimagesize($source);
    if (!$info)
        return "Error: Could not read $source\n";

    $mime = $info['mime'];
    if ($mime == 'image/jpeg')
        $img = imagecreatefromjpeg($source);
    elseif ($mime == 'image/png')
        $img = imagecreatefrompng($source);
    else
        return "Error: Unsupported format $mime\n";

    if ($maxWidth && $info[0] > $maxWidth) {
        $width = $maxWidth;
        $height = round($info[1] * ($maxWidth / $info[0]));
        $newImg = imagecreatetruecolor($width, $height);
        if ($mime == 'image/png') {
            imagealphablending($newImg, false);
            imagesavealpha($newImg, true);
        }
        imagecopyresampled($newImg, $img, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
        imagedestroy($img);
        $img = $newImg;
    }

    imagewebp($img, $dest, $quality);
    imagedestroy($img);
    return "Success: Converted $source to $dest\n";
}

$base = __DIR__ . '/assets/images/';

echo convertToWebP($base . 'team/sedat.jpg', $base . 'team/sedat.webp', 80, 400);
echo convertToWebP($base . 'team/sena.jpg', $base . 'team/sena.webp', 80, 400);
echo convertToWebP($base . 'hero-psikolojik-destek-opt.jpg', $base . 'hero-psikolojik-destek-opt.webp', 75, 1200);
echo convertToWebP($base . 'hero-psikolojik-destek-opt.jpg', $base . 'hero-psikolojik-destek-mobile.webp', 75, 600);
echo convertToWebP($base . 'felt.png', $base . 'felt.webp', 20); // Texture quality can be low
echo convertToWebP($base . 'logo_icon.png', $base . 'logo_icon.webp', 85);

echo "Conversion complete.\n";
?>