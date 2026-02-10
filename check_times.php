<?php
echo "=== FILE TIMES (v2) ===\n";
function listTimes($dir)
{
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..')
            continue;
        $path = $dir . '/' . $file;
        $mtime = date("Y-m-d H:i:s", filemtime($path));
        $type = is_dir($path) ? '[DIR]' : '[FILE]';
        echo "$mtime - $type - $file\n";
        if (is_dir($path) && ($file === 'ERGUVAN_ARSIV' || $file === 'database')) {
            listTimes($path);
        }
    }
}
listTimes(__DIR__);
