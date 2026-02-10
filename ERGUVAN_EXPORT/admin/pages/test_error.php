<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "Hata bastirma testi basliyor...<br>";
// Bilinçli olarak tanımlanmamış bir fonksiyon çağıralım
trigger_fatal_error();
?>