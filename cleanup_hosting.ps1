$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"

$pathsToDelete = @(
    "temp_sena_check",
    "temp_sena_kurtarma",
    "toggle_fix_BASIT",
    "zip_check_temp",
    "upload_package",
    "style_fix_v3_pkg",
    "test_zip_out",
    "test_zip_src",
    "testsprite_tests"
)

$filesToDelete = @(
    "style.css.backup_20260109_013702",
    "style.css.backup_cls_20260102_185429",
    "style.css.backup_cls_20260102_190554",
    "style.css.bak",
    "style.min.css.ESK",
    "style.min.css.backup_cls_20260102_185429",
    "style.min.css.backup_cls_20260102_190554",
    "index_bk_error.php",
    "index_upload.php"
)

function Delete-FtpFile($path) {
    try {
        $uri = "ftp://$ftpHost/$path"
        $request = [System.Net.FtpWebRequest]::Create($uri)
        $request.Method = [System.Net.WebRequestMethods+Ftp]::DeleteFile
        $request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $response = $request.GetResponse()
        Write-Host "✅ Deleted File: $path" -ForegroundColor Green
        $response.Close()
    }
    catch {
        # Ignore if file doesn't exist
        # Write-Host "⚠️ Could not delete file $path : $($_.Exception.Message)" -ForegroundColor Gray
    }
}

function Remove-FtpDirectory($path) {
    # Recursively delete contents first (naive approach, strict FTP might fail if not empty)
    # Since we can't easily list and recurse without a complex script, we will try to delete. 
    # If it fails due to being non-empty, we'd need a recursive delete function.
    # For now, let's try the simple RemoveDirectory. 
    # NOTE: FtpWebRequest.RemoveDirectory only works on empty directories.
    
    # Simple recursive delete implementation
    try {
        $uri = "ftp://$ftpHost/$path"
        
        # List contents
        $listRequest = [System.Net.FtpWebRequest]::Create($uri)
        $listRequest.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectoryDetails
        $listRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        
        try {
            $response = $listRequest.GetResponse()
            $reader = New-Object System.IO.StreamReader($response.GetResponseStream())
            $listing = $reader.ReadToEnd()
            $reader.Close()
            $response.Close()
        }
        catch {
            Write-Host "⚠️ Directory $path not found or accessible." -ForegroundColor Gray
            return
        }

        $lines = $listing -split "`r`n"
        foreach ($line in $lines) {
            if ([string]::IsNullOrWhiteSpace($line)) { continue }
            
            # Parse line (Unix style assumption based on previous output)
            # drwxr-xr-x    2 8076       erguvanpsi         58 Jan 24 00:15 toggle_fix_BASIT
            $parts = $line -split "\s+", 9
            if ($parts.Count -lt 9) { continue }
            
            $name = $parts[8]
            if ($name -eq "." -or $name -eq "..") { continue }
            
            $isDir = $line.StartsWith("d")
            $fullPath = "$path/$name"
            
            if ($isDir) {
                Remove-FtpDirectory $fullPath
            }
            else {
                Delete-FtpFile $fullPath
            }
        }

        # Now delete the empty directory
        $delRequest = [System.Net.FtpWebRequest]::Create($uri)
        $delRequest.Method = [System.Net.WebRequestMethods+Ftp]::RemoveDirectory
        $delRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $delResponse = $delRequest.GetResponse()
        Write-Host "✅ Removed Directory: $path" -ForegroundColor Green
        $delResponse.Close()

    }
    catch {
        Write-Host "❌ Failed to remove directory $path : $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Delete temporary files in root/assets
foreach ($file in $filesToDelete) {
    if ($file.StartsWith("style")) {
        Delete-FtpFile "assets/css/$file"
    }
    else {
        Delete-FtpFile $file
    }
}

# Delete directories
foreach ($dir in $pathsToDelete) {
    Remove-FtpDirectory $dir
}
