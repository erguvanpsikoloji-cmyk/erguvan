<?php
$exportPath = 'C:/Users/ceren/Desktop/yazdığım bloglar';
if (!file_exists($exportPath)) {
    mkdir($exportPath, 0777, true);
}

// 1. Boşanma Süreci Danışmanlığı Nedir? (LİTERAL)
$bosanma_full = "## Boşanma Süreci Danışmanlığı Nedir?\n" .
    "Boşanma süreci danışmanlığı, yalnızca evlilik birliğinin sona erdirilmesine eşlik eden bir destek değil; bireyin yaşamında meydana gelen köklü bir değişime uyum sağlayabilmesi için yapılandırılmış bir psikolojik yardım sürecidir. Bu danışmanlık; bireyin duygusal tepkilerini anlaması, kararlarını daha sağlıklı değerlendirmesi ve yeni yaşam düzenini inşa edebilmesi için rehberlik sunar.\n" .
    "Boşanma, çoğu zaman ani bir karar gibi görünse de arka planında uzun süreli çatışmalar, iletişim problemleri, duygusal kopukluklar ve karşılanmamış ihtiyaçlar yer alır. Bu nedenle süreç yalnızca “ayrılık” ile sınırlı değildir; yas, kayıp, öfke ve yeniden yapılanma evrelerini içerir. Uzman psikolog eşliğinde yürütülen boşanma süreci danışmanlığı, bu evrelerin sağlıklı şekilde tamamlanmasını hedefler.\n" .
    "İstanbul psikolog hizmetleri arasında önemli bir yere sahip olan boşanma süreci danışmanlığı; bireysel terapi, çift görüşmeleri ve çocuklara yönelik psikolojik destek uygulamalarını kapsar. Özellikle İstanbul Fatih ve Topkapı gibi yoğun ve hızlı yaşam temposuna sahip bölgelerde yaşayan bireyler için bu destek, duygusal dengeyi koruyabilmek adına kritik bir rol oynar.\n" .
    "Boşanma danışmanlığı, evliliği kurtarmaya yönelik bir terapi değildir. Amaç; evliliğin devam edip edemeyeceğinin gerçekçi biçimde değerlendirilmesi, boşanma kararı kesinleşmişse sürecin ruhsal açıdan en az zararla yönetilmesidir. Uzman psikolog desteği, bireyin kendini daha net tanımasına ve kararlarının sorumluluğunu sağlıklı şekilde alabilmesine yardımcı olur.\n\n" .
    "## Boşanma Kararı ve Psikolojik Destek\n" .
    "Boşanma kararı almak, bireylerin hayatlarında verdikleri en zor kararlardan biridir. Bu karar; suçluluk, öfke, hayal kırıklığı, kayıp ve belirsizlik gibi yoğun duyguları beraberinde getirir. Psikolojik destek alınmadan verilen kararlar, sonrasında pişmanlık ya da daha derin ruhsal sorunlara yol açabilir. Uzman psikolog desteği, boşanma kararının sağlıklı bir zeminde değerlendirilmesine yardımcı olur. Birey; gerçekten boşanmak mı istediğini, yoksa yaşadığı geçici bir kriz nedeniyle mi bu karara yöneldiğini fark edebilir. İstanbul Fatih psikolog ve Topkapı psikolog hizmetleri bu noktada bireylere güvenli bir alan sunar.\nYetişkin psikoloğu ile yapılan görüşmelerde, bireyin geçmiş ilişki örüntüleri, evlilik içindeki iletişim sorunları ve duygusal ihtiyaçları ele alınır. Böylece kişi, boşanma kararını daha bilinçli ve net bir şekilde verebilir.\n\n" .
    "## Boşanma Öncesi ve Sonrası Danışmanlık\n" .
    "Boşanma danışmanlığı yalnızca boşanma anına değil, öncesi ve sonrasına da odaklanır. Boşanma öncesi danışmanlıkta amaç; çiftlerin tüm seçenekleri değerlendirmesini sağlamak, sağlıklı iletişim kurabilmelerine destek olmaktır. Boşanma sonrası danışmanlık ise bireylerin yeni yaşam düzenine uyum sağlamasına yardımcı olur. Bu süreçte sıklıkla yaşanan duygular şunlardır:\n" .
    "- Yalnızlık ve boşluk hissi\n- Özgüven kaybı\n- Gelecek kaygısı\n- Öfke ve suçluluk\n" .
    "Uzman psikolog eşliğinde yürütülen terapi süreci, bireyin bu duygularla baş etmesini kolaylaştırır. İstanbul psikolog arayışında olan bireyler için Fatih ve Topkapı bölgelerinde sunulan hizmetler, ulaşılabilirlik açısından önemli bir avantaj sağlar.\n\n" .
    "## Boşanan Ailelerde Çocuklara Psikolojik Destek\n" .
    "Boşanma sürecinden en çok etkilenenler çoğu zaman çocuklardır. Ebeveynler arasındaki çatışmalar, belirsizlik ve değişen yaşam koşulları çocuklarda ciddi psikolojik etkilere yol açabilir. Bu nedenle boşanan ailelerde çocuklara psikolojik destek hayati öneme sahiptir. Çocuk psikoloğu ve ergen psikoloğu desteği, çocuğun yaşına ve gelişim dönemine uygun şekilde planlanır. Çocuklar boşanmayı sıklıkla kendileriyle ilişkilendirir ve suçluluk hissedebilir. Terapi sürecinde çocuğa, boşanmanın ebeveynler arası bir durum olduğu ve kendi sorumluluğu olmadığı anlatılır. İstanbul Fatih çocuk psikoloğu hizmetleri kapsamında; oyun terapisi, bireysel görüşmeler ve aile görüşmeleri uygulanabilir. Amaç, çocuğun duygularını sağlıklı şekilde ifade edebilmesini sağlamak ve uzun vadeli psikolojik sorunların önüne geçmektir.\n\n" .
    "## Çocuklar İçin Pedagog ve Psikolog Desteği\n" .
    "Boşanma sürecinde pedagog ve psikolog desteği birlikte değerlendirildiğinde daha bütüncül bir yaklaşım sunar. Pedagog, çocuğun eğitim ve gelişim süreçlerini takip ederken; çocuk psikoloğu çocuğun duygusal dünyasına odaklanır. Özellikle okul çağındaki çocuklarda boşanma sonrası şu sorunlar görülebilir:\n" .
    "- Ders başarısında düşüş\n- İçine kapanma veya saldırgan davranışlar\n- Uyku ve yeme sorunları\n" .
    "Bu belirtiler erken dönemde fark edilip destek sağlanmazsa kalıcı hale gelebilir. İstanbul pedagog ve çocuk psikoloğu hizmetleri, bu riskleri azaltmak için önemli bir role sahiptir.\n\n" .
    "## İstanbul Fatih Boşanma Danışmanlığı Hizmetleri\n" .
    "İstanbul Fatih boşanma danışmanlığı hizmetleri, bireylerin ve ailelerin bu süreci profesyonel destekle yürütmesini amaçlar. Fatih ve Topkapı bölgelerinde hizmet veren uzman psikologlar; yetişkin, çocuk ve ergenlerle bireysel ihtiyaçlara uygun çalışmalar yürütür. İstanbul psikolog hizmetlerinin yoğun olduğu bu bölgelerde, danışanlar hem merkezi konum avantajı hem de deneyimli uzmanlarla çalışma fırsatı bulur. Boşanma süreci danışmanlığı; gizlilik, etik ve profesyonellik ilkeleri çerçevesinde yürütülür.\n\n" .
    "## Uzman Psikolog ile Boşanma Süreci\n" .
    "Uzman psikolog ile yürütülen boşanma süreci, bireyin kendini daha iyi tanımasını ve duygusal dayanıklılığını artırmasını sağlar. Terapi sürecinde; bireyin geçmiş ilişkileri, bağlanma stilleri ve baş etme mekanizmaları ele alınır. Yetişkin psikoloğu desteği sayesinde birey, boşanmayı bir yıkım değil; yeni bir başlangıç olarak değerlendirebilir. Bu bakış açısı, kişinin hem kendisiyle hem de çevresiyle daha sağlıklı ilişkiler kurmasına katkı sağlar.\n\n" .
    "## Sonuç\n" .
    "Boşanma süreci; bireyler, çiftler ve çocuklar için duygusal olarak zorlayıcı, karmaşık ve çoğu zaman yıpratıcı bir dönemdir. Bu sürecin profesyonel destekle yürütilmesi, hem karar alma aşamasında hem de boşanma sonrasında ruhsal uyumun sağlanmasında büyük önem taşır. Boşanma süreci danışmanlığı; bireylerin kendilerini daha iyi tanımalarına, duygularını sağlıklı biçimde düzenlemelerine ve yeni yaşamlarına daha dengeli bir şekilde adapte olmalarına yardımcı olur.\n" .
    "Çocukların bu süreçten en az zarar görmesi, ebeveynlerin bilinçli tutumları ve çocuklara yönelik psikolojik destekle mümkündür. Çocuk psikoloğu, ergen psikoloğu ve pedagog desteği sayesinde çocukların yaşadıkları duygusal tepkiler anlaşılır ve uzun vadeli sorunların önüne geçilebilir.\n" .
    "Fatih, Çapa ve İstanbul genelinde boşanma süreci danışmanlığı, bireysel terapi ve çocuklara yönelik psikolojik destek arayışında olan danışanlarımıza; Erguvan Psikoloji bünyesinde alanında uzman psikologlar ile profesyonel, etik ve gizlilik esasına dayalı bir destek sunmaktayız. Bu zorlu süreçte yalnız değilsiniz; ihtiyaç duyduğunuz her aşamada yanınızda olabiliriz.\n\n" .
    "✍️ Uzm. Psk. Sedat Parmaksız Notu: Bu içerik bilgilendirme amaçlıdır ve profesyonel tıbbi tavsiye yerine geçmez.";

