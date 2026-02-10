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

Write-Host "V38 MODERN DESIGN DEPLOYMENT STARTING..." -ForegroundColor Cyan

# 1. Main Page (Uploading Clean v38 as index.php)
Publish-File "$sourceRoot\index_v38_clean.php" "index.php"

# 2. CSS (Uploading Minified v38 as style_v38.css because HTML links to that)
Publish-File "$sourceRoot\assets\css\style_v38.min.css" "assets/css/style_v38.css"

# 3. Logo
Publish-File "$sourceRoot\assets\images\logo_erguvan_2026.png" "assets/images/logo_erguvan_2026.png"

# 4. Team Images
Publish-File "$sourceRoot\assets\images\team\sena.jpg" "assets/images/team/sena.jpg"
Publish-File "$sourceRoot\assets\images\team\sedat.jpg" "assets/images/team/sedat.jpg"

# 5. Office Images
Publish-File "$sourceRoot\assets\images\office\ofis-1.jpg" "assets/images/office/ofis-1.jpg"
Publish-File "$sourceRoot\assets\images\office\ofis-2.jpg" "assets/images/office/ofis-2.jpg"
Publish-File "$sourceRoot\assets\images\office\ofis-3.jpg" "assets/images/office/ofis-3.jpg"
Publish-File "$sourceRoot\assets\images\office\ofis-4.jpg" "assets/images/office/ofis-4.jpg"

Write-Host "V38 DEPLOYMENT COMPLETE!" -ForegroundColor Yellow
