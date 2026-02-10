$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$sourceRoot = "d:\Erguvan antigravity hosting"

try {
    $uri = [System.Uri]("ftp://$ftpHost/test.txt")
    $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
    $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    
    $fileContent = [System.IO.File]::ReadAllBytes("$sourceRoot\test.txt")
    $ftpRequest.ContentLength = $fileContent.Length
    
    $requestStream = $ftpRequest.GetRequestStream()
    $requestStream.Write($fileContent, 0, $fileContent.Length)
    $requestStream.Close()
    Write-Host "TEST UPLOAD SUCCESSFUL" -ForegroundColor Green
}
catch {
    Write-Host "TEST UPLOAD FAILED: $($_.Exception.Message)" -ForegroundColor Red
}
