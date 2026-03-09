$ftp_server = "94.73.147.96"
$ftp_user = "erguvanpsikoloji.com"
$ftp_pass = "Sercen.1903"
$remote_path = "/httpdocs/"
$local_path = "c:\Users\ceren\Desktop\erguvan son\"

$files_to_upload = @("index.php", "config.php")

$webclient = New-Object System.Net.WebClient
$webclient.Credentials = New-Object System.Net.NetworkCredential($ftp_user, $ftp_pass)

foreach ($file in $files_to_upload) {
    $local_file = Join-Path $local_path $file
    $remote_file = "ftp://$ftp_server" + $remote_path + $file
    Write-Host "Uploading ${file} to ${remote_file}..."
    try {
        $webclient.UploadFile($remote_file, $local_file)
        Write-Host "Successfully uploaded ${file}"
    }
    catch {
        $err = $_.Exception.Message
        Write-Host "Failed to upload ${file}. Error: ${err}"
    }
}

Write-Host "Deployment v76 complete!"
