<?php
header('Content-Type: text/plain');
$file = __DIR__ . '/database/db.php';
if (file_exists($file)) {
    echo file_get_contents($file);
} else {
    echo "db.php not found at $file";
}
