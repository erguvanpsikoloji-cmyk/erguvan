<?php
$file = 'index.php';
$content = file_get_contents($file);

// Footer include ve yanlış kapatılan yerleri temizle (Regex tırnak sorunu olmadan)
$target = "<?php include 'includes/footer.php'; ?>";
if (strpos($content, $target) !== false) {
    $content = str_replace($target, "", $content);
}

// Dosya sonuna doğru kapanışları ve footer'ı ekle
$footer_fix = "\n</div></div></div></section>\n\n<?php include 'includes/footer.php'; ?>";
file_put_contents($file, trim($content) . $footer_fix);
echo "index.php fixed successfully.";
?>