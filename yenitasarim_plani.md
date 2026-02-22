# Hizmet Kartları Estetik Geliştirme Planı (Revize: Sade & Güven Veren)

Bu plan, web sitesindeki hizmet kartlarını psikoterapi merkezinin ruhuna uygun, daha sakin, akademik ve güven veren bir görünüme kavuşturmayı hedeflemektedir.

## Önerilen Yeni Tasarım (Minimalist Yaklaşım)

### 1. Görsel Geliştirmeler (CSS)
- **Sade Zemin:** Karmaşık cam (glassmorphism) efektleri kaldırılarak, yerine çok hafif krem veya kirli beyaz (`#fcfafc`) düz zeminler kullanılacak.
- **Zarif Çerçeveler:** Kartların etrafında sadece hover durumunda belirginleşen, çok ince ve zarif pastel çizgiler yer alacak.
- **Akademik İkonlar:** İkonlar gradyanlı daireler yerine, doğrudan markanın kurumsal renklerinde (Lacivert veya Erguvan) sade ve net şekilde konumlandırılacak.
- **Tipografi Önceliği:** Başlıklar için serif (Playfair Display) yazı tipi kullanılarak akademik ağırlık artırılacak, metin aralıkları (line-height) ferahlatılacak.

### 2. Etkileşim Tasarımı
- **Hafif Derinlik:** Kartlar hover durumunda yukarı sıçramak yerine, sadece hafif bir gölge derinliği kazanacak.
- **Sade Geçişler:** Renk değişimleri ve gölge efektleri çok yavaş ve göz yormayan (0.6s duration) geçişlerle yapılacak.

## Uygulanacak Dosyalar
- [index.php](file:///C:/Users/ceren/Desktop/erguvan%20son/index.php)
- [home.php](file:///C:/Users/ceren/Desktop/erguvan%20son/home.php)

## Doğrulama Planı
1. Yerel olarak dosyaların çıktısı kontrol edilecek.
2. Tasarımın mobil uyumluluğu (farklı ekran boyutları) test edilecek.
3. Değişiklikler canlı siteye (FTP) yüklenerek son onay alınacak.
