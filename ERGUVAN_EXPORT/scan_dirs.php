<?php
echo "=== DIRECTORY SCAN ===\n";
$root = realpath(__DIR__ . '/../../');
echo "ROOT DIR: $root\n";
print_r(scandir($root));

echo "\n=== PARENT DIR ===\n";
$parent = realpath(__DIR__ . '/../');
echo "PARENT DIR: $parent\n";
print_r(scandir($parent));

echo "\n=== CURRENT DIR ===\n";
echo "CURRENT DIR: " . __DIR__ . "\n";
print_r(scandir(__DIR__));
