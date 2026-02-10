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

Write-Host "Baslatiliyor..." -ForegroundColor Cyan

# 1. Root Files
Publish-File "$sourceRoot\index_clean.php" "index.php"
Publish-File "$sourceRoot\config.php" "config.php"

# 2. Includes
Publish-File "$sourceRoot\includes\header.php" "includes/header.php"
Publish-File "$sourceRoot\includes\footer.php" "includes/footer.php"

# 3. Assets CSS (Using minified for both to avoid WAF)
Publish-File "$sourceRoot\assets\css\style.min.css" "assets/css/style.css"
Publish-File "$sourceRoot\assets\css\style.min.css" "assets/css/style.min.css" 

# 4. Assets JS
Publish-File "$sourceRoot\assets\js\script.js" "assets/js/script.js"

# 5. New Logo (Converted to WebP)
Publish-File "$sourceRoot\assets\images\logo.webp" "assets/images/logo.webp"

Write-Host "DEPLOYMENT TAMAMLANDI!" -ForegroundColor Yellow
