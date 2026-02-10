$ftpHost = "ftp.erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"

$filesToDelete = @(
    "admin/pages/blog-add_clean.php",
    "admin/pages/check.php",
    "admin/pages/db_diag.php",
    "admin/pages/debug_check.php",
    "admin/pages/diag.php",
    "admin/pages/diag_simple.php",
    "admin/pages/temp_panik_atak.php",
    "admin/pages/test_error.php",
    "admin/pages/update_sena_db.php",
    "admin/pages/blog-add-new.php", 
    "admin/pages/ba_temp.php"
)

foreach ($file in $filesToDelete) {
    try {
        $request = [System.Net.FtpWebRequest]::Create("ftp://$ftpHost/$file")
        $request.Method = [System.Net.WebRequestMethods+Ftp]::DeleteFile
        $request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $response = $request.GetResponse()
        Write-Host "✅ Deleted: $file" -ForegroundColor Green
        $response.Close()
    }
    catch {
        Write-Host "⚠️ Could not delete $file : $($_.Exception.Message)" -ForegroundColor Yellow
    }
}