// 2. Çocuklar İçin Oyun Terapisi Nedir? (LİTERAL)
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
    "# Oyun Terapisi Türleri\n" .
    "## Çocuk Merkezli Oyun Terapisi\n" .
    "Bu yaklaşımda çocuk, terapi sürecinin merkezindedir. Terapist yönlendirme yapmadan, çocuğun oyunu aracılığıyla kendini ifade etmesine alan tanır. Bu yöntem, çocuğun özgüvenini ve kendini kabul duygusunu güçlendirmeyi hedefler.\n\n" .
    "## Yapılandırılmış Oyun Terapisi\n" .
    "Belirli hedefler doğrultusunda planlanan bu yöntemde terapist, oyunu daha aktif bir şekilde yönlendirir. Duygusal düzenleme, sınır koyma ve davranış geliştirme gibi alanlarda sıklıkla tercih edilir.\n\n" .
    "## Bilişsel Davranışçı Oyun Terapisi\n" .
    "Bu yaklaşımda oyun, çocuğun düşünce-duygu-davranış ilişkisini anlamasına yardımcı olacak şekilde yapılandırılır. Özellikle kaygı temelli sorunlarda etkili bir yöntemdir.\n\n" .
    "# Oyun Terapisi Süreci Nasıl İlerler?\n" .
    "Oyun terapisi süreci, ilk görüşme ile başlar. Bu görüşmede ebeveynlerden ayrıntılı bilgi alınır ve çocuğun gelişimsel öyküsü değerlendirilir. Ardından terapi hedefleri belirlenir. Seanslar genellikle haftada bir kez yapılır ve her seans yaklaşık 45–50 dakika sürer. Terapi sürecinin uzunluğu, çocuğun ihtiyaçlarına göre değişkenlik gösterir. Terapist, seans sırasında çocuğun oyunlarını gözlemler, sembolik ifadelerini analiz eder ve terapi sürecini bilimsel çerçevede yönlendirir.\n\n" .
    "# Oyun Terapisinde Kullanılan Materyaller\n" .
    "Oyun terapisi odasında kullanılan materyaller, çocuğun kendini ifade etmesini kolaylaştıracak şekilde seçilir. Sıklıkla kullanılan materyaller: Figür oyuncaklar, kuklalar, boya ve resim malzemeleri, inşa oyuncakları, hikâye kartları.\n\n" .
    "# Ebeveynlerin Oyun Terapisindeki Rolü\n" .
    "Ebeveynler sürecin önemli bir parçasıdır. Belirli aralıklarla yapılan ebeveyn görüşmeleri sayesinde, çocuğun gelişimi değerlendirilir ve aileye rehberlik sağlanır. Ebeveynlere, çocuğun duygusal ihtiyaçlarını daha iyi anlayabilmeleri için öneriler sunulur.\n\n" .
    "# Oyun Terapisinin Çocuk Gelişimine Katkıları\n" .
    "Çocuk duygularını daha iyi tanır, problem çözme becerileri geliştirir, sosyal ilişkilerde daha rahat olur ve özgüveni artar. Kendini ifade etme becerisi güçlenir.\n\n" .
    "# Sonuç: Çocuğunuzun Duygusal Sağlığı İçin Güvenli Bir Adım\n" .
    "İstanbul Fatih Çapa’da Erguvan Psikoloji olarak; uzman psikolog, çocuk psikoloğu ve pedagog kadromuzla oyun terapisi hizmeti sunmaktayız.\n\n" .
    "✍️ Uzm. Psk. Sedat Parmaksız Notu: Bu içerik bilgilendirme amaçlıdır.";

