$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$sourceRoot = "d:\Erguvan antigravity hosting"

$filesToUpload = @{
    "index.php"                           = "index.php"
    "includes/header.php"                 = "includes/header.php"
    "assets/images/logo_erguvan_2026.png" = "assets/images/logo_erguvan_2026.png"
}

function Upload-File($localPath, $remotePath) {
    try {
        $uri = [System.Uri]("ftp://$ftpHost/$remotePath")
        $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $ftpRequest.UseBinary = $true
        
        $fileContent = [System.IO.File]::ReadAllBytes($localPath)
        $ftpRequest.ContentLength = $fileContent.Length
        
        $requestStream = $ftpRequest.GetRequestStream()
        $requestStream.Write($fileContent, 0, $fileContent.Length)
        $requestStream.Close()
        Write-Host "Yuklendi: $remotePath" -ForegroundColor Green
    }
    catch {
        Write-Host "Hata ($remotePath): $($_.Exception.Message)" -ForegroundColor Red
    }
}

foreach ($file in $filesToUpload.Keys) {
    Upload-File "$sourceRoot\$file" $filesToUpload[$file]
}

Write-Host "LOGO GUNCELLEME (YENI ISIM) TAMAMLANDI!" -ForegroundColor Yellow
