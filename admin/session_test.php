<?php
session_start();
echo "<h1>Session Test</h1>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Data:\n";
print_r($_SESSION);
echo "\nGET Parameters:\n";
print_r($_GET);
echo "</pre>";

if (isset($_GET['create'])) {
    $_SESSION['test'] = 'Session çalışıyor!';
    echo "<p style='color:green;'>Session oluşturuldu!</p>";
}

echo "<p><a href='?create=1'>Session Oluştur</a></p>";
?>