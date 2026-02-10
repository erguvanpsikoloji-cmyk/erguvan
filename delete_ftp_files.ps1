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

# Delete the largest file found
Delete-FtpFile "ERGUVAN_EXPORT/modern_assets.zip"
Delete-FtpFile "ERGUVAN_EXPORT/kurulum_hizli_v2.php"
Delete-FtpFile "ERGUVAN_EXPORT/kurulum_hizli.php"
Delete-FtpFile "ERGUVAN_EXPORT/temp_style.txt"
Delete-FtpFile "ERGUVAN_EXPORT/index_old_design.php"
