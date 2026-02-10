$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$cred = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)

function Do-Action {
    param([string]$Url, [string]$Method, [string]$RenameTo = $null)
    try {
        $req = [System.Net.FtpWebRequest]::Create($Url)
        $req.Method = $Method
        $req.Credentials = $cred
        if ($RenameTo) { $req.RenameTo = $RenameTo }
        $resp = $req.GetResponse()
        Write-Host "✅ [OK] $Method -> $Url" -ForegroundColor Green
        $resp.Close()
    }
    catch {
        Write-Host "❌ [FAIL] $Method -> $Url | $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host "`n--- ROOT LEVEL RESTORATION ---" -ForegroundColor Cyan
# temp_index.php -> index.php
Do-Action -Url "ftp://$ftpHost/temp_index.php" -Method "RENAME" -RenameTo "index.php"

# Verify
Write-Host "`n--- VERIFYING ---" -ForegroundColor Cyan
try {
    $wc = New-Object System.Net.WebClient
    $wc.DownloadString("https://erguvanpsikoloji.com/") | Select-Object -First 5
    Write-Host "✅ Site is UP!" -ForegroundColor Green
}
catch {
    Write-Host "❌ Site is still down or unreachable." -ForegroundColor Red
}

Write-Host "`n--- CLEANING ERGUVAN_EXPORT FILES ---" -ForegroundColor Yellow
# List and delete anything containing export
$req = [System.Net.FtpWebRequest]::Create("ftp://$ftpHost/")
$req.Method = "LIST"
$req.Credentials = $cred
$resp = $req.GetResponse()
$reader = New-Object System.IO.StreamReader($resp.GetResponseStream())
$content = $reader.ReadToEnd()
$reader.Close(); $resp.Close()

$lines = $content -split "`r`n"
foreach ($line in $lines) {
    if ($line -like "*ERGUVAN_EXPORT*") {
        $name = ($line -split "\s+", 9)[8]
        Write-Host "Deleting: $name"
        Do-Action -Url "ftp://$ftpHost/$name" -Method "DELETE"
    }
}

Write-Host "`n--- DONE ---"
