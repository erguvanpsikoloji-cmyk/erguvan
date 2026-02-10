<?php
// admin/pages/team-delete.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../database/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$token = $_GET['token'] ?? '';

// Token kontrolü (CSRF koruması için link üzerinden gelen isteklerde de token kontrolü yapıyoruz)
// Eğer session'daki token ile eşleşmiyorsa işlem yapma
if (!$id || $token !== ($_SESSION['csrf_token'] ?? '')) {
    header('Location: ' . admin_url('pages/team.php?error=Geçersiz istek'));
    exit;
}

try {
    $db = getDB();

    // Önce resmi silmek istersek buradan resim yolunu alabiliriz
    // $stmt = $db->prepare("SELECT image FROM team_members WHERE id = ?");
    // $stmt->execute([$id]);
    // $member = $stmt->fetch();
    // if ($member && $member['image'] && file_exists(__DIR__ . '/../../' . $member['image'])) {
    //     unlink(__DIR__ . '/../../' . $member['image']);
    // }

    $stmt = $db->prepare("DELETE FROM team_members WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: ' . admin_url('pages/team.php?success=Uzman başarıyla silindi'));
} catch (PDOException $e) {
    header('Location: ' . admin_url('pages/team.php?error=Silme hatası: ' . urlencode($e->getMessage())));
}
exit;
