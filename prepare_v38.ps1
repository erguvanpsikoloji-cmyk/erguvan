# Clean index_v38.php
(Get-Content "d:\Erguvan antigravity hosting\index_v38.php") -replace '//.*', '' -replace '/\*[\s\S]*?\*/', '' | Set-Content "d:\Erguvan antigravity hosting\index_v38_clean.php"

# Minify style_v38.css
(Get-Content "d:\Erguvan antigravity hosting\assets\css\style_v38.css") -replace '/\*[\s\S]*?\*/', '' | ForEach-Object { $_.Trim() } | Where-Object { $_ -ne '' } | Set-Content "d:\Erguvan antigravity hosting\assets\css\style_v38.min.css"
