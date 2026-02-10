$cred = New-Object System.Net.NetworkCredential("mirza@erguvanpsikoloji.com", "92Mirza1!")
$baseUrl = "ftp://ftp.erguvanpsikoloji.com/"

function Get-FtpListRecursive ($path) {
    $req = [System.Net.FtpWebRequest]::Create($path)
    $req.Credentials = $cred
    $req.Method = [System.Net.WebRequestMethods+Ftp]::ListDirectoryDetails
    
    try {
        $resp = $req.GetResponse()
        $reader = New-Object System.IO.StreamReader($resp.GetResponseStream())
        $lines = $reader.ReadToEnd() -split "`n"
        $reader.Close()
        $resp.Close()
        
        foreach ($line in $lines) {
            if ($line -match "(\d+)\s+([a-zA-Z]{3}\s+\d+\s+[:\d]+)\s+(.+)$") {
                $size = [long]$matches[1]
                $name = $matches[3].Trim()
                $isDir = $line.StartsWith("d")
                
                if ($name -match "^\.+$") { continue }
                
                if ($isDir) {
                    Write-Host "DIR: $path$name" -ForegroundColor Cyan
                    # Recursion disabled to avoid timeout, just listing root dirs for now
                }
                else {
                    if ($size -gt 1MB) {
                        Write-Host "FILE: $path$name [ $([math]::round($size/1MB, 2)) MB ]" -ForegroundColor Yellow
                    }
                    elseif ($name -like "error_log" -or $name -like "*.zip" -or $name -like "*.sql") {
                        Write-Host "TARGET: $path$name [ $([math]::round($size/1KB, 2)) KB ]" -ForegroundColor Magenta
                    }
                }
            }
        }
    }
    catch {
        Write-Host "Error listing $path : $($_.Exception.Message)"
    }
}

Write-Host "Scanning Root..."
Get-FtpListRecursive $baseUrl

Write-Host "Scanning admin..."
Get-FtpListRecursive "$baseUrl/admin/"
