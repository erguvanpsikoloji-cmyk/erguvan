$appId = "AdobeSystemsIncorporated.AdobePhotoshopExpress_mtcwf2zmmt10c!App"
$scriptPath = "$env:APPDATA\AdobeExpressPrtSc.ps1"
$vbsPath = "$env:APPDATA\AdobeExpressPrtSc.vbs"

# PowerShell script to launch the app
$scriptContent = @"
explorer.exe shell:AppsFolder\$appId
"@
$scriptContent | Set-Content -Path $scriptPath -Force

# VBS script to run hidden
$vbsContent = "Set WshShell = CreateObject(`"WScript.Shell`")`nWshShell.Run `"powershell.exe -ExecutionPolicy Bypass -File $scriptPath`", 0, False"
$vbsContent | Set-Content -Path $vbsPath -Force

# Registry check/setup for PrtSc (Optional, Windows 11 default)
# Note: Full PrtSc mapping usually requires a global hook or third party tool.
# This script prepares the launcher.
Write-Host "Adobe Express Launcher prepared at: $vbsPath" -ForegroundColor Green
Write-Host "To link PrtSc fully, you might need to disable Windows Snipping Tool from PrtSc settings." -ForegroundColor Yellow
