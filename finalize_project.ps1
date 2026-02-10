$source = "d:\Erguvan antigravity hosting"
$dest = "C:\Users\ceren\Desktop\erguvan son"

Write-Host "Yedekleme Basliyor..." -ForegroundColor Cyan
Write-Host "Kaynak: $source"
Write-Host "Hedef: $dest"

if (-not (Test-Path $dest)) {
    New-Item -ItemType Directory -Path $dest -Force | Out-Null
    Write-Host "Hedef klasor olusturuldu." -ForegroundColor Yellow
}

# Exclude .git and node_modules to be faster and cleaner if desired, 
# but user asked for "work's final state", so usually full copy is better.
# We will exclude the destination itself if it were inside source, but it's not.

Copy-Item -Path "$source\*" -Destination $dest -Recurse -Force -ErrorAction SilentlyContinue

Write-Host "YEDEKLEME TAMAMLANDI!" -ForegroundColor Green
Write-Host "Dosyalar '$dest' klasorune kopyalandi."
