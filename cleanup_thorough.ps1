$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$cred = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)

function Invoke-FtpAction {
    param([string]$Url, [string]$Method)
    try {
        $req = [System.Net.FtpWebRequest]::Create($Url)
        $req.Method = $Method
        $req.Credentials = $cred
        $resp = $req.GetResponse()
        $resp.Close()
        Write-Host "✅ [OK] $Method -> $Url" -ForegroundColor Green
        return $true
    }
    catch {
        if ($_.Exception.Message -notlike "*550*") {
            Write-Host "⚠️ [FAIL] $Method -> $Url | $($_.Exception.Message)" -ForegroundColor Yellow
        }
        return $false
    }
}

function Cleanup-Garbage {
    param([string]$DirUrl)
    Write-Host "Scanning: $DirUrl" -ForegroundColor Cyan
    
    $req = [System.Net.FtpWebRequest]::Create($DirUrl)
    $req.Method = "LIST"
    $req.Credentials = $cred
    try {
        $resp = $req.GetResponse()
        $reader = New-Object System.IO.StreamReader($resp.GetResponseStream())
        $content = $reader.ReadToEnd()
        $reader.Close(); $resp.Close()
    }
    catch {
        Write-Host "Could not list $DirUrl"
        return
    }

    $lines = $content -split "`r`n"
    foreach ($line in $lines) {
        if ([string]::IsNullOrWhiteSpace($line)) { continue }
        # Simplified parsing for names that might have spaces or backslashes
        $parts = $line -split "\s+", 9
        if ($parts.Count -lt 9) { continue }
        $name = $parts[8]
        if ($name -eq "." -or $name -eq "..") { continue }
        
        $isDir = $line.StartsWith("d")
        $encodedName = [Uri]::EscapeDataString($name)
        $url = "$DirUrl$encodedName"

        # DELETION CRITERIA:
        # 1. Has backslash
        # 2. Starts with dist_
        # 3. Specifically named garbage
        $isGarbage = $name.Contains("\") -or 
        $name.StartsWith("dist_") -or 
        $name -eq "temp_sena_check" -or 
        $name -eq "temp_sena_kurtarma" -or 
        $name -eq "erguvan psikoloji web" -or
        $name -eq "ERGUVAN PSİKOTERAPİ MERKEZİ" -or
        $name -eq "ERGUVAN_EXPORT" -or
        $name -eq "scrollbar_fix_v1" -or
        $name -eq "style_fix_v3_pkg" -or
        $name -eq "toggle_fix_BASIT"

        if ($isGarbage) {
            if ($isDir) {
                # Note: For folders, we should use Recursive delete if possible, 
                # but if it's a 'fake' folder with backslash, DELE might suffice.
                # However, name.Contains("\") files are often reported as files.
                Write-Host "Deleting garbage: $name"
                Invoke-FtpAction -Url $url -Method "DELE"
                Invoke-FtpAction -Url $url -Method "RMD"
            }
            else {
                Invoke-FtpAction -Url $url -Method "DELE"
            }
        }
    }
}

Write-Host "--- SYSTEM CLEANUP START ---"
Cleanup-Garbage -DirUrl "ftp://$ftpHost/"
Cleanup-Garbage -DirUrl "ftp://$ftpHost/public_html/"
Write-Host "--- SYSTEM CLEANUP END ---"
