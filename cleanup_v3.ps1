$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$cred = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)

function Invoke-FtpMethod {
    param([string]$Url, [string]$Method)
    try {
        $req = [System.Net.FtpWebRequest]::Create($Url)
        $req.Method = $Method
        $req.Credentials = $cred
        $resp = $req.GetResponse()
        $resp.Close()
        Write-Host "SUCCESS: $Method -> $Url" -ForegroundColor Green
        return $true
    }
    catch {
        Write-Host "FAIL: $Method -> $Url | $($_.Exception.Message)" -ForegroundColor Yellow
        return $false
    }
}

$targets = @(
    "temp_sena_check",
    "temp_sena_kurtarma",
    "temp_style.txt",
    "temp_update_text.php",
    "test_debug.php",
    "test_output.php",
    "ERGUVAN_EXPORT",
    ".local",
    "diagnostic_pkg",
    "erguvan_basit_kurulum",
    "erguvan_cikarilmis",
    "SON_COZUM"
)

Write-Host "--- CLEANUP START ---"

foreach ($t in $targets) {
    # Try both root and public_html
    $urls = @(
        "ftp://$ftpHost/$t",
        "ftp://$ftpHost/public_html/$t"
    )
    
    foreach ($url in $urls) {
        Write-Host "Trying: $url"
        # Try DELE (file)
        if (-not (Invoke-FtpMethod -Url $url -Method "DELE")) {
            # Try RMD (folder) - note: only works if empty
            Invoke-FtpMethod -Url $url -Method "RMD"
        }
    }
}

Write-Host "--- CLEANUP END ---"
