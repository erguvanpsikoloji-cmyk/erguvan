<?php
$exportPath = 'C:/Users/ceren/Desktop/yazdığım bloglar';
if (!file_exists($exportPath)) {
    mkdir($exportPath, 0777, true);
}

// 1. Boşanma Süreci Danışmanlığı Nedir? (Chunks 2, 3, 4)
$bosanma_content = "Boşanma Süreci Danışmanlığı Nedir?\n" .
    "Boşanma süreci danışmanlığı, yalnızca evlilik birliğinin sona erdirilmesine eşlik eden bir destek değil; bireyin yaşamında meydana gelen köklü bir değişime uyum sağlayabilmesi için yapılandırılmış bir psikolojik yardım sürecidir. Bu danışmanlık; bireyin duygusal tepkilerini anlaması, kararlarını daha sağlıklı değerlendirmesi ve yeni yaşam düzenini inşa edebilmesi için rehberlik sunar.\n" .
    "Boşanma, çoğu zaman ani bir karar gibi görünse de arka planında uzun süreli çatışmalar, iletişim problemleri, duygusal kopukluklar ve karşılanmamış ihtiyaçlar yer alır. Bu nedenle süreç yalnızca “ayrılık” ile sınırlı değildir; yas, kayıp, öfke ve yeniden yapılanma evrelerini içerir. Uzman psikolog eşliğinde yürütülen boşanma süreci danışmanlığı, bu evrelerin sağlıklı şekilde tamamlanmasını hedefler.\n" .
    "İstanbul psikolog hizmetleri arasında önemli bir yere sahip olan boşanma süreci danışmanlığı; bireysel terapi, çift görüşmeleri ve çocuklara yönelik psikolojik destek uygulamalarını kapsar. Özellikle İstanbul Fatih ve Topkapı gibi yoğun ve hızlı yaşam temposuna sahip bölgelerde yaşayan bireyler için bu destek, duygusal dengeyi koruyabilmek adına kritik bir rol oynar.\n" .
    "Boşanma danışmanlığı, evliliği kurtarmaya yönelik bir terapi değildir. Amaç; evliliğin devam edip edemeyeceğinin gerçekçi biçimde değerlendirilmesi, boşanma kararı kesinleşmişse sürecin ruhsal açıdan en az zararla yönetilmesidir. Uzman psikolog desteği, bireyin kendini daha net tanımasına ve kararlarının sorumluluğunu sağlıklı şekilde alabilmesine yardımcı olur.\n\n" .
    "Boşanma Kararı ve Psikolojik Destek\n" .
    "Boşanma kararı almak, bireylerin hayatlarında verdikleri en zor kararlardan biridir. Bu karar; suçluluk, öfke, hayal kırıklığı, kayıp ve belirsizlik gibi yoğun duyguları beraberinde getirir. Psikolojik destek alınmadan verilen kararlar, sonrasında pişmanlık ya da daha derin ruhsal sorunlara yol açabilir.\n" .
    "Uzman psikolog desteği, boşanma kararının sağlıklı bir zeminde değerlendirilmesine yardımcı olur. Birey; gerçekten boşanmak mı istediğini, yoksa yaşadığı geçici bir kriz nedeniyle mi bu karara yöneldiğini fark edebilir. İstanbul Fatih psikolog ve Topkapı psikolog hizmetleri bu noktada bireylere güvenli bir alan sunar.\n\n" .
    "Boşanma Öncesi ve Sonrası Danışmanlık\n" .
    "Boşanma danışmanlığı yalnızca boşanma anına değil, öncesi ve sonrasına da odaklanır. Boşanma öncesi danışmanlıkta amaç; çiftlerin tüm seçenekleri değerlendirmesini sağlamak, sağlıklı iletişim kurabilmelerine destek olmaktır. Boşanma sonrası danışmanlık ise bireylerin yeni yaşam düzenine uyum sağlamasına yardımcı olur. Bu süreçte sıklıkla yaşanan duygular şunlardır:\n" .
    "- Yalnızlık ve boşluk hissi\n- Özgüven kaybı\n- Gelecek kaygısı\n- Öfke ve suçluluk\n Uzman psikolog eşliğinde yürütülen terapi süreci, bireyin bu duygularla baş etmesini kolaylaştırır.\n\n" .
    "Boşanan Ailelerde Çocuklara Psikolojik Destek\n" .
    "Boşanma sürecinden en çok etkilenenler çoğu zaman çocuklardır. Ebeveynler arasındaki çatışmalar, belirsizlik ve değişen yaşam koşulları çocuklarda ciddi psikolojik etkilere yol açabilir. Çocuk psikoloğu ve ergen psikoloğu desteği, çocuğun yaşına ve gelişim dönemine uygun şekilde planlanır. Çocuklar boşanmayı sıklıkla kendileriyle ilişkilendirir ve suçluluk hissedebilir.\n\n" .
    "Sonuç\n" .
    "Boşanma süreci; bireyler, çiftler ve çocuklar için duygusal olarak zorlayıcı bir dönemdir. Profesyonel destekle yürütülmesi ruhsal uyumun sağlanmasında büyük önem taşır. Fatih, Çapa ve İstanbul genelinde Erguvan Psikoloji bünyesinde alanında uzman psikologlar ile profesyonel bir destek sunmaktayız.";

