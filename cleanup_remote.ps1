$ftpServer = "ftp://104.247.162.226/"
$ftpUser = "erguvanpsi"
$ftpPass = "92Mirza1"

# List of items to delete (relative to root)
$itemsToDelete = @(
    "AT_ARTIK",
    "temp_sena_check",
    "temp_sena_kurtarma",
    "upload_package",
    "zip_check_temp",
    "toggle_fix_BASIT",
    "style_fix_v3_pkg",
    "testsprite_tests",
    "universal_restore.php",
    "universal_restore_v2.php",
    "antigravity_test.txt",
    "test_upload.txt",
    "restore_blogs_final.php",
    "add_blogs.php",
    "server_check.php", 
    "server_diag.php",
    "simple_check.php",
    "site-status.php",
    "sqlite_restore_final.php",
    "sync_all.php",
    "system_check.php",
    "system_test.php",
    "test.html",
    "test.php",
    "test_db_sena.php",
    "test_debug.php",
    "unzip_archive.php"
)

function Delete-FtpItem($path) {
    # Check if it's a directory or file first? 
    # Or just try DeleteFile, then RemoveDirectory if fails?
    
    $uri = $ftpServer + $path
    
    # Try Delete File first
    try {
        Write-Host "Trying to delete file: $path"
        $request = [System.Net.WebRequest]::Create($uri)
        $request.Method = [System.Net.WebRequestMethods+Ftp]::DeleteFile
        $request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $request.GetResponse().Close()
        Write-Host "Deleted file: $path" -ForegroundColor Green
        return
    }
    catch {
        # If error is not 550 (File unavailable), print it. 
        # But for directories, DeleteFile fails.
    }

    # Try Remove Directory (Recursive logic needed for non-empty dirs)
    # Standard FTP RemoveDirectory fails if not empty.
    # We need a recursive delete function.
    try {
        Write-Host "Trying to delete directory: $path"
        # First listing content to delete children
        $listReq = [System.Net.WebRequest]::Create($uri + "/")
        $listReq.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectoryDetails
        $listReq.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        
        try {
            $response = $listReq.GetResponse()
            $reader = New-Object System.IO.StreamReader($response.GetResponseStream())
            $listing = $reader.ReadToEnd()
            $reader.Close()
            $response.Close()
            
            $lines = $listing -split "`r`n"
            foreach ($line in $lines) {
                if ($line.Trim() -eq "") { continue }
                # Parse name (simple parsing logic, assumes last part is name)
                $parts = $line -split "\s+"
                $name = $parts[$parts.Count - 1]
                
                if ($name -eq "." -or $name -eq "..") { continue }
                
                # Recursive call
                Delete-FtpItem "$path/$name"
            }
        }
        catch {
            # Listing failed, maybe empty or not dir
        }

        # Now remove the directory itself
        $rmDirReq = [System.Net.WebRequest]::Create($uri)
        $rmDirReq.Method = [System.Net.WebRequestMethods+Ftp]::RemoveDirectory
        $rmDirReq.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $rmDirReq.GetResponse().Close()
        Write-Host "Deleted directory: $path" -ForegroundColor Green
        
    }
    catch {
        Write-Host "Failed to delete: $path - $($_.Exception.Message)" -ForegroundColor Red
    }
}

foreach ($item in $itemsToDelete) {
    Delete-FtpItem $item
}
