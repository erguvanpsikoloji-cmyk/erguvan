
$ftpHost = "ftp://erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$localFile = "d:\ceren antigravity web site\unzip_status.php"
$remoteFile = "unzip_status.php"

Write-Output "Connecting to $ftpHost..."
$uri = New-Object System.Uri($ftpHost + "/" + $remoteFile)
$webClient = New-Object System.Net.WebClient
$webClient.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)

try {
    Write-Output "Uploading $remoteFile..."
    $webClient.UploadFile($uri, $localFile)
    Write-Output "Upload Success!"
}
catch {
    Write-Error "Upload Failed: $_"
}
