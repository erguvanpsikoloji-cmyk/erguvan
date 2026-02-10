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
        # Hide 550 errors (not found)
        if ($_.Exception.Message -notlike "*550*") {
            Write-Host "⚠️ [FAIL] $Method -> $Url | $($_.Exception.Message)" -ForegroundColor Yellow
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
        $childUrl = "$DirUrl/" + [Uri]::EscapeDataString($name)

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
    "ERGUVAN PSİKOTERAPİ MERKEZİ",
    "public_html/erguvan psikoloji web",
    ".local",
    "diagnostic_pkg",
    "erguvan_basit_kurulum",
    "erguvan_cikarilmis",
    "SON_COZUM"
)

Write-Host "--- CLEANUP PHASE 2 ---"

foreach ($t in $targets) {
    # Encode the path properly
    $encodedPath = $t -replace ' ', '%20'
    $url = "ftp://$ftpHost/$encodedPath"
    Delete-RecursiveFtp -DirUrl $url
}

Write-Host "--- CLEANUP COMPLETE ---" -ForegroundColor Cyan
