<?php
/**
 * AJAX görsel yükleme endpoint'i
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/upload-handler.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu.']);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Dosya seçilmedi veya yükleme hatası oluştu.']);
    exit;
}

// Klasör parametresini al (varsayılan: sliders)
$folder = $_GET['folder'] ?? 'sliders';
// Güvenlik için sadece izin verilen klasörler
$allowedFolders = ['sliders', 'certificates', 'office'];
if (!in_array($folder, $allowedFolders)) {
    $folder = 'sliders';
}

$result = handleImageUpload($_FILES['image'], $folder);
echo json_encode($result);

