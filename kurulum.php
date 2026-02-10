<?php
/**
 * HAKKIMIZDA TOGGLE OTOMATİK KURULUM
 * 
 * Bu dosyayı çalıştırın, her şey otomatik olacak!
 */

// Güvenlik
$key = isset($_GET['key']) ? $_GET['key'] : '';
if ($key !== 'toggle2024') {
    die('Hata: Yetkisiz erişim! URL: kurulum.php?key=toggle2024');
}

?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Hakkımızda Toggle Kurulum</title>
    <style>
        body {
            font-family: Arial;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }

        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #ec4899;
        }

        .success {
            background: #d1fae5;
            padding: 15px;
            border-left: 4px solid #10b981;
            margin: 10px 0;
        }

        .error {
            background: #fee2e2;
            padding: 15px;
            border-left: 4px solid #ef4444;
            margin: 10px 0;
        }

        .step {
            background: #f1f5f9;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }

        code {
            background: #e2e8f0;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <div class="box">
        <h1>🚀 Hakkımızda Toggle Otomatik Kurulum</h1>

        <?php
        $base = __DIR__;
        $errors = [];
        $success = [];

        // CSS içeriği
        $css_addition = "
/* Hakkımızda Toggle Fix */
.about-intro {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--text-dark);
    margin-bottom: 1.5rem;
    transition: opacity 0.3s ease, max-height 0.3s ease;
}

.about-intro.hidden {
    display: none;
}

.about-read-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.75rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
}

.about-read-more-btn .btn-icon {
    transition: transform 0.3s ease;
}

.about-read-more-btn.expanded .btn-icon {
    transform: rotate(180deg);
}

.about-read-more-btn:hover {
    transform: translateY(-2px);
}

.about-full-content {
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transition: max-height 0.5s ease-out, opacity 0.4s ease-out, padding 0.3s ease;
    padding-top: 0;
}

.about-full-content.show {
    max-height: 2000px;
    opacity: 1;
    padding-top: 1rem;
}

.about-full-content .about-description {
    margin-bottom: 1rem;
}
";

        // JavaScript içeriği
        $js_addition = "
    // Hakkımızda - Devamını Oku butonu
    const aboutReadMoreBtn = document.getElementById('aboutReadMoreBtn');
    const aboutFullContent = document.getElementById('aboutFullContent');
    const aboutIntro = document.querySelector('.about-intro');
    
    if (aboutReadMoreBtn && aboutFullContent && aboutIntro) {
        aboutReadMoreBtn.addEventListener('click', function () {
            const isExpanded = aboutFullContent.classList.toggle('show');
            this.classList.toggle('expanded');
            
            // Kısa metni gizle/göster
            aboutIntro.classList.toggle('hidden');
            
            // Buton metnini değiştir
            const btnText = this.querySelector('.btn-text');
            if (btnText) {
                btnText.textContent = isExpanded ? 'Daha Az Göster' : 'Devamını Oku';
            }
            
            // Erişilebilirlik için aria attribute güncellemeleri
            this.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
            aboutFullContent.setAttribute('aria-hidden', isExpanded ? 'false' : 'true');
        });
    }
";

        echo "<h2>📋 Kurulum Başlatılıyor...</h2>";

        // 1. CSS dosyasını güncelle
        $css_file = $base . '/assets/css/style.css';
        if (file_exists($css_file)) {
            $css_content = file_get_contents($css_file);

            // Eğer zaten eklenmemişse ekle
            if (strpos($css_content, 'Hakkımızda Toggle Fix') === false) {
                $css_content .= "\n" . $css_addition;

                if (file_put_contents($css_file, $css_content)) {
                    $success[] = "✅ style.css güncellendi";
                } else {
                    $errors[] = "❌ style.css yazılamadı";
                }
            } else {
                $success[] = "✅ style.css zaten güncel";
            }
        } else {
            $errors[] = "❌ style.css bulunamadı: $css_file";
        }

        // 2. JavaScript dosyasını güncelle
        $js_file = $base . '/assets/js/script.js';
        if (file_exists($js_file)) {
            $js_content = file_get_contents($js_file);

            // Eski toggle kodunu bul ve değiştir
            if (strpos($js_content, 'aboutReadMoreBtn') !== false) {
                // Eski kodu bul
                $pattern = '/\/\/ Hakkımızda - Devamını Oku butonu.*?}\s*}\s*}\);/s';

                if (preg_match($pattern, $js_content)) {
                    $js_content = preg_replace($pattern, $js_addition . "\n});", $js_content);

                    if (file_put_contents($js_file, $js_content)) {
                        $success[] = "✅ script.js güncellendi";
                    } else {
                        $errors[] = "❌ script.js yazılamadı";
                    }
                } else {
                    $success[] = "✅ script.js zaten güncel";
                }
            } else {
                $errors[] = "❌ script.js'de toggle kodu bulunamadı";
            }
        } else {
            $errors[] = "❌ script.js bulunamadı: $js_file";
        }

        // Sonuçları göster
        echo "<h2>📊 Kurulum Raporu</h2>";

        if (!empty($success)) {
            foreach ($success as $msg) {
                echo "<div class='success'>$msg</div>";
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $msg) {
                echo "<div class='error'>$msg</div>";
            }
        }

        if (empty($errors)) {
            echo "<div class='success'>";
            echo "<h3>🎉 Kurulum Tamamlandı!</h3>";
            echo "<p><strong>Şimdi yapmanız gerekenler:</strong></p>";
            echo "<ol>";
            echo "<li>Tarayıcınızda <strong>CTRL + F5</strong> yapın (önbelleği temizle)</li>";
            echo "<li><a href='https://www.erguvanpsikoloji.com' target='_blank'>Ana sayfayı</a> açın</li>";
            echo "<li>'Hakkımızda' bölümüne gidin</li>";
            echo "<li>'Devamını Oku' butonunu test edin</li>";
            echo "<li><strong>ÖNEMLİ:</strong> Bu dosyayı silin: <code>kurulum.php</code></li>";
            echo "</ol>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<h3>⚠️ Bazı Hatalar Oluştu</h3>";
            echo "<p>Lütfen dosya izinlerini kontrol edin veya manuel kurulum yapın.</p>";
            echo "</div>";
        }
        ?>
    </div>
</body>

</html>
