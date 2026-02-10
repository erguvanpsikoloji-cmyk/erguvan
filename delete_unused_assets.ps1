$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"

function Delete-FtpFile($path) {
    try {
        $uri = [System.Uri]("ftp://$ftpHost/$path")
        $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::DeleteFile
        
        $response = $ftpRequest.GetResponse()
        Write-Host "Deleted: $path" -ForegroundColor Green
        $response.Close()
    }
    catch {
        Write-Host "Error deleting ${path}: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Delete large unused files found in listing
Delete-FtpFile "assets/images/office-1.png"
Delete-FtpFile "assets/images/service-card-bg.png"
Delete-FtpFile "assets/images/logo.png"

# Delete hero images if unused (likely safe based on code review)
Delete-FtpFile "assets/images/hero-1.png"
Delete-FtpFile "assets/images/hero-2.png"
Delete-FtpFile "assets/images/hero-3.png"
