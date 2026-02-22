<?php
$exportPath = 'C:/Users/ceren/Desktop/yazdığım bloglar';
if (!file_exists($exportPath)) {
    mkdir($exportPath, 0777, true);
}

// BLOG 1: Boşanma Süreci Danışmanlığı Nedir?
$blog1_content = "BAŞLIK: Boşanma Süreci Danışmanlığı Nedir?\n" .
    "------------------------------------------\n\n" .
    "Boşanma süreci danışmanlığı, yalnızca evlilik birliğinin sona erdirilmesine eşlik eden bir destek değil; bireyin yaşamında meydana gelen köklü bir değişime uyum sağlayabilmesi için yapılandırılmış bir psikolojik yardım sürecidir. Bu danışmanlık; bireyin duygusal tepkilerini anlaması, kararlarını daha sağlıklı değerlendirmesi ve yeni yaşam düzenini inşa edebilmesi için rehberlik sunar.\n\n" .
    "Boşanma, çoğu zaman ani bir karar gibi görünse de arka planında uzun süreli çatışmalar, iletişim problemleri, duygusal kopukluklar ve karşılanmamış ihtiyaçlar yer alır. Bu nedenle süreç yalnızca “ayrılık” ile sınırlı değildir; yas, kayıp, öfke ve yeniden yapılanma evrelerini içerir. Uzman psikolog eşliğinde yürütülen boşanma süreci danışmanlığı, bu evrelerin sağlıklı şekilde tamamlanmasını hedefler.\n\n" .
    "İstanbul psikolog hizmetleri arasında önemli bir yere sahip olan boşanma süreci danışmanlığı; bireysel terapi, çift görüşmeleri ve çocuklara yönelik psikolojik destek uygulamalarını kapsar. Özellikle İstanbul Fatih ve Topkapı gibi yoğun ve hızlı yaşam temposuna sahip bölgelerde yaşayan bireyler için bu destek, duygusal dengeyi koruyabilmek adına kritik bir rol oynar.\n\n" .
    "Boşanma danışmanlığı, evliliği kurtarmaya yönelik bir terapi değildir. Amaç; evliliğin devam edip edemeyeceğinin gerçekçi biçimde değerlendirilmesi, boşanma kararı kesinleşmişse sürecin ruhsal açıdan en az zararla yönetilmesidir. Uzman psikolog desteği, bireyin kendini daha net tanımasına ve kararlarının sorumluluğunu sağlıklı şekilde alabilmesine yardımcı olur.\n\n" .
    "## Boşanma Kararı ve Psikolojik Destek\n" .
    "Boşanma kararı almak, bireylerin hayatlarında verdikleri en zor kararlardan biridir. Bu karar; suçluluk, öfke, hayal kırıklığı, kayıp ve belirsizlik gibi yoğun duyguları beraberinde getirir. Psikolojik destek alınmadan verilen kararlar, sonrasında pişmanlık ya da daha derin ruhsal sorunlara yol açabilir. Uzman psikolog desteği, boşanma kararının sağlıklı bir zeminde değerlendirilmesine yardımcı olur.\n\n" .
    "## Boşanma Öncesi ve Sonrası Danışmanlık\n" .
    "Boşanma danışmanlığı yalnızca boşanma anına değil, öncesi ve sonrasına da odaklanır. Boşanma öncesi danışmanlıkta amaç; çiftlerin tüm seçenekleri değerlendirmesini sağlamak, sağlıklı iletişim kurabilmelerine destek olmaktır. Boşanma sonrası danışmanlık ise bireylerin yeni yaşam düzenine uyum sağlamasına yardımcı olur.\n\n" .
    "## Boşanan Ailelerde Çocuklara Psikolojik Destek\n" .
    "Boşanma sürecinden en çok etkilenenler çoğu zaman çocuklardır. Ebeveynler arasındaki çatışmalar, belirsizlik ve değişen yaşam koşulları çocuklarda ciddi psikolojik etkilere yol açabilir. Bu nedenle boşanan ailelerde çocuklara psikolojik destek hayati öneme sahiptir. Çocuk psikoloğu ve ergen psikoloğu desteği, çocuğun yaşına ve gelişim dönemine uygun şekilde planlanır.\n\n" .
    "## Çocuklar İçin Pedagog ve Psikolog Desteği\n" .
    "Boşanma sürecinde pedagog ve psikolog desteği birlikte değerlendirildiğinde daha bütüncül bir yaklaşım sunar. Pedagog, çocuğun eğitim ve gelişim süreclerini takip ederken; çocuk psikoloğu çocuğun duygusal dünyasına odaklanır. Özellikle okul çağındaki çocuklarda boşanma sonrası şu sorunlar görülebilir: Ders başarısında düşüş, içine kapanma veya saldırgan davranışlar, uyku ve yeme sorunları.\n\n" .
    "## Sonuç\n" .
    "Boşanma süreci profesyonel destekle yürütülmesi gereken, hem karar alma aşamasında hem de sonrasında ruhsal uyumun sağlanması için kritik bir dönemdir. Fatih, Çapa ve İstanbul genelinde Erguvan Psikoloji bünyesinde alanında uzman psikologlar ile profesyonel, etik ve gizlilik esasına dayalı bir destek sunmaktayız.\n\n" .
    "✍️ Uzm. Psk. Sedat Parmaksız Notu: Bu içerik bilgilendirme amaçlıdır ve profesyonel tıbbi tavsiye yerine geçmez. Detaylı bilgi ve randevu için iletişime geçebilirsiniz.";