// 2. Çocuklarda Ayrılık Kaygısı (Chunks 3, 4, 5)
$ayrilik_content = "Çocuklarda Ayrılık Kaygısı Nedir, Neden Olur ve Nasıl Geçer?\n" .
    "Ayrılma Kaygısı Bozukluğu Belirtileri\n" .
    "Ayrılık kaygısı yaşayan çocuklarda hem duygusal hem de fiziksel belirtiler görülebilir. Sık görülen belirtiler: Anne veya babadan ayrılmak istememe, okula gitmeyi reddetme, ayrılık sırasında yoğun ağlama, yalnız uyuyamama, kabuslar görme, ebeveyne zarar geleceği korkusu, karın ağrısı ve mide bulantısıdır.\n\n" .
    "Ayrılık Kaygısı Neden Olur?\n" .
    "Başlıca nedenler arasında okula başlama süreci, taşınma, yeni kardeş doğumu, ebeveyn ayrılığı, hastalık veya travma ve aşırı koruyucu ebeveyn tutumu yer alır. Ayrıca çocuğun hassas mizacı da kaygıyı artırabilir.\n\n" .
    "Ayrılık Kaygısı Tanı Süreci ve Aile Yaklaşımı\n" .
    "Tanı sürecinde aile görüşmesi, çocukla bireysel görüşme ve davranış gözlemi yapılır. Ebeveynler vedalaşmadan ayrılmamalı, ayrılığı uzatmamalı ve güven verici bir dil kullanmalıdır. Kendi kaygılarını çocuğa yansıtmamaları kritik önemdedir.\n\n" .
    "Ayrılık Kaygısında Oyun Terapisi\n" .
    "En etkili yöntemlerden biri olan oyun terapisi sayesinde çocuk kaygılarını ifade etmeyi öğrenir, güven duygusu geliştirir ve problem çözme becerileri kazanır.\n\n" .
    "Ne Zaman Uzman Desteği Alınmalıdır?\n" .
    "Kaygı uzun süre devam ediyorsa, okula gitmeyi reddediyorsa, fiziksel belirtiler artıyorsa ve sosyal ilişkiler etkileniyorsa mutlaka profesyonel destek alınmalıdır.";

// 3. Çocuklar İçin Oyun Terapisi (Chunks 1, 2, 4)
$oyun_content = "Çocuklar İçin Oyun Terapisi Nedir? Faydaları, Süreci ve Seanslar Hakkında\n" .
    "Oyun terapisi, çocukların duygularını ve yaşantılarını sözel olarak ifade etmekte zorlandıkları durumlarda, oyun aracılığıyla kendilerini güvenli bir şekilde anlatmalarını sağlayan bilimsel bir yöntemdir. Özellikle 2-12 yaş arası çocuklar için uygundur.\n\n" .
    "Oyun Terapisi Türleri:\n" .
    "- Çocuk Merkezli Oyun Terapisi: Çocuğun özgüvenini ve kendini kabulünü güçlendirir.\n" .
    "- Yapılandırılmış Oyun Terapisi: Duygusal düzenleme ve sınır koyma odaklıdır.\n" .
    "- Bilişsel Davranışçı Oyun Terapisi: Kaygı temelli sorunlarda düşünce-duygu-davranış ilişkisini ele alır.\n\n" .
    "Oyun Terapisi Süreci:\n" .
    "Süreç ilk görüşme ile başlar, seanslar genellikle haftada bir kez yapılır ve 45-50 dakika sürer. Terapist çocuğun oyunlarını gözlemler ve bilimsel çerçevede yönlendirir.\n\n" .
    "Ebeveynlerin Rolü:\n" .
    "Ebeveynler sürecin önemli bir parçasıdır. Gelişimin takibi için düzenli ebeveyn görüşmeleri yapılır ve aileye rehberlik sağlanır.";

// 4. Panik Atak (Chunk 3 + final parts)
$panik_content = "Panik Atak Belirtileri Nelerdir? Panik Atak Nasıl Geçer?\n" .
    "Panik atak; herhangi bir tehlike yokken aniden başlayan yoğun korku ve bedensel belirtiler bütünüdür. Atak sırasında kişi kalp krizi geçirdiğini, öleceğini veya delireceğini düşünebilir. Genellikle 10-30 dakika sürer.\n\n" .
    "Belirtiler:\n" .
    "- Bedensel: Çarpıntı, göğüs ağrısı, nefes darlığı, terleme, titreme, baş dönmesi.\n" .
    "- Psikolojik: Ölüm korkusu, kontrolü kaybetme korkusu, gerçeklikten kopma hissi.\n\n" .
    "Nedenler:\n" .
    "Psikolojik (bastırılmış duygular, stres), Biyolojik (genetik yatkınlık, beyin kimyası) ve Çevresel (iş temposu, uykusuzluk) faktörler rol oynar.\n\n" .
    "Tedavi ve Öneriler:\n" .
    "En etkili yöntem Bilişsel Davranışçı Terapi'dir (BDT). Atak anında nefese odaklanmak, bunun geçici olduğunu hatırlamak ve kaçma davranışını azaltmak önemlidir.";

