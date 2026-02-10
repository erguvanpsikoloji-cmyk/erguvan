
$sourceDir = "d:\ceren antigravity web site"
$destDir = "d:\ceren antigravity web site\ERGUVAN_EXPORT"

Write-Output "Starting Export Preparation..."

# Clean Destination
if (Test-Path $destDir) {
    Remove-Item -Path "$destDir\*" -Recurse -Force -ErrorAction SilentlyContinue
}
else {
    New-Item -ItemType Directory -Path $destDir
}

# Folders to Copy
$foldersToCopy = @("admin", "assets", "database", "includes", "pages", "css", "js")

foreach ($folder in $foldersToCopy) {
    $srcPath = "$sourceDir\$folder"
    if (Test-Path $srcPath) {
        Write-Output "Copying folder: $folder"
        Copy-Item -Path $srcPath -Destination "$destDir\$folder" -Recurse -Force
    }
    else {
        Write-Output "Folder not found, skipping: $folder"
    }
}

# Files to Copy (Root PHP, htaccess, etc)
$files = Get-ChildItem -Path $sourceDir -File
$excludeFilePatterns = @("prepare_export.ps1", "ftp_deploy.ps1", "sync_config.jsonc", "task.md", "implementation_plan.md")

foreach ($file in $files) {
    # Skip zips and backup files
    if ($file.Extension -eq ".zip") { continue }
    if ($file.Extension -eq ".rar") { continue }
    
    # Check explicit excludes
    if ($excludeFilePatterns -contains $file.Name) { continue }
    
    # Skip typical temp/backup naming
    if ($file.Name -match "sena_") { continue }
    if ($file.Name -match "erguvan_.*\.php") { continue } # Skip temp fixes like erguvan_fix.php unless critical? 
    # Actually, config.php, index.php are critical.
    # User said "why are there erguvan files".
    # But usually site needs index.php.
    
    # Let's be permissive with PHP files but exclude clearly temporary ones
    if ($file.Name -match "asil_yedek") { continue }
    if ($file.Name -match "restore_data") { continue }

    Write-Output "Copying file: $($file.Name)"
    Copy-Item -Path $file.FullName -Destination "$destDir\$($file.Name)" -Force
}

Write-Output "Export Preparation Finished."
