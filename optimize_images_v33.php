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

        // Preserve transparency for PNG
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
    return "Success: Converted $source to $dest (Quality: $quality, MaxWidth: $maxWidth)\n";
}

$images_dir = __DIR__ . '/assets/images/';

// 1. Optimize Office Images (PSI suggested ~700-800px width)
echo "Optimizing Office Images...\n";
$office_path = $images_dir . 'office/';
echo convertToWebP($office_path . 'ofis-1.jpg', $office_path . 'ofis-1.webp', 75, 800);
echo convertToWebP($office_path . 'ofis-2.jpg', $office_path . 'ofis-2.webp', 75, 800);
echo convertToWebP($office_path . 'ofis-3.jpg', $office_path . 'ofis-3.webp', 75, 800);
echo convertToWebP($office_path . 'ofis-4.jpg', $office_path . 'ofis-4.webp', 75, 800);

// 2. Optimize Team Images (PSI suggests better compression, 400px is safe for portraits)
echo "\nOptimizing Team Images...\n";
$team_path = $images_dir . 'team/';
echo convertToWebP($team_path . 'sedat.jpg', $team_path . 'sedat.webp', 70, 400);
echo convertToWebP($team_path . 'sena.jpg', $team_path . 'sena.webp', 70, 400);
echo convertToWebP($team_path . 'ceren.jpg', $team_path . 'ceren.webp', 70, 400);

echo "\nOptimization complete.\n";
?>