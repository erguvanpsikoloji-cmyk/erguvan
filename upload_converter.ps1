$ftp_host = "ftp://erguvanpsikoloji.com"
$ftp_user = "erguvanpsi"
$ftp_pass = "92Mirza1"

$localFile = "C:\Users\ceren\Desktop\erguvan son\server_convert.php"
$remoteFile = "domains/erguvanpsikoloji.com/public_html/server_convert.php"

function Upload-File {
    param($localPath, $remotePath)
    $uri = New-Object System.Uri("$ftp_host/$remotePath")
    $request = [System.Net.FtpWebRequest]::Create($uri)
    $request.Credentials = New-Object System.Net.NetworkCredential($ftp_user, $ftp_pass)
    $request.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile

    try {
        $fileBytes = [System.IO.File]::ReadAllBytes($localPath)
        $requestStream = $request.GetRequestStream()
        $requestStream.Write($fileBytes, 0, $fileBytes.Length)
        $requestStream.Close()
        Write-Host "Success: $remotePath" -ForegroundColor Green
        return $true
    }
    catch {
        Write-Host "Error uploading $remotePath : $($_.Exception.Message)" -ForegroundColor Red
        return $false
    }
}

Upload-File -localPath $localFile -remotePath $remoteFile