// 3. Panik Atak (LİTERAL)
$panik_full = "## Panik Atak Belirtileri Nelerdir? Panik Atak Nasıl Geçer?\n" .
    "Günümüzde birçok yetişkin, aniden ortaya çıkan yoğun korku ve bedensel belirtilerle karakterize edilen panik atak nedeniyle yaşam kalitesinde ciddi düşüşler yaşamaktadır. Çoğu kişi ilk panik atağını kalp krizi geçirdiğini düşünerek acil serviste fark eder. Göğüs sıkışması, çarpıntı, nefes alamama hissi ve baş dönmesi gibi belirtiler, kişiyi ciddi bir fiziksel hastalık yaşadığına inandırabilir. Oysa panik atak, doğru yaklaşımla tedavi edilebilir bir kaygı bozukluğudur.\n\n" .
    "## Panik Atak Nedir?\n" .
    "Panik atak; herhangi bir tehlike yokken, aniden başlayan ve kısa sürede zirveye ulaşan yoğun korku, kaygı ve bedensel belirtiler bütünüdür. Atak sırasında kişi kontrolünü kaybedeceğini, bayılacağını, delireceğini ya da öleceğini düşünebilir. Panik ataklar genellikle 10–30 dakika sürer ancak etkisi saatlerce devam edebilir.\n\n" .
    "## Panik Atak Belirtileri Nelerdir?\n" .
    "### Bedensel Panik Atak Belirtileri\n" .
    "- Çarpıntı, kalp atışının hızlanması\n- Göğüs ağrısı veya sıkışma hissi\n- Nefes alamama, boğuluyormuş hissi\n- Terleme, Titreme, Baş dönmesi, sersemlik\n- Mide bulantısı, Ellerde ayaklarda uyuşma\n- Sıcak basması veya üşüme\n\n" .
    "### Psikolojik Panik Atak Belirtileri\n" .
    "- Ölüm korkusu\n- Kontrolü kaybetme korkusu\n- Delirme korkusu\n- Gerçeklikten kopma hissi (derealizasyon)\n- Kendine yabancılaşma (depersonalizasyon)\n\n" .
    "## Panik Atak Neden Olur?\n" .
    "1. Psikolojik Nedenler: Bastırılmış duygular, yoğun stres, travmatik yaşam olayları, mükemmeliyetçilik.\n" .
    "2. Biyolojik Nedenler: Beyindeki serotonin ve noradrenalin dengesizlikleri, genetik yatkınlık.\n" .
    "3. Çevresel Faktörler: Yoğun iş temposu, kafein ve nikotin kullanımı, uykusuzluk.\n\n" .
    "## Panik Atak Tedavisi Nasıl Olur?\n" .
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
    echo "Kaydedildi: $name\n";
}
?>