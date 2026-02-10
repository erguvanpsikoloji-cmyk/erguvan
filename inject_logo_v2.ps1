$b64 = [System.IO.File]::ReadAllText("d:\Erguvan antigravity hosting\logo_b64.txt")
$url = "https://www.erguvanpsikoloji.com/admin/pages/upload-handler.php"

# Direkt body olarak gönder
Invoke-RestMethod -Uri $url -Method Post -Body $b64 -ContentType "text/plain"
