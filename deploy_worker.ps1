$creds = New-Object System.Net.NetworkCredential('cerenpsikolog', 'ceren123*')
$client = New-Object System.Net.WebClient
$client.Credentials = $creds

$files = @(
    @{ local = "D:\Erguvan antigravity hosting\assets\css\style.min.css"; remote = "ftp://104.247.162.226/public_html/assets/css/style.min.css" },
    @{ local = "D:\Erguvan antigravity hosting\index.php"; remote = "ftp://104.247.162.226/public_html/index.php" }
)

foreach ($file in $files) {
    Write-Host "Uploading: $($file.local) -> $($file.remote)"
    try {
        $client.UploadFile($file.remote, $file.local)
        Write-Host "Success!" -ForegroundColor Green
    }
    catch {
        Write-Host "Failed: $($_.Exception.Message)" -ForegroundColor Red
    }
}
