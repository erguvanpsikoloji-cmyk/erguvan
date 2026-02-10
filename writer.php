<?php
if (isset($_POST['data'])) {
    $data = $_POST['data'];
    $file = 'assets/images/logo_erguvan_2026.png';
    $decoded = base64_decode($data);
    if (file_put_contents($file, $decoded)) {
        echo "BASARILI: $file olusturuldu.";
    } else {
        echo "HATA: Dosya yazilamadi.";
    }
}
?>
<form method="POST">
    <textarea name="data" style="width:100%;height:300px;"></textarea>
    <button type="submit">Yukle</button>
</form>