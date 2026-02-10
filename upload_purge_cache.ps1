$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$localPath = "c:\Users\ceren\Desktop\erguvan son\purge_cache.php"
$remotePath = "purge_cache.php"

try {
    Write-Host "Connecting to FTP..." -ForegroundColor Cyan
    $uri = [System.Uri]("ftp://$ftpHost/$remotePath")
    $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
    $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    $ftpRequest.UseBinary = $true
    $ftpRequest.KeepAlive = $false
    
    if (-not (Test-Path $localPath)) {
        Write-Host "ERROR: Local file not found: $localPath" -ForegroundColor Red
        exit
    }

    Write-Host "Reading local file..." -ForegroundColor Cyan
    $fileContent = [System.IO.File]::ReadAllBytes($localPath)
    $ftpRequest.ContentLength = $fileContent.Length
    
    Write-Host "Uploading to $remotePath..." -ForegroundColor Cyan
    $requestStream = $ftpRequest.GetRequestStream()
    $requestStream.Write($fileContent, 0, $fileContent.Length)
    $requestStream.Close()
    
    Write-Host "SUCCESS: Uploaded $remotePath" -ForegroundColor Green
}
catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
}
