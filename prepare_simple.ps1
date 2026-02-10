# 1. Prepare Logo (Rename to simple)
Copy-Item "d:\Erguvan antigravity hosting\assets\images\logo_erguvan_2026.png" "d:\Erguvan antigravity hosting\assets\images\logo2026.png" -Force

# 2. Prepare CSS (Simple name)
Copy-Item "d:\Erguvan antigravity hosting\assets\css\style_v38.min.css" "d:\Erguvan antigravity hosting\assets\css\style2.css" -Force

# 3. Prepare Index (Update refs and remove comments)
$content = Get-Content "d:\Erguvan antigravity hosting\index_v38.php" -Raw
$content = $content -replace "assets/images/logo_erguvan_2026.png", "assets/images/logo2026.png"
$content = $content -replace "assets/css/style_v38.css", "assets/css/style2.css"
# Remove comments
$content = $content -replace '//.*', '' -replace '/\*[\s\S]*?\*/', '' 
$content | Set-Content "d:\Erguvan antigravity hosting\index_simple.php"

Write-Host "Files prepared: logo2026.png, style2.css, index_simple.php"
