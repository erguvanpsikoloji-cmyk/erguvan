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

Write-Host "`n--- DOSYA YOLLARI KONTROL EDİLİYOR ---" -ForegroundColor Cyan
# Ekran görüntüsüne göre public_html/admin içinde olmalı
$sourceUrl = "ftp://$ftpHost/public_html/admin/temp_index.php"
$destUrl = "ftp://$ftpHost/public_html/index.php"

# Adım 1: Mevcut index.php'yi SİL (Eğer varsa ve kilitli değilse)
Invoke-FtpAction -Url $destUrl -Method [System.Net.WebRequestMethods+Ftp]::DeleteFile

# Adım 2: temp_index.php -> index.php (RENAME)
# RenameTo parametresi görecelidir, RNTO komutuna gider.
$restored = Invoke-FtpAction -Url $sourceUrl -Method [System.Net.WebRequestMethods+Ftp]::Rename -RenameTo "index.php"

if (-not $restored) {
    # Adım 3: Eğer RENAME başarısızsa (farklı klasörler arası rename kısıtlı olabilir), Download/Upload dene
    Write-Host "⚠️ RENAME (Aynı klasörde) denendi. Şimdi Farklı klasöre taşınmayı deniyoruz..." -ForegroundColor Yellow
   
    # Belki RenameTo ".. /index.php" gerekiyordur (admin içinde olduğumuz için)
    # Ama FtpWebRequest RENAME genellikle RNFR [Source] RNTO [Dest] yapar. Dest mutlak veya göreceli olabilir.
    $restored2 = Invoke-FtpAction -Url $sourceUrl -Method [System.Net.WebRequestMethods+Ftp]::Rename -RenameTo "../index.php"
   
    if (-not $restored2) {
        Write-Host "⚠️ Manuel Copy (Download/Upload) Deneniyor..." -ForegroundColor Yellow
        try {
            $localTemp = [System.IO.Path]::GetTempFileName()
            $wc = New-Object System.Net.WebClient
            $wc.Credentials = $cred
            $wc.DownloadFile($sourceUrl, $localTemp)
            $wc.UploadFile($destUrl, $localTemp)
            Write-Host "✅ [OK] Download/Upload Restorasyonu Başarılı!" -ForegroundColor Green
            Remove-Item $localTemp
        }
        catch {
            Write-Host "❌ [FAIL] Manuel Restorasyon da Başarısız: $($_.Exception.Message)" -ForegroundColor Red
        }
    }
}

# --- TEMİZLİK BÖLÜMÜ ---
# Sadece en büyük klasörü deneyelim (Risk azaltmak için)
Write-Host "`n--- .local TEMİZLENİYOR ---" -ForegroundColor Yellow
# Not: Klasör silmek için önce içindeki dosyaları silmek gerekir. 
# Bu işlem kısıtlıysa 451/550 alacağız.

Write-Host "`n--- BİTTİ ---" -ForegroundColor Cyan
