$ftp_host = "ftp://erguvanpsikoloji.com"
$ftp_user = "erguvanpsi"
$ftp_pass = "92Mirza1"

$remoteFile = "domains/erguvanpsikoloji.com/public_html/convert_office.php"

function Delete-File {
    param($remotePath)
    $uri = New-Object System.Uri("$ftp_host/$remotePath")
    $request = [System.Net.FtpWebRequest]::Create($uri)
    $request.Credentials = New-Object System.Net.NetworkCredential($ftp_user, $ftp_pass)
    $request.Method = [System.Net.WebRequestMethods+Ftp]::DeleteFile

    try {
        $response = $request.GetResponse()
        $response.Close()
        Write-Host "Success: Deleted $remotePath" -ForegroundColor Green
    }
    catch {
        Write-Host "Error deleting $remotePath : $($_.Exception.Message)" -ForegroundColor Red
    }
}

Delete-File -remotePath $remoteFile
