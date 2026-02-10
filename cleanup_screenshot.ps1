$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$cred = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)

function Invoke-FtpAction {
    param([string]$Url, [string]$Method)
    try {
        $req = [System.Net.FtpWebRequest]::Create($Url)
        $req.Method = $Method
        $req.Credentials = $cred
        $resp = $req.GetResponse()
        $resp.Close()
        Write-Host "✅ [OK] $Method -> $Url" -ForegroundColor Green
        return $true
    }
    catch {
        # Silently fail if file not found or already deleted
        if ($_.Exception.Message -like "*550*") {
            Write-Host "ℹ️ [SKIP] $Url (Not found or no permission)" -ForegroundColor Gray
        }
        else {
            Write-Host "❌ [FAIL] $Method -> $Url | $($_.Exception.Message)" -ForegroundColor Red
        }
        return $false
    }
}

function Delete-RecursiveFtp {
    param([string]$DirUrl)
    Write-Host "Cleaning: $DirUrl" -ForegroundColor Cyan
    
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
        # Not a directory or can't list, try deleting as file
        Invoke-FtpAction -Url $DirUrl -Method [System.Net.WebRequestMethods+Ftp]::DeleteFile
        return
    }

    $lines = $content -split "`r`n"
    foreach ($line in $lines) {
        if ([string]::IsNullOrWhiteSpace($line)) { continue }
        # Format: drwxr-xr-x    2 8076       erguvanpsi       4096 Feb  8 02:12 foldername
        # Note: FTP LIST format varies, this is a common Unix-like format
        $parts = $line -split "\s+", 9
        if ($parts.Count -lt 9) { continue }
        $name = $parts[8]
        if ($name -eq "." -or $name -eq "..") { continue }
        
        $isDir = $line.StartsWith("d")
        $childUrl = "$DirUrl/$name"

        if ($isDir) {
            Delete-RecursiveFtp -DirUrl $childUrl
        }
        else {
            Invoke-FtpAction -Url $childUrl -Method [System.Net.WebRequestMethods+Ftp]::DeleteFile
        }
    }
    Invoke-FtpAction -Url $DirUrl -Method [System.Net.WebRequestMethods+Ftp]::RemoveDirectory
}

$targets = @(
    "temp_sena_check",
    "temp_sena_kurtarma",
    "temp_style.txt",
    "temp_update_text.php",
    "test_debug.php",
    "test_output.php",
    "ERGUVAN_EXPORT",
    ".local",
    "diagnostic_pkg",
    "erguvan_basit_kurulum",
    "erguvan_cikarilmis",
    "SON_COZUM",
    "YEDEK_2026_02_02",
    "assets_fix_pkg",
    "css_fix_pkg",
    "deploy_fix_images",
    "dist_cls_v10",
    "dist_cls_v11",
    "dist_cls_v12",
    "dist_cls_v13",
    "dist_cls_v7",
    "dist_cls_v8",
    "dist_cls_v9",
    "scrollbar_fix_v1.zip",
    "scrollbar_fix_v1"
)

foreach ($t in $targets) {
    $url = "ftp://$ftpHost/$t"
    # Try deleting as folder first, if fail it will try as file in the function
    Delete-RecursiveFtp -DirUrl $url
}

Write-Host "`n--- CLEANUP COMPLETE ---" -ForegroundColor Cyan
