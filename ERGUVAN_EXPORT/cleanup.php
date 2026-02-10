<?php
$files = [
    'search_server.php',
    'read_preview.php',
    'check_times.php',
    'read_content.php',
    'read_post_content.php',
    'test_db_sena.php',
    'unzip_archive.php',
    'unzip_debug.php',
    'unzip_deploy.php',
    'unzip_status.php',
    'read_db_php.php',
    'pi.php',
    'debug_env.php',
    'cleanup.php'
];

$it = new RecursiveDirectoryIterator(__DIR__);
foreach (new RecursiveIteratorIterator($it) as $file) {
    if ($file->isDir())
        continue;
    $filename = $file->getFilename();
    if (in_array($filename, $files)) {
        unlink($file->getPathname());
        echo "Deleted: " . $file->getPathname() . "\n";
    }
}
echo "Cleanup complete.";
