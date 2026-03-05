<?php
$path = 'index.php';
$content = file_get_contents($path);

// 1. Yanlış yerdeki footer inclusion'ı temizle
$footer_tag = "<?php include 'includes/footer.php'; ?>";
$content = str_replace($target, "", $content); // Önceki değişken adını düzeltelim
$content = str_replace("<?php include 'includes/footer.php'; ?>", "", $content);

// 2. Gereksiz veya yarım kalan kısımları temizle (form sonrasını hedef al)
// Formun bittiği ve footer'ın yanlışlıkla eklendiği yeri bulup temizleyelim.
$content = preg_replace('/<\/form>\s*<\?php include \'includes\/footer\.php\'; \?>/s', '</form>', $content);

// 3. Dosyanın en sonuna doğru kapanışları ve footer'ı ekle
$footer_section = "\n                    </form>\n                </div>\n            </div>\n        </div>\n    </section>\n\n    <?php include 'includes/footer.php'; ?>";

// Eğer form zaten kapanmışsa, sadece divleri ve footer'ı ekleyelim
// Ama en güvenlisi dosyayı bir noktadan kesip yeniden kurmak.
// 'Randevu Talebi Gönder</button>' ifadesinden sonrasını temizleyelim.
$marker = 'Randevu Talebi Gönder</button>';
$parts = explode($marker, $content);
$new_content = $parts[0] . $marker . $footer_section;

file_put_contents($path, $new_content);
echo "index.php fixed.";
?>
