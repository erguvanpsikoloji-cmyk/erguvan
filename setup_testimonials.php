<?php
/**
 * Testimonials Tablosu Kurulum Scripti
 * Bu script testimonials tablosunu oluşturur ve örnek yorumları ekler.
 * Tek seferlik çalıştır, ardından sil.
 */
require_once __DIR__ . '/database/db.php';

$db = getDB();

// 1. Tabloyu oluştur
$db->exec("CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    comment TEXT NOT NULL,
    rating TINYINT DEFAULT 5,
    source VARCHAR(50) DEFAULT 'Google',
    date_info VARCHAR(50) DEFAULT NULL,
    avatar_char CHAR(2) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

echo "✅ Tablo oluşturuldu.<br>";

// 2. Zaten kayıt var mı kontrol et
$count = $db->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
if ($count > 0) {
    echo "⚠️ Tabloda zaten {$count} yorum var. Seed atlandı.<br>";
} else {
    // 3. Seed - 8 orijinal Google yorumu
    $testimonials = [
        ['Nihat Duman', "Sena Hanım'a tavsiye üzerine gittim, ilgi ve alakası çok iyi. Gerçek bir psikolog arıyorsanız doğru adrestesiniz.", 5, 'Google', 'Bir ay önce', 'N', 1, 1],
        ['Ayşe Yılmaz', "Sena Hanım'ı kesinlikle tavsiye ederim. Çok anlayışlı ve profesyonel. Online terapi seansları çok verimli geçti.", 5, 'Google', '3 ay önce', 'A', 1, 2],
        ['A. Kaya', "Hayatımın en zor döneminde Sena Hanım ile tanıştım. Profesyonel ve samimi yaklaşımı sayesinde kendimi yeniden buldum.", 5, 'Google', '2 ay önce', 'A', 1, 3],
        ['M. Yılmaz', "Ferah ofis ortamı ve güven veren duruşuyla terapi sürecim çok verimli geçti. Herkese tavsiye ediyorum.", 5, 'Google', '4 ay önce', 'M', 1, 4],
        ['Selin Arslan', "Sena Hanım ile çalışmak hayatımda çok önemli bir adım oldu. Sabırlı, anlayışlı ve oldukça deneyimli bir terapist. Her seanstan motive ayrılıyorum.", 5, 'Google', '5 ay önce', 'S', 1, 5],
        ['Kemal Çelik', "Sedat Bey ile yaptığım görüşmeler sayesinde iş hayatındaki stresimi çok daha iyi yönetmeye başladım. Profesyonel ve çözüm odaklı bir yaklaşımı var.", 5, 'Google', '6 ay önce', 'K', 1, 6],
        ['Elif Bozkurt', "Panik atak tedavisinde gerçekten büyük ilerleme kaydettim. Sena Hanım hem anlayışlı hem de çok bilgili. Terapi sürecinde hiç yalnız hissetmedim.", 5, 'Google', '7 ay önce', 'E', 1, 7],
        ['Oğuz Demir', "Çok anlayışlı ve güven verici bir ortam. İlk seanstan itibaren kendimi rahat hissettim. Kesinlikle tavsiye ediyorum.", 5, 'Google', '8 ay önce', 'O', 1, 8],
    ];

    $stmt = $db->prepare("INSERT INTO testimonials (name, comment, rating, source, date_info, avatar_char, is_active, display_order) VALUES (?,?,?,?,?,?,?,?)");
    foreach ($testimonials as $t) {
        $stmt->execute($t);
    }
    echo "✅ " . count($testimonials) . " yorum eklendi.<br>";
}

echo "<br><strong>🎉 Kurulum tamamlandı! Bu dosyayı silebilirsin.</strong>";
?>