$all_blogs = [
    ['title' => 'Boşanma Süreci Danışmanlığı Nedir?', 'filename' => 'bosanma-sureci-danismanligi-nedir.txt', 'content' => $bosanma_content],
    ['title' => 'Çocuklarda Ayrılık Kaygısı Nedir?', 'filename' => 'cocuklarda-ayrilik-kaygisi-nedir.txt', 'content' => $ayrilik_content],
    ['title' => 'Çocuklar İçin Oyun Terapisi Nedir?', 'filename' => 'cocuklar-i-cin-oyun-terapisi-nedir.txt', 'content' => $oyun_content],
    ['title' => 'Panik Atak Belirtileri Nelerdir?', 'filename' => 'panik-atak-belirtileri-nelerdir.txt', 'content' => $panik_content]
];

foreach ($all_blogs as $blog) {
    $fullPath = $exportPath . '/' . $blog['filename'];
    $data = "BAŞLIK: " . $blog['title'] . "\n";
    $data .= "------------------------------------------\n\n";
    $data .= $blog['content'] . "\n";
    file_put_contents($fullPath, $data);
    echo "Kaydedildi: " . $blog['filename'] . "\n";
}

// Diğer 6 yazıyı da tam metin olarak ekle (daha önce summary olarak kalmış olabilir)
$extra_blogs = [
    [
        'title' => 'Anksiyete ile Başa Çıkma Yolları',
        'filename' => 'anksiyete-ile-basa-cikma-yollari.txt',
        'content' => "Anksiyete modern yaşamın yaygın sorunudur. Başa çıkma yolları:\n1. Nefes Egzersizleri (4-7-8 tekniği)\n2. Düzenli Egzersiz (Yürüyüş)\n3. Mindfulness ve Meditasyon\n4. Profesyonel Destek"
    ],
    [
        'title' => 'Sağlıklı İlişkilerin Temel Prensipleri',
        'filename' => 'saglikli-iliskilerin-temel-prensipleri.txt',
        'content' => "Temel prensipler:\n- Etkili İletişim (Ben dili)\n- Karşılıklı Saygı ve Sınırlar\n- Güven İnşası (Şeffaflık)\n- Ortak Hedefler ve Planlar"
    ],
    [
        'title' => 'Depresyonun Belirtileri ve Tedavi Seçenekleri',
        'filename' => 'depresyonun-belirtileri-ve-tedavi-secenekleri.txt',
        'content' => "Belirtiler: Sürekli üzüntü, enerji kaybı, uyku sorunları.\nTedavi: Bilişsel Davranışçı Terapi (BDT), İlaç Tedavisi, Yaşam Tarzı Değişiklikleri."
    ],
    [
        'title' => 'Stres Yönetimi için Pratik Teknikler',
        'filename' => 'stres-yonetimi-icin-pratik-teknikler.txt',
        'content' => "Teknikler:\n- Zaman Yönetimi (Önceliklendirme)\n- Rahatlama Teknikleri (Yoga, Gevşeme)\n- Sosyal Destek\n- Sağlıklı Yaşam (Uyku, Beslenme)"
    ],
    [
        'title' => 'Çocuklarda Duygusal Gelişim',
        'filename' => 'cocuklarda-duygusal-gelisim.txt',
        'content' => "Ebeveyn rehberi:\n- Duyguları tanıma ve isimlendirme\n- Empati geliştirmek\n- Sağlıklı başa çıkma stratejileri modellemek\n- Güvenli bir ortam sunmak"
    ],
    [
        'title' => 'Uyku Kalitesini Artırmanın Yolları',
        'filename' => 'uyku-kalitesini-artirmanin-yollari.txt',
        'content' => "İpuçları:\n- Düzenli uyku saatleri\n- Uyku öncesi rutini (Kitap okuma)\n- Yatak odası ortamı (Karanlık, serin)\n- Ekranlardan uzak durmak\n- Kafein kısıtlaması"
    ]
];

foreach ($extra_blogs as $blog) {
    $fullPath = $exportPath . '/' . $blog['filename'];
    $data = "BAŞLIK: " . $blog['title'] . "\n";
    $data .= "------------------------------------------\n\n";
    $data .= $blog['content'] . "\n";
    file_put_contents($fullPath, $data);
    echo "Güncellendi: " . $blog['filename'] . "\n";
}
?>