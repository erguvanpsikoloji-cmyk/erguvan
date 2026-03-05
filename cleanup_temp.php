<?php
$files = ['check_caps.php', 'converter.php'];
foreach ($files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        unlink(__DIR__ . '/' . $file);
        echo "Deleted: $file\n";
    }
}
?>