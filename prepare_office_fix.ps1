# Rename files locally to simpler names (no hyphens)
Copy-Item "d:\Erguvan antigravity hosting\assets\images\office\ofis-2.jpg" "d:\Erguvan antigravity hosting\assets\images\office\office2.jpg" -Force
Copy-Item "d:\Erguvan antigravity hosting\assets\images\office\ofis-3.jpg" "d:\Erguvan antigravity hosting\assets\images\office\office3.jpg" -Force
Copy-Item "d:\Erguvan antigravity hosting\assets\images\office\ofis-4.jpg" "d:\Erguvan antigravity hosting\assets\images\office\office4.jpg" -Force

# Update index_simple.php to use new names
$content = Get-Content "d:\Erguvan antigravity hosting\index_simple.php" -Raw
$content = $content -replace "assets/images/office/ofis-2.jpg", "assets/images/office/office2.jpg"
$content = $content -replace "assets/images/office/ofis-3.jpg", "assets/images/office/office3.jpg"
$content = $content -replace "assets/images/office/ofis-4.jpg", "assets/images/office/office4.jpg"
$content | Set-Content "d:\Erguvan antigravity hosting\index_simple_fix.php"

Write-Host "Files renamed and index updated."
