$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$cred = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)

function Do-Delete {
    param([string]$name)
    $Url = "ftp://$ftpHost/$name"
    try {
        $req = [System.Net.FtpWebRequest]::Create($Url)
        $req.Method = [System.Net.WebRequestMethods+Ftp]::DeleteFile
        $req.Credentials = $cred
        $resp = $req.GetResponse()
        Write-Host "✅ Deleted: $name" -ForegroundColor Green
        $resp.Close()
    }
    catch {
        Write-Host "❌ Failed: $name | $($_.Exception.Message)" -ForegroundColor Red
    }
}

# List of files identified in the previous LIST / output that are clearly junk
$junk = @(
    "erguvan psikoloji web\ERGUVAN_EXPORT\yedek_veritabani.db",
    "erguvan psikoloji web\ERGUVAN_EXPORT\minify.php",
    "erguvan psikoloji web\ERGUVAN_EXPORT\pi.php",
    "erguvan psikoloji web\ERGUVAN_EXPORT\run_transfer.php"
)

Write-Host "--- CLEANING REMAINING JUNK ---" -ForegroundColor Cyan
foreach ($f in $junk) {
    Do-Delete -name $f
}

Write-Host "--- DONE ---"
