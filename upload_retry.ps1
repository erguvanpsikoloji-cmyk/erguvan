# Retry Upload sifre_sifirla.php
$LocalFile = "c:\Users\ceren\Desktop\erguvan son\sifre_sifirla.php"
$RemoteFile = "sifre_sifirla.php"
$ftpUrl = "ftp://ftp.erguvanpsikoloji.com/$RemoteFile"
$username = "mirza@erguvanpsikoloji.com"
$password = "92Mirza1!"

try {
    $webclient = New-Object System.Net.WebClient
    $webclient.Credentials = New-Object System.Net.NetworkCredential($username, $password)
    Write-Host "Uploading..."
    $webclient.UploadFile($ftpUrl, $LocalFile)
    Write-Host "✅ Upload SUCCEEDED!"
}
catch {
    Write-Host "❌ Upload FAILED: $($_.Exception.Message)"
}
