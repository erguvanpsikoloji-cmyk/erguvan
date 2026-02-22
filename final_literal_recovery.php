<?php
$exportPath = 'C:/Users/ceren/Desktop/yazdığım bloglar';
if (!file_exists($exportPath)) {
    mkdir($exportPath, 0777, true);
}

// 1. BOŞANMA SÜRECİ DANIŞMANLIĞI NEDİR? (TÜM CHUNKS BİRLEŞTİRİLDİ)
$bosanma_full = "## Boşanma Süreci Danışmanlığı Nedir?\n" .
    "Boşanma süreci danışmanlığı, yalnızca evlilik birliğinin sona erdirilmesine eşlik eden bir destek değil; bireyin yaşamında meydana gelen köklü bir değişime uyum sağlayabilmesi için yapılandırılmış bir psikolojik yardım sürecidir. Bu danışmanlık; bireyin duygusal tepkilerini anlaması, kararlarını daha sağlıklı değerlendirmesi ve yeni yaşam düzenini inşa edebilmesi için rehberlik sunar.\n" .
    "Boşanma, çoğu zaman ani bir karar gibi görünse de arka planında uzun süreli çatışmalar, iletişim problemleri, duygusal kopukluklar ve karşılanmamış ihtiyaçlar yer alır. Bu nedenle süreç yalnızca “ayrılık” ile sınırlı değildir; yas, kayıp, öfke ve yeniden yapılanma evrelerini içerir. Uzman psikolog eşliğinde yürütülen boşanma süreci danışmanlığı, bu evrelerin sağlıklı şekilde tamamlanmasını hedefler.\n" .
    "İstanbul psikolog hizmetleri arasında önemli bir yere sahip olan boşanma süreci danışmanlığı; bireysel terapi, çift görüşmeleri ve çocuklara yönelik psikolojik destek uygulamalarını kapsar. Özellikle İstanbul Fatih ve Topkapı gibi yoğun ve hızlı yaşam temposuna sahip bölgelerde yaşayan bireyler için bu destek, duygusal dengeyi koruyabilmek adına kritik bir rol oynar.\n" .
    "Boşanma danışmanlığı, evliliği kurtarmaya yönelik bir terapi değildir. Amaç; evliliğin devam edip edemeyeceğinin gerçekçi biçimde değerlendirilmesi, boşanma kararı kesinleşmişse sürecin ruhsal açıdan en az zararla yönetilmesidir. Uzman psikolog desteği, bireyin kendini daha net tanımasına ve kararlarının sorumluluğunu sağlıklı şekilde alabilmesine yardımcı olur. Boşanma süreci danışmanlığı, evlilik birliğini sonlandırma aşamasında olan ya da boşanma sonrası yeni bir hayata uyum sağlamaya çalışan birey ve ailelere yönelik psikolojik destek sürecidir. Boşanma yalnızca hukuki bir işlem değil; duygusal, psikolojik ve sosyal boyutları olan çok yönlü bir yaşam krizidir. Bu süreçte alınan profesyonel destek, bireylerin daha sağlıklı kararlar alabilmesini ve süreci daha az yıpratıcı şekilde yönetmesini sağlar.\n" .
    "İstanbul psikolog hizmetleri arasında önemli bir yere sahip olan boşanma süreci danışmanlığı; bireysel terapi, çift danışmanlığı ve çocuk odaklı çalışmalarla yürütülür. Özellikle İstanbul Fatih, Topkapı gibi yoğun yaşam temposuna sahip bölgelerde yaşayan bireyler için bu destek, sürecin sağlıklı ilerlemesi açısından büyük önem taşır.\n" .
    "Boşanma danışmanlığı, evliliği kurtarmaya yönelik bir terapi değildir. Amaç, evliliğin sürdürülüp sürdürülemeyeceğinin sağlıklı bir şekilde değerlendirilmesi, boşanma kararı kesinleşmişse bu sürecin en az zararla atlatılmasıdır. Uzman psikolog eşliğinde yürütülen danışmanlık, duygusal karmaşayı azaltır ve bireyin kendini daha net görmesini sağlar.\n\n" .
    "## Boşanma Kararı ve Psikolojik Destek\n" .
    "Boşanma kararı almak, bireylerin hayatlarında verdikleri en zor kararlardan biridir. Bu karar; suçluluk, öfke, hayal kırıklığı, kayıp ve belirsizlik gibi yoğun duyguları beraberinde getirir. Psikolojik destek alınmadan verilen kararlar, sonrasında pişmanlık ya da daha derin ruhsal sorunlara yol açabilir.\n" .
    "Uzman psikolog desteği, boşanma kararının sağlıklı bir zeminde değerlendirilmesine yardımcı olur. Birey; gerçekten boşanmak mı istediğini, yoksa yaşadığı geçici bir kriz nedeniyle mi bu karara yöneldiğini fark edebilir. İstanbul Fatih psikolog ve Topkapı psikolog hizmetleri bu noktada bireylere güvenli bir alan sunar.\n" .
    "Yetişkin psikoloğu ile yapılan görüşmelerde, bireyin geçmiş ilişki örüntüleri, evlilik içindeki iletişim sorunları ve duygusal ihtiyaçları ele alınır. Böylece kişi, boşanma kararını daha bilinçli ve net bir şekilde verebilir.\n\n" .
    "## Sonuç\n" .
    "Boşanma süreci; bireyler, çiftler ve çocuklar için duygusal olarak zorlayıcı, karmaşık ve çoğu zaman yıpratıcı bir dönemdir. Bu sürecin profesyonel destekle yürütülmesi, hem karar alma aşamasında nem de boşanma sonrasında ruhsal uyumun sağlanmasında büyük önem taşır. Boşanma süreci danışmanlığı; bireylerin kendilerini daha iyi tanımalarına, duygularını sağlıklı biçimde düzenlemelerine ve yeni yaşamlarına daha dengeli bir şekilde adapte olmalarına yardımcı olur.\n" .
    "Çocukların bu süreçten en az zarar görmesi, ebeveynlerin bilinçli tutumları ve çocuklara yönelik psikolojik destekle mümkündür. Çocuk psikoloğu, ergen psikoloğu ve pedagog desteği sayesinde çocukların yaşadıkları duygusal tepkiler anlaşılır ve uzun vadeli sorunların önüne geçilebilir.\n" .
    "Fatih, Çapa ve İstanbul genelinde boşanma süreci danışmanlığı, bireysel terapi ve çocuklara yönelik psikolojik destek arayışında olan danışanlarımıza; Erguvan Psikoloji bünyesinde alanında uzman psikologlar ile profesyonel, etik ve gizlilik esasına dayalı bir destek sunmaktayız. Bu zorlu süreçte yalnız değilsiniz; ihtiyaç duyduğunuz her aşamada yanınızda olabiliriz.\n\n" .
    "✍️ Uzm. Psk. Sedat Parmaksız Notu: Bu içerik bilgilendirme amaçlıdır ve profesyonel tıbbi tavsiye yerine geçmez.";

