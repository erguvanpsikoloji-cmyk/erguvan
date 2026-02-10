$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"

# Silinecek dosya listesi (Obje bazli)
$filesToDelete = @(
    "toc_fix_final.zip",
    "toc_update.zip",
    "toggle_fix_BASIT.zip",
    "simple_unzipper_upload.ps1",
    "simple_upload.ps1",
    "sqlite_restore_final.php",
    "sqlite_restore_final_v2.php",
    "test_dirs.ps1",
    "test_dirs_v2.ps1",
    "test_dirs_v3.ps1",
    "test_pwd.ps1",
    "test_rename.ps1",
    "upload_purge.ps1",
    "upload_test_dirs.ps1",
    "upload_test_file.ps1",
    "upload_to_live.ps1",
    "upload_to_live_v2.ps1",
    "upload_to_live_v3.ps1",
    "upload_unzipper.ps1",
    "upload_v29.ps1",
    "root_write_test.txt",
    "passive_test.txt",
    "quota_test.txt",
    "unzip_archive.php",
    "unzipper.php"
)

function Delete-FtpFile($remotePath) {
    try {
        $uri = [System.Uri]("ftp://$ftpHost/$remotePath")
        $ftpRequest = [System.Net.FtpWebRequest]::Create($uri)
        $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::DeleteFile
        $response = $ftpRequest.GetResponse()
        Write-Host "Silindi: $remotePath" -ForegroundColor Yellow
        $response.Close()
    }
    catch {
        Write-Host "Silinemedi ($remotePath): $($_.Exception.Message)" -ForegroundColor Red
    }
}

foreach ($file in $filesToDelete) {
    Delete-FtpFile $file
}

Write-Host "TEMIZLIK TAMAMLANDI!" -ForegroundColor Green
