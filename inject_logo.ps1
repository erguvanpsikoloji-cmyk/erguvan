$b64 = [System.IO.File]::ReadAllText("d:\Erguvan antigravity hosting\logo_b64.txt")
$url = "https://www.erguvanpsikoloji.com/admin/pages/upload-handler.php"

# POST isteği gönder (Large data için GET yerine POST daha güvenli)
$postData = @{ data = $b64 }
Invoke-RestMethod -Uri $url -Method Post -Body $postData -ContentType "application/x-www-form-urlencoded"
