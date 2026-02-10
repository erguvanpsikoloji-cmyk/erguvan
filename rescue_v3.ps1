$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$cred = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)

function Get-FtpList {
    param([string]$Url)
    Write-Host "Listing: $Url" -ForegroundColor Gray
    try {
        $req = [System.Net.FtpWebRequest]::Create($Url)
        $req.Method = "LIST"
        $req.Credentials = $cred
        $resp = $req.GetResponse()
        $reader = New-Object System.IO.StreamReader($resp.GetResponseStream())
        $content = $reader.ReadToEnd()
        $reader.Close(); $resp.Close()
        Write-Host "✅ Found: `n$content" -ForegroundColor White
        return $true
    }
    catch {
        Write-Host "❌ Failed Listing: $($_.Exception.Message)" -ForegroundColor Red
        return $false
    }
}

function Do-FtpAction {
    param([string]$Url, [string]$Method, [string]$RenameTo = $null)
    try {
        $req = [System.Net.FtpWebRequest]::Create($Url)
        $req.Method = $Method
        $req.Credentials = $cred
        if ($RenameTo) { $req.RenameTo = $RenameTo }
        $resp = $req.GetResponse()
        $resp.Close()
        Write-Host "✅ Success: $Method -> $Url" -ForegroundColor Green
        return $true
    }
    catch {
        Write-Host "❌ Failed: $Method -> $Url | $($_.Exception.Message)" -ForegroundColor Red
        return $false
    }
}

# 1. DOSYA YOLU KEŞFİ
Write-Host "`n--- DİZİN KEŞFİ ---" -ForegroundColor Cyan
$paths = @(
    "ftp://$ftpHost/",
    "ftp://$ftpHost/public_html/",
    "ftp://$ftpHost/public_html/admin/",
    "ftp://$ftpHost/domains/erguvanpsikoloji.com/public_html/admin/"
)

foreach ($p in $paths) {
    Get-FtpList -Url $p
}

# 2. BULUNDUĞU VARSAYILAN YOL ÜZERİNDEN OPERASYON
# Ekran görüntüsünde "admin" klasöründe temp_index.php görüldü.
# public_html/admin/temp_index.php veya domains/erguvanpsikoloji.com/public_html/admin/temp_index.php

$source = "ftp://$ftpHost/public_html/admin/temp_index.php"
$dest = "ftp://$ftpHost/public_html/index.php"

Write-Host "`n--- OPERASYON ---" -ForegroundColor Cyan
# RNFR temp_index.php RNTO ../index.php
Do-FtpAction -Url $source -Method "RENAME" -RenameTo "../index.php"

# Eğer başarısızsa mutlak yolla dene (Server desteğine bağlı)
Do-FtpAction -Url $source -Method "RENAME" -RenameTo "/public_html/index.php"

Write-Host "`n--- BİTTİ ---"
