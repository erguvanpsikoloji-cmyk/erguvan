$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"

try {
    $uri = [System.Uri]("ftp://$ftpHost/passive_test.txt")
    $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
    $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    $ftpRequest.UsePassive = $true  # PASIF MOD ZORLA
    
    $content = "Passive test success at " + (Get-Date).ToString()
    $bytes = [System.Text.Encoding]::UTF8.GetBytes($content)
    
    $requestStream = $ftpRequest.GetRequestStream()
    $requestStream.Write($bytes, 0, $bytes.Length)
    $requestStream.Close()
    Write-Host "PASIF MOD TESTI BASARILI!" -ForegroundColor Green
}
catch {
    Write-Host "PASIF MOD TESTI HATASI: $($_.Exception.Message)" -ForegroundColor Red
}
