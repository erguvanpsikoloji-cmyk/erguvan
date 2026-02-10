<?php
/**
 * Randevu/İletişim Formu İşleme
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/db.php';

header('Content-Type: application/json; charset=utf-8');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Geçersiz istek!';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Form verilerini al
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$service = trim($_POST['service'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validasyon
if (empty($name) || empty($email) || empty($phone) || empty($service)) {
    $response['message'] = 'Lütfen tüm gerekli alanları doldurun!';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Geçerli bir e-posta adresi girin!';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $db = getDB();

    // Randevu talebini veritabanına kaydet
    // MySQL'de created_at otomatik olarak CURRENT_TIMESTAMP ile doldurulur
    $stmt = $db->prepare("INSERT INTO appointments (name, email, phone, service, message, status) 
                          VALUES (:name, :email, :phone, :service, :message, 'pending')");
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':service' => $service,
        ':message' => $message
    ]);

    // Hizmet isimlerini düzenle
    $serviceNames = [
        'bireysel' => 'Bireysel Terapi',
        'online' => 'Online Terapi',
        'cift' => 'Çift Terapisi',
        'aile' => 'Aile Danışmanlığı'
    ];
    $serviceName = $serviceNames[$service] ?? $service;

    // E-posta gönder
    $to = 'sedatparmak@gmail.com';
    $subject = 'Yeni Randevu Talebi - Uzm. Psk. Sena Ceren';

    // E-posta içeriği
    $emailBody = "Yeni bir randevu talebi alındı.\n\n";
    $emailBody .= "=== RANDEVU BİLGİLERİ ===\n\n";
    $emailBody .= "Ad Soyad: " . $name . "\n";
    $emailBody .= "E-posta: " . $email . "\n";
    $emailBody .= "Telefon: " . $phone . "\n";
    $emailBody .= "Hizmet: " . $serviceName . "\n";
    if (!empty($message)) {
        $emailBody .= "Mesaj: " . $message . "\n";
    }
    $emailBody .= "\n";
    $emailBody .= "Tarih: " . date('d.m.Y H:i') . "\n";
    $emailBody .= "\n";
    $emailBody .= "Bu e-posta otomatik olarak gönderilmiştir.\n";

    // E-posta başlıkları
    $headers = "From: Uzm. Psk. Sena Ceren <noreply@uzmanpsikologsenaceren.com>\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // E-posta gönder
    $mailSent = @mail($to, $subject, $emailBody, $headers);

    // E-posta gönderim hatası olsa bile randevu kaydı başarılı olduğu için başarı mesajı döndür
    // (E-posta gönderim hatası loglanır ama kullanıcıya gösterilmez)
    if (!$mailSent) {
        error_log('E-posta gönderim hatası: Randevu talebi kaydedildi ancak e-posta gönderilemedi.');
    }

    $response['success'] = true;
    $response['message'] = 'Randevu talebiniz başarıyla alındı! En kısa sürede sizinle iletişime geçeceğiz.';

} catch (PDOException $e) {
    $response['message'] = 'Bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
    // Hata detayını logla (geliştirme için)
    error_log('Appointment error: ' . $e->getMessage());
    // Geliştirme ortamında daha detaylı hata mesajı göster
    if (defined('DEBUG') && DEBUG) {
        $response['message'] = 'Hata: ' . $e->getMessage();
    }
}

// JSON response gönder ve çık
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;

