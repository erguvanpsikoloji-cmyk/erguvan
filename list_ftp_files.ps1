$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"

function Get-FtpListing($path) {
    try {
        $uri = [System.Uri]("ftp://$ftpHost/$path")
        $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectoryDetails
        
        $response = $ftpRequest.GetResponse()
        $reader = New-Object System.IO.StreamReader($response.GetResponseStream())
        $listing = $reader.ReadToEnd()
        $reader.Close()
        $response.Close()
        
        Write-Host "--- Listing for /$path ---"
        Write-Host $listing
        Write-Host "------------------------"
    }
    catch {
        Write-Host "Error listing ${path}: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Get-FtpListing ""
Get-FtpListing "ERGUVAN_EXPORT"
