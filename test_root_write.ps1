$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"

try {
    # Kok dizine yukle
    $uri = [System.Uri]("ftp://$ftpHost/root_write_test.txt")
    $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
    $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    
    $content = "Root write test success at " + (Get-Date).ToString()
    $bytes = [System.Text.Encoding]::UTF8.GetBytes($content)
    
    $requestStream = $ftpRequest.GetRequestStream()
    $requestStream.Write($bytes, 0, $bytes.Length)
    $requestStream.Close()
    Write-Host "KOK DIZIN TESTI BASARILI!" -ForegroundColor Green
}
catch {
    Write-Host "KOK DIZIN TESTI HATASI: $($_.Exception.Message)" -ForegroundColor Red
}
