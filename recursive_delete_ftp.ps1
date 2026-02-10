$ftpUrl = "ftp://ftp.erguvanpsikoloji.com"
$cred = New-Object System.Net.NetworkCredential("mirza@erguvanpsikoloji.com", "92Mirza1!")

function Delete-FtpFolder {
    param (
        [string]$CurrentUrl
    )

    # 1. List contents (files and folders)
    try {
        $req = [System.Net.FtpWebRequest]::Create($CurrentUrl)
        $req.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectoryDetails
        $req.Credentials = $cred
        $resp = $req.GetResponse()
        $reader = New-Object System.IO.StreamReader($resp.GetResponseStream())
        $contents = $reader.ReadToEnd()
        $reader.Close()
        $resp.Close()
    }
    catch {
        Write-Host "Error listing $CurrentUrl : $($_.Exception.Message)" -ForegroundColor Red
        return
    }

    if ([string]::IsNullOrWhiteSpace($contents)) {
        # Empty folder, delete it
        Write-Host "Deleting empty folder: $CurrentUrl" -ForegroundColor Gray
        try {
            $delReq = [System.Net.FtpWebRequest]::Create($CurrentUrl)
            $delReq.Method = [System.Net.WebRequestMethods+Ftp]::RemoveDirectory
            $delReq.Credentials = $cred
            $delReq.GetResponse().Close()
        }
        catch { Write-Host "Failed to remove $CurrentUrl : $($_.Exception.Message)" -ForegroundColor Red }
        return
    }

    $lines = $contents -split "`r`n"
    
    foreach ($line in $lines) {
        if ([string]::IsNullOrWhiteSpace($line)) { continue }
        
        # Parse Unix style listing: drwxr-xr-x ... name
        $parts = $line -split "\s+", 9
        if ($parts.Count -lt 9) { continue }
        
        $name = $parts[8]
        if ($name -eq "." -or $name -eq "..") { continue }
        
        $isDir = $line.StartsWith("d")
        $childUrl = "$CurrentUrl/$name"

        if ($isDir) {
            # Recursively delete subfolder
            Delete-FtpFolder -CurrentUrl $childUrl
        }
        else {
            # Delete file
            try {
                $delReq = [System.Net.FtpWebRequest]::Create($childUrl)
                $delReq.Method = [System.Net.WebRequestMethods+Ftp]::DeleteFile
                $delReq.Credentials = $cred
                $delReq.GetResponse().Close()
                Write-Host "Deleted file: $name" -ForegroundColor DarkGray
            }
            catch {
                Write-Host "Failed to delete file $name : $($_.Exception.Message)" -ForegroundColor Red
            }
        }
    }

    # Finally remove the now-empty folder
    try {
        $delReq = [System.Net.FtpWebRequest]::Create($CurrentUrl)
        $delReq.Method = [System.Net.WebRequestMethods+Ftp]::RemoveDirectory
        $delReq.Credentials = $cred
        $delReq.GetResponse().Close()
        Write-Host "✅ DELETED FOLDER: $CurrentUrl" -ForegroundColor Green
    }
    catch {
        Write-Host "Failed to remove root $CurrentUrl : $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Main execution for passed folders
$folders = @(
    ".local",
    "diagnostic_pkg",
    "erguvan_basit_kurulum",
    "erguvan_cikarilmis",
    "SON_COZUM"
)

foreach ($f in $folders) {
    Write-Host "Starting recursive delete for: $f" -ForegroundColor Cyan
    Delete-FtpFolder -CurrentUrl "$ftpUrl/public_html/$f"
}
