<?php
echo "=== LIVE FILE LIST ===\n";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file === '.' || $file === '..')
        continue;
    $type = is_dir($file) ? '[DIR]' : '[FILE]';
    $size = is_file($file) ? filesize($file) : '-';
    echo "$type $file ($size bytes)\n";
}
echo "\n=== SERVER INFO ===\n";
echo "SERVER_NAME: " . $_SERVER['SERVER_NAME'] . "\n";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
