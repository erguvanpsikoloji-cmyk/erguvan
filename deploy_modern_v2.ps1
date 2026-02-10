$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$sourceRoot = "d:\Erguvan antigravity hosting"

# Klasörler ve Hedefler
# Yerel Klasör -> Uzak Klasör
$syncMap = @{
    "psikolog-modern/assets" = "assets/images"
}

$filesToUpload = @{
    "index.php"                   = "index.php"
    "assets/css/modern-style.css" = "assets/css/modern-style.css"
}

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

# 1. Dosyaları yükle
foreach ($file in $filesToUpload.Keys) {
    Upload-File "$sourceRoot\$file" $filesToUpload[$file]
}

# 2. Modern assets görsellerini yükle
foreach ($localDir in $syncMap.Keys) {
    $remoteDirRoot = $syncMap[$localDir]
    Write-Host "Gorseller yukleniyor: $localDir -> $remoteDirRoot" -ForegroundColor Cyan
    $items = Get-ChildItem -Path "$sourceRoot\$localDir" -Recurse -File
    foreach ($item in $items) {
        # Sadece dosya adını ve alt klasör yapısını al (psikolog-modern/assets kısmını atarak)
        $relativePath = $item.FullName.Substring(("$sourceRoot\$localDir").Length + 1).Replace("\", "/")
        $remoteFinalPath = "$remoteDirRoot/$relativePath"
        
        # Uzak klasör yapısını oluştur
        $pathParts = $remoteFinalPath.Split("/")
        $currentPath = ""
        for ($i = 0; $i -lt ($pathParts.Length - 1); $i++) {
            if ($currentPath -eq "") { $currentPath = $pathParts[$i] }
            else { $currentPath = "$currentPath/$pathParts[$i]" }
            Create-FtpDirectory "$currentPath"
        }
        
        Upload-File $item.FullName $remoteFinalPath
    }
}

Write-Host "MODERN TASARIM AKTARIMI TAMAMLANDI!" -ForegroundColor Yellow
