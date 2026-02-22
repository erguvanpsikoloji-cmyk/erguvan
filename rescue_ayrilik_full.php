<?php
$exportPath = 'C:/Users/ceren/Desktop/yazdığım bloglar';
if (!file_exists($exportPath)) {
    mkdir($exportPath, 0777, true);
}

// AYRILIK KAYGISI TAM METİN (Chunks 3, 4, 5)
$ayrilik_full = "## Ayrılma Kaygısı Bozukluğu Belirtileri\n" .
    "Ayrılık kaygısı yaşayan çocuklarda hem duygusal hem de fiziksel belirtiler görülebilir. Bu belirtiler çoğunlukla ayrılık anında ortaya çıkar ancak bazı çocuklarda ayrılık düşüncesi bile kaygının artmasına neden olabilir.\n" .
    "Sık görülen belirtiler:\n" .
    "- Anne veya babadan ayrılmak istememe\n" .
    "- Okula gitmeyi reddetme\n" .
    "- Ayrılık sırasında yoğun ağlama\n" .
    "- Yalnız uyuyamama\n" .
    "- Kabuslar görme\n" .
    "- Anne babaya zarar geleceği korkusu\n" .
    "- Karın ağrısı, mide bulantısı gibi fiziksel şikayetler\n" .
    "Bazı çocuklar ayrılık anlarında öfke nöbetleri yaşayabilir veya ebeveynlerine sıkı sıkıya sarılabilir. Bu davranışlar çoğu zaman çocuğun inatçılığından değil, kaygı düzeyinin yüksek olmasından kaynaklanır. Bu nedenle ebeveynlerin bu belirtileri doğru yorumlaması önemlidir.\n" .
    "Bu belirtiler birkaç haftadan uzun sürüyorsa bir child psikoloğu veya uzman psikolog tarafından değerlendirilmesi önerilir. Erken dönemde destek almak, sorunun kronikleşmesini önler.\n\n" .
    "## Ayrılık Kaygısı Neden Olur?\n" .
    "Ayrılık kaygısı tek bir nedene bağlı değildir. Genellikle birden fazla faktörün bir araya gelmesiyle ortaya çıkar.\n" .
    "Başlıca nedenler:\n" .
    "- Okula başlama süreci\n" .
    "- Taşınma veya okul değişikliği\n" .
    "- Yeni kardeş doğumu\n" .
    "- Ebeveyn ayrılığı\n" .
    "- Hastalık veya travma\n" .
    "- Aşırı koruyucu ebeveyn tutumu\n" .
    "Bunlara ek olarak çocuğun mizacı da önemli bir etkendir. Daha hassas ve kaygıya yatkın çocuklar ayrılık durumlarına daha yoğun tepkiler verebilir. Aile içindeki stres, ebeveynlerin kaygılı tutumları veya çocuğun kendini güvende hissetmemesi de ayrılık kaygısını artırabilir.\n" .
    "Bazı çocuklar doğuştan daha hassas bir mizaca sahip olabilir. Böyle durumlarda bir pedagog veya psikolog tarafından yapılan değerlendirme oldukça faydalı olabilir.\n\n" .
    "## Ayrılık Kaygısı Hangi Yaşlarda Görülür?\n" .
    "Ayrılık kaygısı genellikle 8 ay civarında başlar ve 2–3 yaş arasında belirginleşir. Bu dönem gelişimsel olarak normal kabul edilir. Bu süreçte çocuk, bağ kurduğu kişiden ayrıldığında huzursuzluk yaşayabilir ve bu durum sağlıklı bağlanmanın bir göstergesi olarak değerlendirilebilir.\n" .
    "Ancak 5–6 yaş sonrasında yoğun şekilde devam ediyorsa dikkat edilmelidir. İlkokul çağında görülen ayrılık kaygısı, çocuğun okul uyumunu ve akademik başarısını etkileyebilir. Bu noktada bir çocuk psikoloğu desteği sürecin sağlıklı ilerlemesine yardımcı olur.\n" .
    "Ergenlik döneminde görülen ayrılık kaygısı ise farklı şekillerde ortaya çıkabilir. Bu yaş grubunda çocuklar fiziksel olarak ayrılabiliyor olsa bile yoğun endişe yaşayabilir ve aileye aşırı bağımlı davranışlar gösterebilir.\n\n" .
    "## Ayrılık Kaygısı Çocuğu Nasıl Etkiler?\n" .
    "Ayrılık kaygısı sadece ayrılık anında yaşanan bir sorun değildir. Uzun vadede çocuğun gelişimini etkileyebilir.\n" .
    "Olası etkiler:\n" .
    "- Okul başarısında düşüş\n" .
    "- Sosyal ortamlardan kaçınma\n" .
    "- Özgüven eksikliği\n" .
    "- Uyku problemleri\n" .
    "- Duygusal hassasiyet\n" .
    "Kaygı uzun süre devam ettiğinde çocuk yeni ortamlara girmekte zorlanabilir, arkadaş ilişkilerinde çekingen davranabilir ve bağımsızlık becerileri yeterince gelişmeyebilir. Bu durum ilerleyen yıllarda sosyal kaygı ve özgüven sorunlarına zemin hazırlayabilir.\n" .
    "Bu nedenle ayrılık kaygısının erken fark edilmesi ve uygun destekle ele alınması önemlidir.\n\n" .
    "## Ayrılık Kaygısında Oyun Terapisi\n" .
    "Ayrılık kaygısının tedavisinde en etkili yöntemlerden biri oyun terapisidir. Oyun terapisi, çocukların duygularını oyun yoluyla ifade etmelerini sağlayan bilimsel bir yöntemdir.\n" .
    "Oyun terapisi sayesinde çocuk:\n" .
    "- Kaygılarını ifade etmeyi öğrenir\n" .
    "- Güven duygusu geliştirir\n" .
    "- Ayrılık durumlarına daha sağlıklı tepki verir\n" .
    "- Problem çözme becerileri kazanır\n" .
    "Çocuklar çoğu zaman yaşadıkları duyguları kelimelerle ifade etmekte zorlanır. Oyun terapisi, çocuğun iç dünyasını anlamaya yardımcı olur ve güvenli bir ortamda duygularını dışa vurmasını sağlar.\n" .
    "Bu süreç genellikle bir çocuk psikoloğu veya uzman psikolog tarafından yürütülür. Gerektiğinde pedagog desteği de sürece dahil edilir.\n\n" .
    "## Ayrılık Kaygısı Tanı Süreci\n" .
    "Tanı sürecinde yalnızca belirtiler değil, çocuğun yaşam koşulları ve aile ilişkileri de değerlendirilir. Ayrılık kaygısının doğru şekilde anlaşılması için kapsamlı bir değerlendirme yapılması gerekir.\n" .
    "Değerlendirme süreci:\n" .
    "- Aile görüşmesi\n" .
    "- Çocukla bireysel görüşme\n" .
    "- Davranış gözlemi\n" .
    "- Gerekli testler\n" .
    "Bu süreç bir psikolog tarafından yürütülür ve çocuğa en uygun destek planı oluşturulur.\n\n" .
    "## Aileler Ayrılık Kaygısında Nasıl Davranmalı?\n" .
    "Ebeveynlerin yaklaşımı ayrılık kaygısının azalmasında büyük rol oynar. Yanlış tutumlar kaygıyı artırabilirken doğru yaklaşım çocuğun güven duygusunu güçlendirir.\n" .
    "Öneriler:\n" .
    "- Vedalaşmadan ayrılmayın\n" .
    "- Ayrılığı uzatmayın\n" .
    "- Çocuğu suçlamayın\n" .
    "- Güven verici bir dil kullanın\n" .
    "- Rutinler oluşturun\n" .
    "Ayrıca ebeveynlerin kendi kaygılarını çocuğa yansıtmamaları önemlidir. Çocuklar ebeveynlerinin duygularını kolaylıkla hisseder ve bu durum kaygının artmasına neden olabilir.\n" .
    "Aile danışmanlığı sürecinde bir pedagog ve uzman psikolog aileye rehberlik edebilir.\n\n" .
    "## Okula Başlama Döneminde Ayrılık Kaygısı\n" .
    "Okula başlama süreci ayrılık kaygısının en sık görüldüğü dönemlerden biridir. Yeni ortam, yeni kurallar ve ebeveynden uzak kalma düşüncesi çocuğun kaygısını artırabilir.\n" .
    "Bu dönemde:\n" .
    "- Okul öncesinde sınıfı tanıtmak\n" .
    "- Öğretmenle güven ilişkisi kurmak\n" .
    "- Sabah rutinlerini düzenlemek\n" .
    "- Okul hakkında olumlu konuşmak\n" .
    "uyum sürecini kolaylaştırır. Okula alışma sürecinde çocuğa zaman tanımak ve sabırlı olmak oldukça önemlidir.\n\n" .
    "## Ayrılık Kaygısı Ne Kadar Sürer?\n" .
    "Ayrılık kaygısının süresi çocuğun yaşına, mizaç özelliklerine, daha önce yaşadığı deneyimlere ve ebeveyn tutumlarına bağlı olarak değişebilir. Gelişimsel olarak bakıldığında, bebeklik döneminde 8–18 ay arasında görülen ayrılık kaygısı çoğu çocukta doğal bir süreçtir ve zamanla kendiliğinden azalır. Okul öncesi dönemde ise yeni bir ortama başlama, anaokulu süreci veya bakım veren kişinin değişmesi gibi durumlar ayrılık kaygısının yeniden ortaya çıkmasına neden olabilir. Bu tür durumlarda kaygı genellikle birkaç hafta içinde hafifleyerek azalır.\n" .
    "Ancak bazı çocuklarda ayrılık kaygısı daha uzun sürebilir. Özellikle çocuğun okula gitmeyi sürekli reddetmesi, ebeveynden ayrılırken yoğun panik yaşaması, yalnız kalmaktan aşırı korkması veya uykuya dalarken bile ebeveyne ihtiyaç duyması gibi belirtiler haftalar ya da aylar boyunca devam ediyorsa bu durum profesyonel olarak değerlendirilmelidir. Çünkü uzayan ayrılık kaygısı, çocuğun sosyal gelişimini, arkadaş ilişkilerini ve akademik uyumunu olumsuz etkileyebilir.\n" .
    "Ayrılık kaygısının süresini etkileyen en önemli faktörlerden biri ebeveyn tutumudur. Vedalaşmaların uzatılması, çocuğun kaygısını azaltmak amacıyla sürekli taviz verilmesi ya da okula gitmemesine izin verilmesi kısa vadede rahatlama sağlasa da uzun vadede kaygının sürmesine neden olabilir. Buna karşılık tutarlı, sakin ve güven veren bir yaklaşım sergilemek, çocuğun kaygıyla baş etme becerisini güçlendirir ve sürecin daha kısa sürmesine yardımcı olur.\n" .
    "Bazı durumlarda ayrılık kaygısının altında başka etkenler de bulunabilir. Taşınma, aile içi stres, kardeş doğumu, hastalık deneyimleri veya travmatik yaşantılar çocuğun güven duygusunu zedeleyerek kaygının daha uzun sürmesine yol açabilir. Bu gibi durumlarda bir psikolog, uzman psikolog, pedagog ya da çocuk psikoloğu tarafından yapılacak değerlendirme, kaygının nedenlerini anlamada önemli bir adımdır. Gerektiğinde uygulanan oyun terapisi, çocuğun iç dünyasını ifade etmesine yardımcı olarak kaygının daha sağlıklı bir şekilde azalmasını destekleyebilir.\n" .
    "Genel olarak uygun ebeveyn yaklaşımı ve destekleyici bir ortam sağlandığında ayrılık kaygısı çoğu çocukta birkaç hafta ile birkaç ay içinde belirgin şekilde azalır. Ancak belirtiler uzun süre devam ediyor, şiddeti artıyor veya çocuğun günlük yaşamını ciddi biçimde etkiliyorsa profesyonel destek almak sürecin uzamasını önlemek açısından oldukça önemlidir. Erken müdahale, kaygının kronikleşmesini engelleyerek çocuğun duygusal gelişimini korur ve özgüveninin güçlenmesine katkı sağlar.\n\n" .
    "## Ne Zaman Uzman Desteği Alınmalıdır?\n" .
    "Aşağıdaki durumlarda destek alınmalıdır:\n" .
    "- Kaygı uzun süre devam ediyorsa\n" .
    "- Okula gitmeyi reddediyorsa\n" .
    "- Fiziksel belirtiler artıyorsa\n" .
    "- Uyku ve iştah sorunları başladıysa\n" .
    "- Sosyal ilişkiler belirgin şekilde etkileniyorsa\n" .
    "Bu durumlarda bir psikolog, uzman psikolog veya pedagog desteği almak sürecin sağlıklı yönetilmesine yardımcı olur.\n\n" .
    "## Sonuç\n" .
    "Ayrılık kaygısı, çocukluk döneminde sık görülen ancak doğru yaklaşımlar uygulanmadığında hem çocuğun duygusal gelişimini hem de aile içi ilişkileri olumsuz etkileyebilen önemli bir durumdur. Özellikle okul öncesi ve ilkokulun ilk yıllarında ortaya çıkan bu kaygı, çocuğun kendini güvende hissetme ihtiyacıyla yakından ilişkilidir. Bu nedenle ayrılık kaygısını yalnızca “ağlama” ya da “okula gitmek istememe” davranışı olarak değerlendirmek yerine, çocuğun duygusal dünyasını anlamaya çalışmak büyük önem taşır.\n" .
    "Ebeveynlerin bu süreçte sabırlı, tutarlı ve destekleyici olması, kaygının azalmasında belirleyici rol oynar. Çocuğa güven veren rutinler oluşturmak, vedalaşmaları kısa ve net tutmak, çocuğun duygularını küçümsememek ve onun kaygısını anladığınızı hissettirmek oldukça etkilidir. Ancak kaygı uzun süre devam ediyor, çocuğun günlük yaşamını ve sosyal ilişkilerini belirgin biçimde etkiliyorsa profesyonel destek almak sürecin sağlıklı ilerlemesini sağlar.\n" .
    "Bu noktada bir psikolog, uzman psikolog, pedagog veya çocuk psikoloğu tarafından yapılan değerlendirme, ayrılık kaygısının düzeyini ve altında yatan nedenleri anlamaya yardımcı olur. Gerektiğinde uygulanan oyun terapisi, çocukların duygularını oyun yoluyla ifade etmelerine olanak tanıyarak kaygının azalmasında oldukça etkili bir yöntemdir. Oyun terapisi sayesinde çocuk, yaşadığı korkuları ve endişeleri sembolik olarak ortaya koyar ve bu süreçte güven duygusu yeniden yapılandırılabilir.\n" .
    "Ayrılık kaygısının erken fark edilmesi ve doğru şekilde ele alınması, ilerleyen yıllarda oluşabilecek okul kaygısı, özgüven sorunları veya sosyal çekingenlik gibi problemlerin önlenmesi açısından da koruyucu bir rol oynar. Bu nedenle ebeveynlerin çocuklarının davranışlarındaki değişimleri dikkatle gözlemlemesi, gerektiğinde bir uzmana başvurmaktan çekinmemesi önemlidir. Unutulmamalıdır ki psikolojik destek almak yalnızca sorunlar büyüdüğünde değil, erken dönemde de oldukça faydalıdır ve sürecin daha kısa sürede olumlu sonuçlanmasını sağlar.\n" .
    "Her çocuk farklıdır ve her çocuğun kaygıyla baş etme süreci kendine özgüdür. Bu yüzden başkalarıyla kıyaslama yapmak yerine çocuğun bireysel ihtiyaçlarına odaklanmak, onun duygusal gelişimini destekleyen en sağlıklı yaklaşımdır. Doğru yönlendirme, bilinçli ebeveyn tutumu ve gerektiğinde profesyonel destekle ayrılık kaygısı büyük ölçüde aşılabilir ve çocuklar kendilerini daha güvende hissederek sosyal hayata daha kolay uyum sağlayabilirler.\n" .
    "Çocuğunuzda ayrılık kaygısı olduğunu düşünüyorsanız İstanbul Fatih’teki Erguvan Psikolojik Danışmanlık Merkezi’ne başvurarak bir psikolog, uzman psikolog, pedagog veya çocuk psikoloğu eşliğinde değerlendirme yapılmasını sağlayabilir, gerekirse oyun terapisi desteğiyle sürecin sağlıklı şekilde ilerlemesine yardımcı olabilirsiniz.\n" .
    "Sena Ceren Notu...";

$fileName = 'cocuklarda-ayrilik-kaygisi-nedir.txt';
$fullPath = $exportPath . '/' . $fileName;

$data = "BAŞLIK: Çocuklarda Ayrılık Kaygısı Nedir, Neden Olur ve Nasıl Geçer?\n";
$data .= "------------------------------------------\n\n";
$data .= $ayrilik_full;

file_put_contents($fullPath, $data);
echo "Tam metin kaydedildi: $fileName\n";
?>