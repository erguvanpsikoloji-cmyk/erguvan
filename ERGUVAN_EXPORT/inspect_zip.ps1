Add-Type -AssemblyName System.IO.Compression.FileSystem
$zipFile = "C:\Users\ceren\.gemini\antigravity\scratch\uzmanpsikolog_sena_ceren\erguvan_deploy_v54.zip"
$zip = [System.IO.Compression.ZipFile]::OpenRead($zipFile)
$zip.Entries | Select-Object -ExpandProperty FullName
$zip.Dispose()
