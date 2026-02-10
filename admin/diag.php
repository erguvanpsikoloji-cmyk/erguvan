<?php
ob_start();
session_start();

$dir = __DIR__;
$root_dir = realpath($dir . '/../../');
$session_dir = $root_dir . '/sessions';

echo "<h1>Oturum Teşhis Raporu</h1>";
echo "<b>Geçerli Oturum ID:</b> " . session_id() . "<br>";
echo "<b>Oturum Kayıt Yolu (Default):</b> " . session_save_path() . "<br>";
echo "<b>Oturum Dizini (Özel):</b> " . $session_dir . "<br>";

if (is_dir($session_dir)) {
    echo "<b>Özel Oturum Dizini Mevcut mu?</b> Evet<br>";
    echo "<b>Yazılabilir mi?</b> " . (is_writable($session_dir) ? "<span style='color:green'>Evet</span>" : "<span style='color:red'>Hayır</span>") . "<br>";
} else {
    echo "<b>Özel Oturum Dizini Mevcut mu?</b> Hayır (Lütfen oluşturun: $session_dir)<br>";
}

echo "<b>Çerezler (Cookies):</b> <pre>";
print_r($_COOKIE);
echo "</pre>";

echo "<b>Oturum Verisi:</b> <pre>";
print_r($_SESSION);
echo "</pre>";

if (!isset($_SESSION['test_val'])) {
    $_SESSION['test_val'] = time();
    echo "<p style='color:blue'>Yeni bir test değeri atandı. Lütfen sayfayı yenileyin!</p>";
} else {
    echo "<p style='color:green'>Oturum kalıcı! Test Değeri: " . $_SESSION['test_val'] . "</p>";
}

echo "<br><br><a href='diag.php'>Yenilemek için buraya tıklayın</a>";
?>