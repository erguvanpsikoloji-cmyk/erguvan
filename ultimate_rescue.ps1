$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$cred = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)

function Invoke-FtpAction {
    param([string]$Url, [string]$Method, [string]$RenameTo = $null)
    try {
        $req = [System.Net.FtpWebRequest]::Create($Url)
        $req.Method = $Method
        $req.Credentials = $cred
        if ($RenameTo) { $req.RenameTo = $RenameTo }
        $resp = $req.GetResponse()
        $resp.Close()
        Write-Host "✅ [OK] $Method -> $Url" -ForegroundColor Green
        return $true
    }
    catch {
        Write-Host "❌ [FAIL] $Method -> $Url | $($_.Exception.Message)" -ForegroundColor Red
        return $false
    }
}

# --- 1. RESTORE INDEX.PHP ---
Write-Host "`n[1/2] Site Restorasyonu Başlatılıyor..." -ForegroundColor Cyan

# Adım A: Mevcut index.php'yi silmeyi veya ismini değiştirmeyi dene (Yolu temizlemek için)
Invoke-FtpAction -Url "ftp://$ftpHost/public_html/index.php" -Method "DELETEFILE"

# Adım B: admin/temp_index.php -> public_html/index.php (RENAME)
$restored = Invoke-FtpAction -Url "ftp://$ftpHost/admin/temp_index.php" -Method "RENAME" -RenameTo "../public_html/index.php"

if (-not $restored) {
    Write-Host "⚠️ RENAME Başarısız. Manuel Copy (Download/Upload) Deneniyor..." -ForegroundColor Yellow
    try {
        $localTemp = [System.IO.Path]::GetTempFileName()
        $wc = New-Object System.Net.WebClient
        $wc.Credentials = $cred
        $wc.DownloadFile("ftp://$ftpHost/admin/temp_index.php", $localTemp)
        $wc.UploadFile("ftp://$ftpHost/public_html/index.php", $localTemp)
        Write-Host "✅ [OK] Download/Upload Restorasyonu Başarılı!" -ForegroundColor Green
        Remove-Item $localTemp
    }
    catch {
        Write-Host "❌ [FAIL] Manuel Restorasyon da Başarısız: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# --- 2. CLEANUP ---
Write-Host "`n[2/2] Temizlik Operasyonu Başlatılıyor..." -ForegroundColor Cyan

function Delete-RecursiveFtp {
    param([string]$DirUrl)
    Write-Host "İnceleniyor: $DirUrl" -ForegroundColor Gray
    
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
        # Eğer listeleyemiyorsa dosyadır veya yoktur
        Invoke-FtpAction -Url $DirUrl -Method "DELETEFILE"
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
            Delete-RecursiveFtp -DirUrl $childUrl
        }
        else {
            Invoke-FtpAction -Url $childUrl -Method "DELETEFILE"
        }
    }
    Invoke-FtpAction -Url $DirUrl -Method "REMOVEDIRECTORY"
}

$targets = @(
    "public_html/ERGUVAN_EXPORT",
    "public_html/.local",
    "public_html/diagnostic_pkg",
    "public_html/erguvan_basit_kurulum",
    "public_html/erguvan_cikarilmis",
    "public_html/SON_COZUM"
)

foreach ($t in $targets) {
    Write-Host "`nTemizleniyor: $t" -ForegroundColor Yellow
    Delete-RecursiveFtp -DirUrl "ftp://$ftpHost/$t"
}

Write-Host "`n--- OPERASYON TAMAMLANDI ---" -ForegroundColor Cyan
