<?php
echo "=== PREVIEW.PHP CONTENT ===\n";
if (file_exists(__DIR__ . '/preview.php')) {
    echo file_get_contents(__DIR__ . '/preview.php');
} else {
    echo "Files not found.";
}