// 2. ÇOCUKLAR İÇİN OYUN TERAPİSİ NEDİR? (TÜM CHUNKS BİRLEŞTİRİLDİ)
$oyun_full = "# Çocuklar İçin Oyun Terapisi Nedir? Faydaları, Süreci ve Seanslar Hakkında Bilmeniz Gerekenler\n" .
    "Oyun terapisi, çocukların duygularını, düşüncelerini ve yaşantılarını sözel olarak ifade etmekte zorlandıkları durumlarda, oyun aracılığıyla kendilerini güvenli bir şekilde anlatmalarını sağlayan bilimsel bir terapi yöntemidir. Çocuklar için oyun, yalnızca eğlenceli bir aktivite değil; aynı zamanda iç dünyalarını yansıttıkları en doğal iletişim yoludur. Bu nedenle oyun terapisi, çocukların psikolojik ihtiyaçlarını anlamada ve desteklemede etkili bir araç olarak kullanılmaktadır.\n" .
    "Çocuklar çoğu zaman yaşadıkları kaygı, korku, öfke ya da içsel çatışmaları kelimelere dökemezler. Oyun terapisi sürecinde oyuncaklar, figürler, resimler ve semboller aracılığıyla çocukların iç dünyası terapötik bir ortamda görünür hale gelir. Bu süreç, çocuğun kendini anlaşılmış ve güvende hissetmesini sağlar.\n" .
    "İstanbul Fatih Çapa’da hizmet veren Erguvan Psikoloji’de, oyun terapisi süreci uzman psikolog, çocuk psikoloğu ve pedagog eşliğinde bilimsel temellere dayanarak yürütülmektedir.\n\n" .
    "# Oyun Terapisinin Temel Amacı\n" .
    "Oyun terapisinin temel amacı, çocuğun duygusal yüklerini sağlıklı bir şekilde dışa vurmasına yardımcı olmak ve psikolojik iyilik halini desteklemektir. Terapi sürecinde amaç yalnızca problem davranışları ortadan kaldırmak değil; çocuğun duygusal farkındalığını, baş etme becerilerini ve kendilik algısını güçlendirmektir.\n" .
    "Bu süreçte çocuk:\n" .
    "- Duygularını tanımayı ve ifade etmeyi öğrenir\n- Güven duygusunu geliştirir\n- İçsel çatışmalarını düzenlemeye başlar\n- Kendini kontrol etme ve problem çözme becerileri kazanır\n" .
    "Oyun terapisi, çocuğun gelişimsel ihtiyaçlarını göz önünde bulundurarak ilerler ve her çocuk için bireysel olarak yapılandırılır.\n\n" .
    "# Oyun Terapisi Kimler İçin Uygundur?\n" .
    "Oyun terapisi genellikle 2–12 yaş arası çocuklar için uygulanır. Bu yaş aralığı, oyunun çocuğun temel ifade dili olduğu dönemleri kapsar. Ancak her çocuğun gelişimsel özellikleri farklı olduğu için yaş sınırı esnek olarak değerlendirilir.\n" .
    "Oyun terapisi;\n" .
    "- Duygusal zorlanmalar yaşayan\n- Davranışsal problemler gösteren\n- Yaşam değişikliklerine uyum sağlamakta güçlük çeken\n- Aile içi değişimlerden etkilenen\n- Sosyal ilişkilerde zorlanan\n" .
    "çocuklar için etkili bir yöntemdir. Kardeş doğumu, taşınma, okul değişikliği gibi durumlar da oyun terapisi sürecinde ele alınabilir. Fatih bölgesinde çocuk psikoloğu ve pedagog desteği arayan aileler için oyun terapisi, çocuğun ihtiyaçlarına uygun güvenli bir destek alanı sunar.\n\n" .
    "## Çocuk Merkezli Oyun Terapisi\n" .
    "Bu yaklaşımda çocuk, terapi sürecinin merkezindedir. Terapist yönlendirme yapmadan, çocuğun oyunu aracılığıyla kendini ifade etmesine alan tanır. Bu yöntem, çocuğun özgüvenini ve kendini kabul duygusunu güçlendirmeyi hedefler.\n\n" .
    "## Yapılandırılmış Oyun Terapisi\n" .
    "Belirli hedefler doğrultusunda planlanan bu yöntemde terapist, oyunu daha aktif bir şekilde yönlendirir. Duygusal düzenleme, sınır koyma ve davranış geliştirme gibi alanlarda sıklıkla tercih edilir.\n\n" .
    "## Bilişsel Davranışçı Oyun Terapisi\n" .
    "Bu yaklaşımda oyun, çocuğun düşünce-duygu-davranış ilişkisini anlamasına yardımcı olacak şekilde yapılandırılır. Özellikle kaygı temelli sorunlarda etkili bir yöntemdir.\n\n" .
    "# Sonuç: Çocuğunuzun Duygusal Sağlığı İçin Güvenli Bir Adım\n" .
    "Çocuğunuzun duygusal ve davranışsal ihtiyaçlarını erken fark etmek, sağlıklı gelişimin temelidir. Oyun terapisi, çocukların kendilerini ifade etmeleri ve iyileşmeleri için güçlü ve bilimsel bir araçtır.\n" .
    "İstanbul Fatih Çapa’da Erguvan Psikoloji olarak; uzman psikolog, çocuk psikoloğu ve pedagog kadromuzla oyun terapisi hizmeti sunmaktayız. Çocuğunuz için güvenilir, bilimsel ve profesyonel destek almak istiyorsanız bizimle iletişime geçebilirsiniz.\n\n" .
    "✍️ Uzm. Psk. Sedat Parmaksız Notu: Bu içerik bilgilendirme amaçlıdır.";

