$ftp_host = "ftp://erguvanpsikoloji.com"
$ftp_user = "erguvanpsi"
$ftp_pass = "92Mirza1"
$base_remotepath = "domains/erguvanpsikoloji.com/public_html"

function Upload-File {
    param($localPath, $remotePath)
    $uri = New-Object System.Uri("$ftp_host/$remotePath")
    $request = [System.Net.FtpWebRequest]::Create($uri)
    $request.Credentials = New-Object System.Net.NetworkCredential($ftp_user, $ftp_pass)
    $request.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile

    $fileContent = [System.IO.File]::ReadAllBytes($localPath)
    $request.ContentLength = $fileContent.Length
    $requestStream = $request.GetRequestStream()
    $requestStream.Write($fileContent, 0, $fileContent.Length)
    $requestStream.Close()

    $response = $request.GetResponse()
    $response.Close()
    Write-Host "Success: Uploaded $localPath to $remotePath" -ForegroundColor Green
}

# Upload modified files
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\index.php" -remotePath "$base_remotepath/index.php"
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\includes\footer.php" -remotePath "$base_remotepath/includes/footer.php"
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\check_caps.php" -remotePath "$base_remotepath/check_caps.php"
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\converter.php" -remotePath "$base_remotepath/converter.php"
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\cleanup_temp.php" -remotePath "$base_remotepath/cleanup_temp.php"
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\perf_test.php" -remotePath "$base_remotepath/perf_test.php"
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\assets\css\main.css" -remotePath "$base_remotepath/assets/css/main.css"
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\pages\ofisimiz.php" -remotePath "$base_remotepath/pages/ofisimiz.php"
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\.htaccess" -remotePath "$base_remotepath/.htaccess"

Write-Host "Deployment complete."
