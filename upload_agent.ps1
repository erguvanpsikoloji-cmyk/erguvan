$ftpServer = "ftp.erguvanpsikoloji.com"
$username = "erguvanpsi"
$password = "92Mirza1"
$localFile = Join-Path (Get-Location) "admin\test.php"
$remoteFile = "ftp://$ftpServer/admin/test.php"

Write-Host "Overwriting: test.php ..."
$webClient = New-Object System.Net.WebClient
$webClient.Credentials = New-Object System.Net.NetworkCredential($username, $password)
try {
    $webClient.UploadFile($remoteFile, "STOR", $localFile)
    Write-Host "Basarili!"
}
catch {
    Write-Host "HATA: $_"
}