// BLOG 2: Çocuklarda Ayrılık Kaygısı Nedir, Neden Olur ve Nasıl Geçer?
$blog2_content = "BAŞLIK: Çocuklarda Ayrılık Kaygısı Nedir, Neden Olur ve Nasıl Geçer?\n" .
    "------------------------------------------\n\n" .
    "## Ayrılma Kaygısı Bozukluğu Belirtileri\n" .
    "Ayrılık kaygısı yaşayan çocuklarda hem duygusal hem de fiziksel belirtiler görülebilir. Sık görülen belirtiler:\n" .
    "- Anne veya babadan ayrılmak istememe\n- Okula gitmeyi reddetme\n- Ayrılık sırasında yoğun ağlama\n- Yalnız uyuyamama, kabuslar görme\n- Anne babaya zarar geleceği korkusu\n- Karın ağrısı, mide bulantısı gibi fiziksel şikayetler\n\n" .
    "## Ayrılık Kaygısı Neden Olur?\n" .
    "Başlıca nedenler arasında okula başlama süreci, taşınma, yeni kardeş doğumu, ebeveyn ayrılığı, hastalık veya travma ve aşırı koruyucu ebeveyn tutumu yer alır. Ayrıca çocuğun mizacı da önemli bir etkendir.\n\n" .
    "## Ayrılık Kaygısı Hangi Yaşlarda Görülür?\n" .
    "Genellikle 8 ay civarında başlar ve 2–3 yaş arasında belirginleşir. Ancak 5–6 yaş sonrasında yoğun şekilde devam ediyorsa dikkat edilmelidir. Ergenlik döneminde ise aileye aşırı bağımlı davranışlar şeklinde görülebilir.\n\n" .
    "## Ayrılık Kaygısı Çocuğu Nasıl Etkiler?\n" .
    "Okul başarısında düşüş, sosyal ortamlardan kaçınma, özgüven eksikliği ve uyku problemleri gibi etkilere yol açabilir.\n\n" .
    "## Ayrılık Kaygısında Oyun Terapisi\n" .
    "Oyun terapisi sayesinde çocuk kaygılarını ifade etmeyi öğrenir, güven duygusu geliştirir ve problem çözme becerileri kazanır. Çocuklar yaşadıkları duyguları kelimelerle ifade etmekte zorlandıkları için oyun terapisi en etkili yöntemdir.\n\n" .
    "## Ayrılık Kaygısı Tanı Süreci ve Aile Tutumu\n" .
    "Tanı sürecinde aile görüşmesi ve davranış gözlemi yapılır. Aileler vedalaşmadan ayrılmamalı, ayrılığı uzatmamalı ve güven verici bir dil kullanmalıdır. Kendi kaygılarını çocuğa yansıtmamaya dikkat etmelidirler.\n\n" .
    "## Ayrılık Kaygısı Ne Kadar Sürer?\n" .
    "Sakin ve güven veren bir yaklaşım sergilemek, çocuğun kaygıyla baş etme becerisini güçlendirir. Belirtiler haftalar ya da aylar boyunca devam ediyorsa profesyonel olarak değerlendirilmelidir.\n\n" .
    "## Sonuç\n" .
    "Ayrılık kaygısının erken fark edilmesi ve doğru şekilde ele alınması, ilerleyen yıllarda oluşabilecek okul kaygısı ve özgüven sorunlarının önlenmesi açısından hayati önem taşır. Fatih'teki Erguvan Psikolojik Danışmanlık Merkezi'nde oyun terapisi desteğiyle bu süreci sağlıklı yönetebilirisiniz.\n\n" .
    "✍️ Uzm. Psk. Sena Ceren Notu: Bu içerik bilgilendirme amaçlıdır.";

