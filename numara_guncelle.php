<?php
/**
 * PHONE NUMBER UPDATE SCRIPT
 * Updates all occurrences of the phone number in index.php.
 */

$indexPath = __DIR__ . '/index.php';
if (!file_exists($indexPath)) {
    die("Hata: index.php bulunamadı.");
}

$content = file_get_contents($indexPath);

// Target variations (old and potentially wrong ones)
$search = [
    '0551 176 52 84',
    '0551 176 52 85',
    '05511765284',
    '+905511765284'
];

// Normalized new number
$formatDisplay = '0551 176 52 85';
$formatLink = '05511765285';

// Update display numbers
$content = str_replace($search[0], $formatDisplay, $content);

// Update link numbers (tel:)
$content = str_replace('tel:05511765284', 'tel:' . $formatLink, $content);
$content = str_replace('tel:+905511765284', 'tel:+90' . $formatLink, $content);
$content = str_replace('wa.me/905511765284', 'wa.me/90' . $formatLink, $content);

if (file_put_contents($indexPath, $content)) {
    echo "<h3>Başarılı! Telefon numarası güncellendi: $formatDisplay</h3>";
} else {
    echo "<h3>Hata: Yazma izni sorunu.</h3>";
}
?>