<?php
$file = 'd:\Erguvan antigravity hosting\index.php';
$content = file_get_contents($file);
// Remove single line comments
$content = preg_replace('/\/\/.*$/m', '', $content);
// Remove multi-line comments
$content = preg_replace('/\/\*.*?\*\//s', '', $content);
// Save to new file
file_put_contents('d:\Erguvan antigravity hosting\index_clean.php', $content);
echo "Cleaned index.php saved to index_clean.php";
?>