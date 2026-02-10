<?php
/**
 * ERGUVAN HEALER V2
 * Uses Inline SVGs to ensure WhatsApp and Call icons are ALWAYS visible.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$indexPath = __DIR__ . '/index.php';
if (!file_exists($indexPath)) {
    die("Hata: index.php bulunamadı.");
}

$content = file_get_contents($indexPath);

// Definitive SVG + HTML Structure
$whatsappSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="white" viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3 18.7-68.1-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-5.5-2.8-23.2-8.5-44.2-27.1-16.4-14.6-27.4-32.6-30.6-38.1-3.2-5.6-.3-8.6 2.5-11.4 2.5-2.5 5.5-6.5 8.3-9.7 2.8-3.2 3.7-5.5 5.5-9.3 1.9-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 13.2 5.8 23.5 9.2 31.5 11.8 13.3 4.2 25.4 3.6 35 2.2 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>';
$phoneSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white" viewBox="0 0 512 512"><path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg>';

$newActionsHtml = '
    <!-- Sabit Yüzer Butonlar (SVG Version) -->
    <div class="floating-actions" style="position: fixed; bottom: 25px; right: 25px; display: flex; flex-direction: column; gap: 15px; z-index: 999999;">
        <a href="https://wa.me/905511765285" target="_blank" title="WhatsApp" style="width: 60px; height: 60px; border-radius: 50%; background: #25D366; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(0,0,0,0.3); transition: transform 0.3s ease;">
            ' . $whatsappSvg . '
        </a>
        <a href="tel:+905511765285" title="Arama" style="width: 60px; height: 60px; border-radius: 50%; background: #0F172A; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(0,0,0,0.3); transition: transform 0.3s ease;">
            ' . $phoneSvg . '
        </a>
    </div>';

// Cleanup: Remove ANY existing floating-actions or floating-container
$content = preg_replace('/<div class="floating-actions".*?<\/div>/s', '', $content);
$content = preg_replace('/<div class="floating-container">.*?<\/div>/s', '', $content);

// Ensure we don't have residual styles that hide it
$content = str_replace('</body>', $newActionsHtml . "\n</body>", $content);

if (file_put_contents($indexPath, $content)) {
    echo "<h3>Başarılı! İkonlar SVG olarak güncellendi.</h3>";
} else {
    echo "<h3>Hata: Yazma izni sorunu.</h3>";
}
?>