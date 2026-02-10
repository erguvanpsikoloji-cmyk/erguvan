<?php
/**
 * CSS Minifier & Optimizer
 */

$cssFile = __DIR__ . '/assets/css/style.css';
$minFile = __DIR__ . '/assets/css/style.min.css';

if (!file_exists($cssFile)) {
    die("Error: style.css not found at $cssFile");
}

$css = file_get_contents($cssFile);

// Basic Minification
$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css); // Remove comments
$css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css); // Remove newlines and tabs
$css = preg_replace('/ {2,}/', ' ', $css); // Remove multiple spaces
$css = str_replace([' {', '{ '], '{', $css);
$css = str_replace([' }', '} ', ';}', '; }'], '}', $css);
$css = str_replace([': ', ' :'], ':', $css);
$css = str_replace([', ', ' ,'], ',', $css);
$css = str_replace(['; ', ' ;'], ';', $css);

if (file_put_contents($minFile, $css)) {
    chmod($minFile, 0644);
    echo "Success: style.min.css created and minified. (" . strlen($css) . " bytes)";
} else {
    echo "Error: Could not write to $minFile";
}
?>