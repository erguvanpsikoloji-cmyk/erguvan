$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$sourceRoot = "d:\Erguvan antigravity hosting"

function Publish-File($localPath, $remotePath) {
    try {
        $uri = [System.Uri]("ftp://$ftpHost/$remotePath")
        $servicePoint = [System.Net.ServicePointManager]::FindServicePoint($uri)
        $servicePoint.ConnectionLimit = 4 
        
        $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $ftpRequest.UseBinary = $true
        $ftpRequest.KeepAlive = $false
        
        if (-not (Test-Path $localPath)) {
            Write-Host "HATA: Yerel dosya bulunamadi: $localPath" -ForegroundColor Red
            return
        }

        $fileContent = [System.IO.File]::ReadAllBytes($localPath)
        $ftpRequest.ContentLength = $fileContent.Length
        
        $requestStream = $ftpRequest.GetRequestStream()
        $requestStream.Write($fileContent, 0, $fileContent.Length)
        $requestStream.Close()
        Write-Host "Yuklendi: $remotePath" -ForegroundColor Green
    }
    catch {
        Write-Host "Hata ($remotePath): $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host "DEPLOYING VISUAL FIXES..." -ForegroundColor Cyan

# 1. CSS (Updated)
Publish-File "$sourceRoot\assets\css\style2.css" "assets/css/style2.css"

# 2. Main Page (Updated with inline styles)
Publish-File "$sourceRoot\index_simple.php" "index.php"

# 3. Logo (Just in case)
Publish-File "$sourceRoot\assets\images\logo2026.png" "assets/images/logo2026.png"

Write-Host "VISUAL FIXES DEPLOYED!" -ForegroundColor Yellow
