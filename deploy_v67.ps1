$ftp_host = "ftp://erguvanpsikoloji.com"
$ftp_user = "erguvanpsi"
$ftp_pass = "92Mirza1"
$base_remotepath = "domains/erguvanpsikoloji.com/public_html"

function Upload-File {
    param($localPath, $remotePath)
    Write-Host "Uploading $localPath to $remotePath..."
    $uri = New-Object System.Uri("$ftp_host/$remotePath")
    $request = [System.Net.FtpWebRequest]::Create($uri)
    $request.Credentials = New-Object System.Net.NetworkCredential($ftp_user, $ftp_pass)
    $request.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    $request.UsePassive = $true
    $request.KeepAlive = $false

    try {
        $fileContent = [System.IO.File]::ReadAllBytes($localPath)
        $request.ContentLength = $fileContent.Length
        $requestStream = $request.GetRequestStream()
        $requestStream.Write($fileContent, 0, $fileContent.Length)
        $requestStream.Close()
        $response = $request.GetResponse()
        $response.Close()
        Write-Host "Success: $localPath" -ForegroundColor Green
    }
    catch {
        Write-Host "Failed: $localPath : $_" -ForegroundColor Red
    }
}

Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\config.php" -remotePath "$base_remotepath/config.php"
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\index.php" -remotePath "$base_remotepath/index.php"
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\assets\images\logo_erguvan_new.svg" -remotePath "$base_remotepath/assets/images/logo_erguvan_new.svg"

Write-Host "v67 Deployment complete (Logo 1 - SVG Version)."
