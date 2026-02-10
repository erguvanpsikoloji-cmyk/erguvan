<?php
// Local FTP Management Script
$ftp_server = "ftp.erguvanpsikoloji.com";
$ftp_user = "mirza@erguvanpsikoloji.com";
$ftp_pass = "92Mirza1!";

echo "Connecting to $ftp_server...\n";
$conn_id = ftp_connect($ftp_server);

if (!$conn_id) {
    die("❌ Connection failed\n");
}

if (@ftp_login($conn_id, $ftp_user, $ftp_pass)) {
    echo "✅ Connected as $ftp_user\n";
} else {
    die("❌ Login failed\n");
}

ftp_pasv($conn_id, true);

// 1. RESTORE INDEX.PHP
echo "\n--- Attempting to Restore index.php ---\n";
// Try to rename/move
$source = "admin/temp_index.php";
$dest = "public_html/index.php";

// Check if source exists
if (ftp_size($conn_id, $source) != -1) {
    echo "Found source: $source\n";

    // First, try to delete destination if it exists (to avoid overwrite errors)
    if (ftp_size($conn_id, $dest) != -1) {
        if (ftp_delete($conn_id, $dest)) {
            echo "Deleted existing broken $dest\n";
        }
    }

    if (ftp_rename($conn_id, $source, $dest)) {
        echo "✅ SUCCESS: Moved $source to $dest\n";
    } else {
        echo "⚠️ Rename failed. Trying download/upload approach...\n";

        // Alternative: Download to local memory and upload
        $tempHandle = fopen('php://temp', 'r+');
        if (ftp_fget($conn_id, $tempHandle, $source, FTP_BINARY)) {
            rewind($tempHandle);
            if (ftp_fput($conn_id, $dest, $tempHandle, FTP_BINARY)) {
                echo "✅ SUCCESS: Copied $source to $dest via memory\n";
            } else {
                echo "❌ Upload failed via memory.\n";
            }
        } else {
            echo "❌ Download failed via memory.\n";
        }
        fclose($tempHandle);
    }
} else {
    echo "❌ Source file $source NOT FOUND on server!\n";
}

// 2. CLEANUP GARBAGE
echo "\n--- Attempting Cleanup ---\n";

function recursiveDelete($conn_id, $directory)
{
    if (!@ftp_chdir($conn_id, $directory)) {
        // Can't chdir, maybe it's a file?
        if (@ftp_delete($conn_id, $directory)) {
            echo "Deleted file: $directory\n";
            return true;
        }
        return false;
    }

    // We are in directory
    $files = ftp_nlist($conn_id, ".");
    foreach ($files as $file) {
        if ($file == '.' || $file == '..')
            continue;

        if (@ftp_chdir($conn_id, $file)) {
            ftp_chdir($conn_id, "..");
            recursiveDelete($conn_id, "$directory/$file");
        } else {
            ftp_delete($conn_id, "$directory/$file");
        }
    }

    ftp_chdir($conn_id, ".."); // Go back up
    if (@ftp_rmdir($conn_id, $directory)) {
        echo "✅ Deleted directory: $directory\n";
    } else {
        echo "⚠️ Could not remove directory: $directory\n";
    }
}

$cleanup_targets = [
    "public_html/ERGUVAN_EXPORT",
    "public_html/.local",
    "public_html/diagnostic_pkg",
    "public_html/erguvan_basit_kurulum",
    "public_html/erguvan_cikarilmis",
    "public_html/SON_COZUM"
];

foreach ($cleanup_targets as $target) {
    echo "Targeting: $target\n";
    recursiveDelete($conn_id, $target);
}

ftp_close($conn_id);
echo "\nDone.\n";
?>