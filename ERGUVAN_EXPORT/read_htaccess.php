<?php
header('Content-Type: text/plain');
if (file_exists(__DIR__ . '/.htaccess')) {
    echo file_get_contents(__DIR__ . '/.htaccess');
} else {
    echo ".htaccess not found";
}
