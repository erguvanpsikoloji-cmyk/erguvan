$ftp_host = "ftp://erguvanpsikoloji.com"
$ftp_user = "erguvanpsi"
$ftp_pass = "92Mirza1"
$remote_path = "domains/erguvanpsikoloji.com/public_html/setup_testimonials.php"

$uri = New-Object System.Uri("$ftp_host/$remote_path")
$request = [System.Net.FtpWebRequest]::Create($uri)
$request.Credentials = New-Object System.Net.NetworkCredential($ftp_user, $ftp_pass)
$request.Method = [System.Net.WebRequestMethods+Ftp]::DeleteFile
$request.UsePassive = $true

try {
    $response = $request.GetResponse()
    $response.Close()
    Write-Host "setup_testimonials.php silindi." -ForegroundColor Green
}
catch {
    $err = $_.Exception.Message
    Write-Host "Silme basarisiz: ${err}" -ForegroundColor Red
}
