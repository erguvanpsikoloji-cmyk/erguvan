<?php
function detect_encoding($file)
{
    if (!file_exists($file))
        return "File not found";
    $content = file_get_contents($file);
    $encodings = ['UTF-8', 'ISO-8859-9', 'Windows-1254', 'ASCII'];
    $detected = mb_detect_encoding($content, $encodings, true);

    // Check for BOM
    $bom = bin2hex(substr($content, 0, 3));
    $has_bom = ($bom === 'efbbbf') ? "With BOM" : "No BOM";

    return "File: $file | Encoding: $detected | $has_bom";
}

echo detect_encoding('index.php') . PHP_EOL;
echo detect_encoding('includes/footer.php') . PHP_EOL;
