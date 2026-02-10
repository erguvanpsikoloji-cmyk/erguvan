# FTP Upload Script - Admin Panel Fix
# Uploads admin panel security fixes to live server

$ftpServer = "ftp.erguvanpsikoloji.com"
$ftpUsername = "u235795654"  # FTP username from previous scripts
$ftpPassword = Read-Host "FTP Password" -AsSecureString
$ftpPasswordPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($ftpPassword))

# Files to upload
$files = @(
    @{Local = "admin\login.php"; Remote = "/domains/erguvanpsikoloji.com/public_html/admin/login.php" },
    @{Local = "admin\includes\csrf.php"; Remote = "/domains/erguvanpsikoloji.com/public_html/admin/includes/csrf.php" }
)

Write-Host "=== Admin Panel Security Fix Deployment ===" -ForegroundColor Cyan
Write-Host ""

foreach ($file in $files) {
    $localPath = Join-Path "c:\Users\ceren\Desktop\erguvan son" $file.Local
    $remotePath = $file.Remote
    
    if (!(Test-Path $localPath)) {
        Write-Host "❌ File not found: $localPath" -ForegroundColor Red
        continue
    }
    
    Write-Host "📤 Uploading: $($file.Local)" -ForegroundColor Yellow
    
    try {
        # Create FTP request
        $ftpUri = "ftp://$ftpServer$remotePath"
        $request = [System.Net.FtpWebRequest]::Create($ftpUri)
        $request.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $request.Credentials = New-Object System.Net.NetworkCredential($ftpUsername, $ftpPasswordPlain)
        $request.UseBinary = $true
        $request.UsePassive = $true
        
        # Read file content
        $fileContent = [System.IO.File]::ReadAllBytes($localPath)
        $request.ContentLength = $fileContent.Length
        
        # Upload
        $requestStream = $request.GetRequestStream()
        $requestStream.Write($fileContent, 0, $fileContent.Length)
        $requestStream.Close()
        
        # Get response
        $response = $request.GetResponse()
        Write-Host "✅ Uploaded successfully: $remotePath" -ForegroundColor Green
        $response.Close()
    }
    catch {
        Write-Host "❌ Upload failed: $_" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "=== Deployment Complete ===" -ForegroundColor Cyan
Write-Host "Test admin panel at: https://www.erguvanpsikoloji.com/admin/login.php`?bypass=1" -ForegroundColor Green
