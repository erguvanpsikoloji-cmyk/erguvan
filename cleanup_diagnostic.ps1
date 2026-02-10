$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$filesToDelete = @("diagnostic.php", "purge_cache.php")

foreach ($file in $filesToDelete) {
    try {
        $uri = [System.Uri]("ftp://$ftpHost/$file")
        $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::DeleteFile
        $ftpRequest.GetResponse()
        Write-Host "Deleted: $file" -ForegroundColor Green
    }
    catch {
        Write-Host "Error deleting $file : $($_.Exception.Message)" -ForegroundColor Red
    }
}
