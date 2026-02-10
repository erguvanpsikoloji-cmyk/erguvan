# Clean index.php
(Get-Content "d:\Erguvan antigravity hosting\index.php") -replace '//.*', '' -replace '/\*[\s\S]*?\*/', '' | Set-Content "d:\Erguvan antigravity hosting\index_clean.php"

# Minify style.css
(Get-Content "d:\Erguvan antigravity hosting\assets\css\style.css") -replace '/\*[\s\S]*?\*/', '' | ForEach-Object { $_.Trim() } | Where-Object { $_ -ne '' } | Set-Content "d:\Erguvan antigravity hosting\assets\css\style.min.css"
