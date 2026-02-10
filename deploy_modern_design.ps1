$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$sourceRoot = "d:\Erguvan antigravity hosting"

# Yüklenmesi gereken modern tasarım dosyaları
$filesToUpload = @(
    "index.php",
    "assets/css/modern-style.css"
)

# Klasörler (Görseller dahil)
$dirsToUpload = @(
    "psikolog-modern/assets"
)

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

function Create-FtpDirectory($remoteDirPath) {
    try {
        $uri = [System.Uri]("ftp://$ftpHost/$remoteDirPath/")
        $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
        $response = $ftpRequest.GetResponse()
        $response.Close()
    }
    catch { }
}

# 1. Root ve CSS dosyalarını yükle
foreach ($file in $filesToUpload) {
    Upload-File "$sourceRoot\$file" "$file"
}

# 2. Modern assets klasöründeki görselleri yükle
foreach ($dir in $dirsToUpload) {
    Write-Host "Modern Assets yukleniyor: $dir" -ForegroundColor Cyan
    $items = Get-ChildItem -Path "$sourceRoot\$dir" -Recurse -File
    foreach ($item in $items) {
        $relativePath = $item.FullName.Substring($sourceRoot.Length + 1).Replace("\", "/")
        
        # Uzak klasör yapısını oluştur
        $pathParts = $relativePath.Split("/")
        $currentPath = ""
        for ($i = 0; $i -lt ($pathParts.Length - 1); $i++) {
            if ($currentPath -eq "") { $currentPath = $pathParts[$i] }
            else { $currentPath = "$currentPath/$pathParts[$i]" }
            Create-FtpDirectory "$currentPath"
        }
        
        Upload-File $item.FullName $relativePath
    }
}

Write-Host "MODERN TASARIM YAYINLANDI!" -ForegroundColor Yellow
