<?php
$file = 'index.php';
$content = file_get_contents($file);

// Footer include ve yanlış kapatılan yerleri temizle
$content = preg_replace('/<form action="#" method="POST">.*?<\/form>\s*<\?php include \'includes\/footer\.php\'; \?>/s', '', $content);

// Eğer yukarıdaki başarısız olursa manuel temizlik denemesi
if (strpos($content, '<?php include \'includes/footer.php\'; ?>') !== false) {
    $content = str_replace("<?php include 'includes/footer.php'; ?>", "", $content);
}

// Dosya sonuna doğru kapanışları ve footer'ı ekle
$footer_section = '                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include "includes/footer.php"; ?>';

// Son </section> etiketinden sonrasını temizle ve yeni yapıyı ekle
// Not: Bu riskli olabilir, o yüzden daha spesifik bir yaklaşım:
// Formun sonlandığı yerden itibaren düzeltme yapıyoruz.

file_put_contents($file, $content . $footer_section);
echo "index.php fixed and footer moved.";
?>