// BLOG 3: Çocuklar İçin Oyun Terapisi Nedir?
$blog3_content = "BAŞLIK: Çocuklar İçin Oyun Terapisi Nedir? Faydaları, Süreci ve Seanslar Hakkında Bilmeniz Gerekenler\n" .
    "------------------------------------------\n\n" .
    "Oyun terapisi, çocukların duygularını, düşüncelerini ve yaşantılarını sözel olarak ifade etmekte zorlandıkları durumlarda, oyun aracılığıyla kendilerini güvenli bir şekilde anlatmalarını sağlayan bilimsel bir terapi yöntemidir. Çocuklar için oyun, iç dünyalarını yansıttıkları en doğal iletişim yoludur.\n\n" .
    "## Oyun Terapisinin Temel Amacı\n" .
    "Duygusal yükleri dışa vurmak, özgüveni artırmak, problem çözme becerileri kazandırmak ve duygusal farkındalığı güçlendirmektir.\n\n" .
    "## Oyun Terapisi Kimler İçin Uygundur?\n" .
    "Genellikle 2–12 yaş arası çocuklar için uygulanır. Duygusal zorlanmalar, davranışsal problemler, aile içi değişimler (kardeş doğumu vb.) ve sosyal ilişki sorunları yaşayan çocuklar için etkilidir.\n\n" .
    "## Oyun Terapisi Türleri\n" .
    "- Çocuk Merkezli Oyun Terapisi: Çocuğun kendini kabul duygusunu güçlendirir.\n" .
    "- Yapılandırılmış Oyun Terapisi: Sınır koyma ve davranış geliştirme odaklıdır.\n" .
    "- Bilişsel Davranışçı Oyun Terapisi: Kaygı temelli sorunlarda etkindir.\n\n" .
    "## Oyun Terapisi Süreci ve Materyalleri\n" .
    "Seanslar genellikle haftada bir kez 45-50 dakika sürer. Odada figür oyuncaklar, kuklalar, resim malzemeleri gibi terapötik materyaller bulunur.\n\n" .
    "## Ebeveynlerin Rolü\n" .
    "Ebeveynlere çocuğun gelişimi için rehberlik sağlanır. Ev ortamında destekleyici tutum sergilemek terapinin etkisini artırır.\n\n" .
    "Sonuç: Çocuğunuzun duygusal sağlığı için oyun terapisi güçlü ve bilimsel bir araçtır. İstanbul Fatih'te profesyonel destek için bizimle iletişime geçebilirsiniz.";

// BLOG 4: Panik Atak Belirtileri Nelerdir?
$blog4_content = "BAŞLIK: Panik Atak Belirtileri Nelerdir? Panik Atak Nasıl Geçer?\n" .
    "------------------------------------------\n\n" .
    "Panik atak; herhangi bir tehlike yokken aniden başlayan, kısa sürede zirveye ulaşan yoğun korku ve bedensel belirtiler bütünüdür. Ataklar genellikle 10–30 dakika sürer.\n\n" .
    "## Panik Atak Belirtileri\n" .
    "- Bedensel: Çarpıntı, göğüs ağrısı, nefes alamama, terleme, titreme, baş dönmesi, mide bulantısı, uyuşma.\n" .
    "- Psikolojik: Ölüm korkusu, kontrolü kaybetme korkusu, delirme korkusu, gerçeklikten kopma hissi.\n\n" .
    "## Panik Atak Neden Olur?\n" .
    "- Psikolojik: Bastırılmış duygular, yoğun stres, travmalar.\n" .
    "- Biyolojik: Beyin kimyası dengesizlikleri, genetik yatkınlık.\n" .
    "- Çevresel: Yoğun iş temposu, uykusuzluk, kafein kullanımı.\n\n" .
    "## Panik Atak ile Anksiyete Arasındaki Fark\n" .
    "Anksiyete daha uzun süreli ve düşük yoğunlukluyken, panik atak ani başlayan ve çok yoğun belirtilerle seyreden bir durumdur.\n\n" .
    "## Tedavi Yöntemleri\n" .
    "En etkili yöntem Bilişsel Davranışçı Terapi (BDT)'dir. Terapide bedensel belirtilerin zararsız olduğu öğretilir ve kaygı yönetimi becerileri geliştirilir.\n\n" .
    "## Atak Anında Ne Yapmalı?\n" .
    "Nefesine odaklan, bunun bir panik atak olduğunu hatırla, bedensel belirtilerle savaşma ve dikkatini başka bir yöne ver.\n\n" .
    "✍️ Uzm. Psk. Sedat Parmaksız Notu: Bu içerik bilgilendirme amaçlıdır. Profesyonel destek için randevu alabilirsiniz.";

