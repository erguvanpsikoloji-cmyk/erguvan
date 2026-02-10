# FTP Upload Script - Alternative Method
param(
    [string]$LocalFile = "c:\Users\ceren\Desktop\erguvan son\admin\login.php",
    [string]$RemoteFile = "admin/login.php"
)

try {
    $ftpUrl = "ftp://ftp.erguvanpsikoloji.com/$RemoteFile"
    $username = "mirza@erguvanpsikoloji.com"
    $password = "92Mirza1!"
    
    $webclient = New-Object System.Net.WebClient
    $webclient.Credentials = New-Object System.Net.NetworkCredential($username, $password)
    
    Write-Host "Uploading $LocalFile to $ftpUrl..." -ForegroundColor Yellow
    $webclient.UploadFile($ftpUrl, $LocalFile)
    Write-Host "✅ Upload successful!" -ForegroundColor Green
    
    $webclient.Dispose()
}
catch {
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
}
