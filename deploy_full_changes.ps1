$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$sourceRoot = "d:\Erguvan antigravity hosting"

function Upload-File($localPath, $remotePath) {
    try {
        $uri = [System.Uri]("ftp://$ftpHost/$remotePath")
        $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $ftpRequest.UseBinary = $true
        
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

# --- CRITICAL FILES DEPLOYMENT ---

# 1. Root Files
Upload-File "$sourceRoot\index_clean.php" "index.php"
Upload-File "$sourceRoot\config.php" "config.php"

# 2. Includes
Upload-File "$sourceRoot\includes\header.php" "includes/header.php"
Upload-File "$sourceRoot\includes\footer.php" "includes/footer.php"

# 3. Assets CSS
Upload-File "$sourceRoot\assets\css\style.min.css" "assets/css/style.css"
Upload-File "$sourceRoot\assets\css\style.min.css" "assets/css/style.min.css" 

# 4. Assets JS
Upload-File "$sourceRoot\assets\js\script.js" "assets/js/script.js"

# 5. New Logo
Upload-File "$sourceRoot\assets\images\logo-modern.png" "assets/images/logo_new.png"

Write-Host "TUM DOSYALAR BASARIYLA YUKLENDI! SITEDE DEGISIKLIKLERI GORMEK ICIN CTRL+F5 YAPIN." -ForegroundColor Yellow