$files = [
    'bosanma-sureci-danismanligi-nedir.txt' => $blog1_content,
    'cocuklarda-ayrilik-kaygisi-nedir.txt' => $blog2_content,
    'cocuklar-i-cin-oyun-terapisi-nedir.txt' => $blog3_content,
    'panik-atak-belirtileri-nelerdir.txt' => $blog4_content
];

foreach ($files as $name => $content) {
    file_put_contents($exportPath . '/' . $name, $content);
    echo "Kaydedildi: $name\n";
}

// Diğer 6 yazıyı da tam metin olarak kaydet
$extra_blogs = [
    'anksiyete-ile-basa-cikma-yollari.txt' => "BAŞLIK: Anksiyete ile Başa Çıkma Yolları\n------------------------------------------\nAnksiyete modern yaşamın yaygın sorunudur. Başa çıkma yolları:\n1. Nefes Egzersizleri (4-7-8 tekniği)\n2. Düzenli Egzersiz (Yürüyüş)\n3. Mindfulness ve Meditasyon\n4. Profesyonel Destek",
    'saglikli-iliskilerin-temel-prensipleri.txt' => "BAŞLIK: Sağlıklı İlişkilerin Temel Prensipleri\n------------------------------------------\nTemel prensipler:\n- Etkili İletişim (Ben dili)\n- Karşılıklı Saygı ve Sınırlar\n- Güven İnşası (Şeffaflık)\n- Ortak Hedefler ve Planlar",
    'depresyonun-belirtileri-ve-tedavi-secenekleri.txt' => "BAŞLIK: Depresyonun Belirtileri ve Tedavi Seçenekleri\n------------------------------------------\nBelirtiler: Sürekli üzüntü, enerji kaybı, uyku sorunları.\nTedavi: Bilişsel Davranışçı Terapi (BDT), İlaç Tedavisi, Yaşam Tarzı Değişiklikleri.",
    'stres-yonetimi-icin-pratik-teknikler.txt' => "BAŞLIK: Stres Yönetimi için Pratik Teknikler\n------------------------------------------\nTeknikler:\n- Zaman Yönetimi (Önceliklendirme)\n- Rahatlama Teknikleri (Yoga, Gevşeme)\n- Sosyal Destek\n- Sağlıklı Yaşam (Uyku, Beslenme)",
    'cocuklarda-duygusal-gelisim.txt' => "BAŞLIK: Çocuklarda Duygusal Gelişim\n------------------------------------------\nEbeveyn rehberi:\n- Duyguları tanıma ve isimlendirme\n- Empati geliştirmek\n- Sağlıklı başa çıkma stratejileri modellemek\n- Güvenli bir ortam sunmak",
    'uyku-kalitesini-artirmanin-yollari.txt' => "BAŞLIK: Uyku Kalitesini Artirmanin Yollari\n------------------------------------------\nİpuçları:\n- Düzenli uyku saatleri\n- Uyku öncesi rutini (Kitap okuma)\n- Yatak odası ortamı (Karanlık, serin)\n- Ekranlardan uzak durmak\n- Kafein kısıtlaması"
];

foreach ($extra_blogs as $name => $content) {
    file_put_contents($exportPath . '/' . $name, $content);
    echo "Kaydedildi: $name\n";
}
?>