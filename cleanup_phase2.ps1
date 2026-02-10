$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$cred = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)

function Invoke-FtpMethod {
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
        Write-Host "❌ [FAIL] $Method -> $Url | $($_.Exception.Message)" -ForegroundColor Yellow
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
        return (Invoke-FtpMethod -Url $DirUrl -Method "DELE")
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
            Delete-RecursiveFtp -DirUrl $childUrl
        }
        else {
            Invoke-FtpMethod -Url $childUrl -Method "DELE"
        }
    }
    return (Invoke-FtpMethod -Url $DirUrl -Method "RMD")
}

$targets = @(
    "dist_fcp_v6",
    "dist_final",
    "dist_hiz_90",
    "dist_toc",
    "dist_toc_fixed",
    "dist_v22",
    "ERGUVAN_ARSIV",
    "public_html/erguvan psikoloji web",
    "public_html/temp_sena_check",
    "public_html/temp_sena_kurtarma",
    "public_html/sertifikalar" # Screenshot shows it, but check if user wants it. Actually it has png/webp, maybe keep? Wait, user said "gereksizleri sil". Usually sertifikalar are used. I'll skip it for safety unless sure.
)

Write-Host "`n--- CLEANUP PHASE 2 START ---"

foreach ($t in $targets) {
    if ($t -eq "public_html/sertifikalar") { continue } # Safety skip
    
    # Try absolute root
    $rootUrl = "ftp://$ftpHost/$t"
    Delete-RecursiveFtp -DirUrl $rootUrl
}

Write-Host "`n--- CLEANUP COMPLETE ---" -ForegroundColor Cyan
