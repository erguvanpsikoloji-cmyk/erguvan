$ftp_host = "ftp://erguvanpsikoloji.com"
$ftp_user = "erguvanpsi"
$ftp_pass = "92Mirza1"
$base_remotepath = "domains/erguvanpsikoloji.com/public_html"

function Invoke-FtpUpload {
    param($localPath, $remotePath)
    Write-Host "Uploading $localPath to $remotePath..."
    $uri = New-Object System.Uri("$ftp_host/$remotePath")
    $request = [System.Net.FtpWebRequest]::Create($uri)
    $request.Credentials = New-Object System.Net.NetworkCredential($ftp_user, $ftp_pass)
    $request.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    $request.UsePassive = $true
    $request.KeepAlive = $false

    try {
        $fileContent = [System.IO.File]::ReadAllBytes($localPath)
        $request.ContentLength = $fileContent.Length
        $requestStream = $request.GetRequestStream()
        $requestStream.Write($fileContent, 0, $fileContent.Length)
        $requestStream.Close()
        $response = $request.GetResponse()
        $response.Close()
        Write-Host "Success: $localPath" -ForegroundColor Green
    }
    catch {
        $err = $_.Exception.Message
        Write-Host "Failed: $localPath. Error: ${err}" -ForegroundColor Red
    }
}

$base = "c:\Users\ceren\Desktop\erguvan son"

Invoke-FtpUpload -localPath "$base\index.php" -remotePath "$base_remotepath/index.php"
Invoke-FtpUpload -localPath "$base\setup_testimonials.php" -remotePath "$base_remotepath/setup_testimonials.php"
Invoke-FtpUpload -localPath "$base\admin\pages\testimonials.php"    -remotePath "$base_remotepath/admin/pages/testimonials.php"
Invoke-FtpUpload -localPath "$base\admin\pages\testimonial-add.php" -remotePath "$base_remotepath/admin/pages/testimonial-add.php"
Invoke-FtpUpload -localPath "$base\admin\pages\testimonial-edit.php" -remotePath "$base_remotepath/admin/pages/testimonial-edit.php"

Write-Host "v78 Deployment complete (Testimonials Admin Fix)." -ForegroundColor Cyan
