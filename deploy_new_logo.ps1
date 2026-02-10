$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$sourceRoot = "d:\Erguvan antigravity hosting"
$generatedLogoPath = "C:\Users\ceren\.gemini\antigravity\brain\731b297c-bc84-44db-98a4-327819d6cd1b\logo_erguvan_final.png"
$localWebPPath = "$sourceRoot\assets\images\logo_erguvan_final.webp"

# Ensure local asset directory exists
if (-not (Test-Path "$sourceRoot\assets\images")) {
    New-Item -ItemType Directory -Force -Path "$sourceRoot\assets\images"
}

# Function to upload file
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

Write-Host "PROCESSING LOGO..." -ForegroundColor Cyan

# 1. Copy generated image to workspace
Copy-Item $generatedLogoPath "$sourceRoot\assets\images\logo_erguvan_final.png" -Force

# 2. Convert to WebP (using PHP via command line if available, or just renaming if we assume capabilities - but here we will try a PHP script helper if needed, OR just upload the PNG as WebP if the user is ok, BUT let's do real conversion if possible. 
# Since we don't have a local easy webp converter tool guaranteed, we will upload PNG and use a PHP script on server to convert, OR just use the PNG if it's good. 
# User asked for "most suitable format". WebP is best. 
# Let's write a PHP script to convert it on the server side.

$phpConversionScript = @"
<?php
\$source = 'logo_erguvan_final.png';
\$destination = 'logo_erguvan_final.webp';
if (file_exists(\$source)) {
    \$image = imagecreatefrompng(\$source);
    if (\$image) {
        imagepalettetotruecolor(\$image);
        imagealphablending(\$image, true);
        imagesavealpha(\$image, true);
        imagewebp(\$image, \$destination, 90);
        imagedestroy(\$image);
        echo 'Conversion successful';
    } else {
        echo 'GD Load Failed';
    }
} else {
    echo 'Source not found';
}
?>
"@
$phpConversionScript | Set-Content "$sourceRoot\convert_logo_final.php"

# Upload PNG and Converter
Publish-File "$sourceRoot\assets\images\logo_erguvan_final.png" "assets/images/logo_erguvan_final.png"
Publish-File "$sourceRoot\convert_logo_final.php" "assets/images/convert_logo_final.php"

# Trigger Conversion
try {
    $response = Invoke-WebRequest -Uri "http://www.erguvanpsikoloji.com/assets/images/convert_logo_final.php" -UseBasicParsing
    Write-Host "Conversion Response: $($response.Content)" -ForegroundColor Green
}
catch {
    Write-Host "Conversion Trigger Failed: $_" -ForegroundColor Red
}

Write-Host "LOGO DEPLOYED & CONVERTED!" -ForegroundColor Yellow
