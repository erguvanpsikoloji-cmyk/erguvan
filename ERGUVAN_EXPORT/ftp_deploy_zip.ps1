
$ftpHost = "ftp://erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$filesToUpload = @("d:\ceren antigravity web site\deploy_package.zip", "d:\ceren antigravity web site\unzip_deploy.php")

foreach ($filePath in $filesToUpload) {
    $fileName = Split-Path $filePath -Leaf
    Write-Output "Uploading: $fileName"
    
    $uri = New-Object System.Uri($ftpHost + "/" + $fileName)
    $webClient = New-Object System.Net.WebClient
    $webClient.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
    
    try {
        $webClient.UploadFile($uri, $filePath)
        Write-Output "Uploaded: $fileName"
    }
    catch {
        Write-Error "Failed to upload $($fileName): $_"
    }
}
Write-Output "Zip Deployment Upload Complete."
