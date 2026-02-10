$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$cred = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)

function Send-FtpCommand {
    param([string]$Url, [string]$Method, [string]$RenameTo = $null)
    try {
        $req = [System.Net.FtpWebRequest]::Create($Url)
        $req.Method = $Method
        $req.Credentials = $cred
        if ($RenameTo) { $req.RenameTo = $RenameTo }
        $resp = $req.GetResponse()
        Write-Host "✅ Success: $Url ($Method)" -ForegroundColor Green
        $resp.Close()
        return $true
    }
    catch {
        Write-Host "❌ Failed: $Url ($Method) - $($_.Exception.Message)" -ForegroundColor Red
        return $false
    }
}

# 1. Attempt to Restore index.php (Move from admin/temp_index.php)
Write-Host "`n--- RESTORING INDEX.PHP ---" -ForegroundColor Cyan
# Try moving admin/temp_index.php to ../public_html/index.php
Send-FtpCommand -Url "ftp://$ftpHost/admin/temp_index.php" -Method "RENAME" -RenameTo "../public_html/index.php"

# 2. Cleanup ERGUVAN_EXPORT Folder
Write-Host "`n--- CLEANING UP ERGUVAN_EXPORT ---" -ForegroundColor Cyan

function Delete-Recursive {
    param([string]$DirUrl)
    
    # List files
    try {
        $req = [System.Net.FtpWebRequest]::Create($DirUrl)
        $req.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectoryDetails
        $req.Credentials = $cred
        $resp = $req.GetResponse()
        $reader = New-Object System.IO.StreamReader($resp.GetResponseStream())
        $content = $reader.ReadToEnd()
        $reader.Close(); $resp.Close()
    }
    catch { 
        # If list fails, maybe it's a file or doesn't exist. Try delete file.
        Write-Host "Listing failed for $DirUrl, trying as file delete..." -ForegroundColor Gray
        Send-FtpCommand -Url $DirUrl -Method "DELETEFILE"
        return
    }

    $lines = $content -split "`r`n"
    foreach ($line in $lines) {
        if ([string]::IsNullOrWhiteSpace($line)) { continue }
        $parts = $line -split "\s+", 9
        if ($parts.Count -lt 9) { continue }
        $name = $parts[8]
        if ($name -eq "." -or $name -eq "..") { continue }
        
        $isDir = $line.StartsWith("d")
        $childUrl = "$DirUrl/$name"

        if ($isDir) {
            Delete-Recursive -DirUrl $childUrl
        }
        else {
            Send-FtpCommand -Url $childUrl -Method "DELETEFILE"
        }
    }
    # Finally remove empty dir
    Send-FtpCommand -Url $DirUrl -Method "REMOVEDIRECTORY"
}

# Target cleanup
Delete-Recursive -DirUrl "ftp://$ftpHost/public_html/ERGUVAN_EXPORT"
# Also try others mentioned
Delete-Recursive -DirUrl "ftp://$ftpHost/public_html/.local"
Delete-Recursive -DirUrl "ftp://$ftpHost/public_html/diagnostic_pkg"

Write-Host "`n--- DONE ---" -ForegroundColor Cyan
