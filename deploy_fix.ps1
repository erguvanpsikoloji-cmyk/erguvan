$ftpServer = "ftp.erguvanpsikoloji.com"
$username = "erguvanpsi"
$password = "92Mirza1"

$filesToUpload = @(
    "index.php",
    "assets/css/style.css",
    "assets/css/update.css",
    "includes/header.php",
    "includes/footer.php",
    "admin/test.php",
    "admin/login.php"
)

$webClient = New-Object System.Net.WebClient
$webClient.Credentials = New-Object System.Net.NetworkCredential($username, $password)

foreach ($file in $filesToUpload) {
    $localPath = Join-Path (Get-Location) $file
    $remotePath = "ftp://$ftpServer/$file"
    
    # Ensure remote directory structure exists (simplified for common files)
    Write-Host "Yukleniyor: $file ..."
    try {
        $webClient.UploadFile($remotePath, "STOR", $localPath)
        Write-Host "Basarili!"
    }
    catch {
        Write-Host "HATA: $_"
    }
}

Write-Host "`n--- Islem Tamamlandi. Tarayicidan kontrol edin. ---"