// 3. PANİK ATAK BELİRTİLERİ NELERDİR? (TÜM CHUNKS BİRLEŞTİRİLDİ)
$panik_full = "## Panik Atak Belirtileri Nelerdir? Panik Atak Nasıl Geçer?\n" .
    "Günümüzde birçok yetişkin, aniden ortaya çıkan yoğun korku ve bedensel belirtilerle karakterize edilen panik atak nedeniyle yaşam kalitesinde ciddi düşüşler yaşamaktadır. Çoğu kişi ilk panik atağını kalp krizi geçirdiğini düşünerek acil serviste fark eder. Göğüs sıkışması, çarpıntı, nefes alamama hissi ve baş dönmesi gibi belirtiler, kişiyi ciddi bir fiziksel hastalık yaşadığına inandırabilir. Oysa panik atak, doğru yaklaşımla tedavi edilebilir bir kaygı bozukluğudur.\n\n" .
    "#### Hızlı Menü:\n" .
    "- Panik Atak Belirtileri Nelerdir? Panik Atak Nasıl Geçer?\n" .
    "- Panik Atak Nedir?\n" .
    "- Panik Atak Belirtileri Nelerdir?\n" .
    "- Bedensel Panik Atak Belirtileri\n" .
    "- Psikolojik Panik Atak Belirtileri\n" .
    "- Panik Atak Neden Olur?\n" .
    "- Psikolojik Nedenler\n" .
    "- Biyolojik Nedenler\n" .
    "- Çevresel ve Yaşam Tarzı Faktörleri\n" .
    "- Panik Atak ile Anksiyete Arasındaki Fark\n" .
    "- Panik Atağı Tetikleyen Düşünce Hataları\n" .
    "- Günlük Hayatta Panik Atağı Azaltan Alışkanlıklar\n" .
    "- Panik Atak Tedavisi Nasıl Olur?\n" .
    "- Psikoterapi ile Panik Atak Tedavisi\n" .
    "- İlaç Tedavisi Gerekli mi?\n" .
    "- Panik Atak Anında Ne Yapmalı?\n" .
    "- Panik Atak Hakkında Sıkça Sorulan Sorular\n" .
    "- Panik Atak İçin Ne Zaman Psikolog Randevu Alınmalı?\n" .
    "- Sonuç\n\n" .
    "## Panik Atak Nedir?\n" .
    "Panik atak; herhangi bir tehlike yokken, aniden başlayan ve kısa sürede zirveye ulaşan yoğun korku, kaygı ve bedensel belirtiler bütünüdür. Atak sırasında kişi kontrolünü kaybedeceğini, bayılacağını, delireceğini ya da öleceğini düşünebilir. Panik ataklar genellikle 10–30 dakika sürer ancak etkisi saatlerce devam edebilir.\n\n" .
    "## Panik Atak Belirtileri Nelerdir?\n" .
    "### Bedensel Panik Atak Belirtileri\n" .
    "- Çarpıntı, kalp atışının hızlanması\n- Göğüs ağrısı veya sıkışma hissi\n- Nefes alamama, boğuluyormuş hissi\n- Terleme, Titreme, Baş dönmesi, sersemlik\n- Mide bulantısı, Ellerde ayaklarda uyuşma\n- Sıcak basması veya üşüme\n\n" .
    "### Psikolojik Panik Atak Belirtileri\n" .
    "- Ölüm korkusu\n- Kontrolü kaybetme korkusu\n- Delirme korkusu\n- Gerçeklikten kopma hissi (derealizasyon)\n- Kendine yabancılaşma (depersonalizasyon)\n\n" .
    "## Panik Atak Neden Olur?\n" .
    "Panik atak tek bir nedene bağlı değildir. Genellikle biyolojik ve psikolojik faktörler birlikte rol oynar.\n\n" .
    "### Psikolojik Nedenler\n" .
    "- Bastırılmış duygular, yoğun stres, travmatik yaşam olayları, mükemmeliyetçilik.\n\n" .
    "### Biyolojik Nedenler\n" .
    "- Beyindeki serotonin ve noradrenalin dengesizlikleri, genetik yatkınlık.\n\n" .
    "### Panik Atak Tedavisi Nasıl Olur?\n" .
    "En etkili yaklaşım Bilişsel Davranışçı Terapi (BDT)’dir. Terapide bedensel belirtilerin zararsız olduğu öğretilir, felaketleştirme düşünceleri ele alınır ve nefes/gevşeme teknikleri öğretilir.\n\n" .
    "## Panik Atak Anında Ne Yapmalı?\n" .
    "Nefesine odaklan, bunun bir panik atak olduğunu hatırla, bedensel belirtilerle savaşma, kaçma davranışını azalt ve dikkatini başka bir şeye yönlendir.\n\n" .
    "✍️ Uzm. Psk. Sedat Parmaksız Notu: Bu içerik psikolojik bilgilendirme amaçlıdır. Tanı ve tedavi yerine geçmez.";

$files = [
    'bosanma-sureci-danismanligi-nedir.txt' => "BAŞLIK: Boşanma Süreci Danışmanlığı Nedir?\n------------------------------------------\n\n" . $bosanma_full,
    'cocuklar-i-cin-oyun-terapisi-nedir.txt' => "BAŞLIK: Çocuklar İçin Oyun Terapisi Nedir?\n------------------------------------------\n\n" . $oyun_full,
    'panik-atak-belirtileri-nelerdir-panik-atak-nasil-gecer.txt' => "BAŞLIK: Panik Atak Belirtileri Nelerdir? Panik Atak Nasıl Geçer?\n------------------------------------------\n\n" . $panik_full
];

foreach ($files as $name => $content) {
    file_put_contents($exportPath . '/' . $name, $content);
    echo "FİNAL METİN KAYDEDİLDİ: $name\n";
}
?>