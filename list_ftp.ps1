$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"

try {
    $uri = [System.Uri]("ftp://$ftpHost/")
    $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
    $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectoryDetails
    
    $response = $ftpRequest.GetResponse()
    $reader = New-Object System.IO.StreamReader($response.GetResponseStream())
    $list = $reader.ReadToEnd()
    $reader.Close()
    $response.Close()
    
    Write-Host "FTP DOSYA LISTESI:" -ForegroundColor Cyan
    Write-Host $list
}
catch {
    Write-Host "LISTELEME HATASI: $($_.Exception.Message)" -ForegroundColor Red
}
