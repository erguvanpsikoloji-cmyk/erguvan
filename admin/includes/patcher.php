<?php
// Simple File Patcher
// Usage: POST to this file with 'file' (relative path) and 'content' (base64 encoded)
// Security: Basic check

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_POST['file'] ?? '';
    $content = $_POST['content'] ?? '';
    $key = $_POST['key'] ?? '';

    if ($key !== 'Erguvan2026!') {
        die('❌ Access Denied');
    }

    // List Directory Mode
    if (isset($_POST['mode']) && $_POST['mode'] === 'list') {
        $dir = isset($_POST['dir']) ? $_POST['dir'] : __DIR__;
        if (is_dir($dir)) {
            $files = scandir($dir);
            echo "Listing $dir:\n";
            foreach ($files as $file) {
                echo $file . (is_dir($dir . '/' . $file) ? '/' : '') . "\n";
            }
        } else {
            echo "Not a directory: $dir";
        }
        exit;
    }

    // Read File Mode
    if (isset($_POST['mode']) && $_POST['mode'] === 'read') {
        $file = isset($_POST['file']) ? $_POST['file'] : '';
        $path = __DIR__ . '/' . $file;
        if (file_exists($path)) {
            echo file_get_contents($path);
        } else {
            echo "File not found: $path";
        }
        exit;
    }

    // Rename File Mode
    if (isset($_POST['mode']) && $_POST['mode'] === 'rename') {
        $old = isset($_POST['old']) ? $_POST['old'] : '';
        $new = isset($_POST['new']) ? $_POST['new'] : '';
        $oldPath = __DIR__ . '/' . $old;
        $newPath = __DIR__ . '/' . $new;

        if (file_exists($oldPath)) {
            if (rename($oldPath, $newPath)) {
                echo "Renamed $old to $new";
            } else {
                echo "Failed to rename $old";
            }
        } else {
            echo "File not found: $old";
        }
        exit;
    }

    // Delete File Mode
    if (isset($_POST['mode']) && $_POST['mode'] === 'delete') {
        $file = isset($_POST['file']) ? $_POST['file'] : '';
        $path = __DIR__ . '/' . $file;
        if (file_exists($path)) {
            if (unlink($path)) {
                echo "Deleted $file";
            } else {
                echo "Failed to delete $file";
            }
        } else {
            echo "File not found: $file";
        }
        exit;
    }

    // Unzip Mode
    if (isset($_POST['mode']) && $_POST['mode'] === 'unzip') {
        $file = isset($_POST['file']) ? $_POST['file'] : '';
        $path = __DIR__ . '/' . $file;
        $dest = isset($_POST['dest']) ? realpath(__DIR__ . '/' . $_POST['dest']) : __DIR__;

        $zip = new ZipArchive;
        if ($zip->open($path) === TRUE) {
            $zip->extractTo($dest);
            $zip->close();
            echo "✅ Unzipped $file to $dest";
        } else {
            echo "❌ Failed to open zip: $file";
        }
        exit;
    }

    // Write or Append Mode
    if (!empty($file) && !empty($content)) {
        $mode = isset($_POST['mode']) && $_POST['mode'] === 'append' ? FILE_APPEND : 0;

        // Decode content
        $decoded = base64_decode($content);
        if ($decoded === false) {
            die('❌ Base64 decode failed');
        }

        // Path check (allow only specific directories)
        $realBase = realpath(__DIR__ . '/../../'); // Root
        $targetPath = $realBase . '/' . $file;

        // Create directory if not exists
        $dir = dirname($targetPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (file_put_contents($targetPath, $decoded, $mode)) {
            echo "✅ Success (" . ($mode ? "Appended" : "Wrote") . "): $file (" . strlen($decoded) . " bytes)";
        } else {
            echo "❌ Write failed: $file";
            print_r(error_get_last());
        }
    }
} else {
    echo "Ready to patch.";
}
?>