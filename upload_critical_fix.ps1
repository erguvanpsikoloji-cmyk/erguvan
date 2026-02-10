$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$localFile = "d:\Erguvan antigravity hosting\database\db.php"
$remoteFile = "/domains/erguvanpsikoloji.com/public_html/database/db.php"

try {
    Write-Host "Kritik dosya yukleniyor: $localFile -> $remoteFile"
    $uri = [System.Uri]("ftp://$ftpHost/$remoteFile")
    $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
    $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    $ftpRequest.UseBinary = $true
    
    $fileContent = [System.IO.File]::ReadAllBytes($localFile)
    $ftpRequest.ContentLength = $fileContent.Length
    
    $requestStream = $ftpRequest.GetRequestStream()
    $requestStream.Write($fileContent, 0, $fileContent.Length)
    $requestStream.Close()
    
    Write-Host "BASARILI: db.php sunucuya yuklendi!" -ForegroundColor Green
}
catch {
    Write-Host "HATA: $($_.Exception.Message)" -ForegroundColor Red
}
