$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$sourceRoot = "d:\Erguvan antigravity hosting"

# Yeni hedef klasor
$remoteTargetDir = "assets/images/modern_design"

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

# Hedef klasoru olustur
Create-FtpDirectory "assets"
Create-FtpDirectory "assets/images"
Create-FtpDirectory $remoteTargetDir

# psikolog-modern/assets icindeki dosyalari yukle
$items = Get-ChildItem -Path "$sourceRoot\psikolog-modern\assets" -Recurse -File
foreach ($item in $items) {
    # Relatif yolu al
    $relativePath = $item.FullName.Substring(("$sourceRoot\psikolog-modern\assets").Length + 1).Replace("\", "/")
    $remoteFinalPath = "$remoteTargetDir/$relativePath"
    
    # Alt klasorleri olustur
    $pathParts = $remoteFinalPath.Split("/")
    $currentPath = ""
    for ($i = 0; $i -lt ($pathParts.Length - 1); $i++) {
        if ($currentPath -eq "") { $currentPath = $pathParts[$i] }
        else { $currentPath = "$currentPath/$pathParts[$i]" }
        Create-FtpDirectory "$currentPath"
    }
    
    Upload-File $item.FullName $remoteFinalPath
}

Write-Host "MODERN ASSETS AYRI KLASORE YUKLENDI!" -ForegroundColor Yellow
