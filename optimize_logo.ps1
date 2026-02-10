Add-Type -AssemblyName System.Drawing

$sourcePath = "d:\Erguvan antigravity hosting\assets\images\logo_icon.png"
$backupPath = "d:\Erguvan antigravity hosting\assets\images\logo_icon_backup.png"

if (-not (Test-Path $sourcePath)) {
    Write-Host "Error: Source file not found."
    exit
}

# Backup original
Copy-Item $sourcePath $backupPath -Force

$img = [System.Drawing.Image]::FromFile($sourcePath)
Write-Host "Original Dimensions: $($img.Width) x $($img.Height)"

# Target Height (130px for Retina 2x of 65px)
$targetHeight = 130

if ($img.Height -gt $targetHeight) {
    $ratio = $targetHeight / $img.Height
    $targetWidth = [int]($img.Width * $ratio)

    $newImg = New-Object System.Drawing.Bitmap($targetWidth, $targetHeight)
    $graph = [System.Drawing.Graphics]::FromImage($newImg)
    $graph.CompositingQuality = [System.Drawing.Drawing2D.CompositingQuality]::HighQuality
    $graph.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $graph.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality

    $graph.DrawImage($img, 0, 0, $targetWidth, $targetHeight)
    
    $img.Dispose() # Release original file handle
    
    $newImg.Save($sourcePath, [System.Drawing.Imaging.ImageFormat]::Png)
    $newImg.Dispose()
    $graph.Dispose()
    
    Write-Host "Optimized to: $targetWidth x $targetHeight"
}
else {
    Write-Host "Image is already small enough."
    $img.Dispose()
}

$originalSize = (Get-Item $backupPath).Length
$newSize = (Get-Item $sourcePath).Length

Write-Host "Original Size: $originalSize bytes"
Write-Host "New Size: $newSize bytes"
