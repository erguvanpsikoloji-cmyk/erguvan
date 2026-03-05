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

    try {
        $fileContent = [System.IO.File]::ReadAllBytes($localPath)
        $request.ContentLength = $fileContent.Length
        $requestStream = $request.GetRequestStream()
        $requestStream.Write($fileContent, 0, $fileContent.Length)
        $requestStream.Close()

        $response = $request.GetResponse()
        $response.Close()
        Write-Host "Success: Uploaded $localPath to $remotePath" -ForegroundColor Green
    }
    catch {
        Write-Host "Error: Could not upload $localPath to $remotePath. $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Upload optimized office images
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\assets\images\office\ofis-1.webp" -remotePath "$base_remotepath/assets/images/office/ofis-1.webp"
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\assets\images\office\ofis-2.webp" -remotePath "$base_remotepath/assets/images/office/ofis-2.webp"
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\assets\images\office\ofis-3.webp" -remotePath "$base_remotepath/assets/images/office/ofis-3.webp"
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\assets\images\office\ofis-4.webp" -remotePath "$base_remotepath/assets/images/office/ofis-4.webp"

# Upload optimized team images
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\assets\images\team\sedat.webp" -remotePath "$base_remotepath/assets/images/team/sedat.webp"
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\assets\images\team\sena.webp" -remotePath "$base_remotepath/assets/images/team/sena.webp"
Upload-File -localPath "c:\Users\ceren\Desktop\erguvan son\assets\images\team\ceren.webp" -remotePath "$base_remotepath/assets/images/team/ceren.webp"

Write-Host "Image optimization deployment complete."
