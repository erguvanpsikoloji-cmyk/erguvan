<?php
set_time_limit(300);
echo "=== SERVER SEARCH ===\n";
$search = "oluyorum";
$it = new RecursiveDirectoryIterator(__DIR__);
foreach (new RecursiveIteratorIterator($it) as $file) {
    if ($file->isDir())
        continue;
    if ($file->getExtension() !== 'php' && $file->getExtension() !== 'html')
        continue;
    $content = file_get_contents($file->getPathname());
    if (stripos($content, $search) !== false) {
        $mtime = date("Y-m-d H:i:s", filemtime($file->getPathname()));
        echo "$mtime - " . $file->getPathname() . "\n";
    }
}
echo "Done.";
