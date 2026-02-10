
$ftpHost = "ftp://erguvanpsikoloji.com"
$ftpUser = "mirza@erguvanpsikoloji.com"
$ftpPass = "92Mirza1!"
$localFile = "d:\ceren antigravity web site\erguvan_archive_deploy.zip"
$remoteFile = "erguvan_archive_deploy.zip"

Write-Output "Starting Robust Upload for $remoteFile..."

$uri = New-Object System.Uri($ftpHost + "/" + $remoteFile)
$request = [System.Net.FtpWebRequest]::Create($uri)
$request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
$request.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
$request.UseBinary = $true
$request.KeepAlive = $false
$request.Timeout = 300000 # 5 minutes

try {
    $fileStream = [System.IO.File]::OpenRead($localFile)
    $requestStream = $request.GetRequestStream()
    
    $buffer = New-Object byte[] 8192
    $totalBytes = $fileStream.Length
    $uploadedBytes = 0
    
    while (($count = $fileStream.Read($buffer, 0, $buffer.Length)) -gt 0) {
        $requestStream.Write($buffer, 0, $count)
        $uploadedBytes += $count
        # Optional: Print progress every ~1MB
        if ($uploadedBytes % (1024 * 1024) -lt 8192) {
            Write-Host "Progress: $([math]::Round($uploadedBytes / 1024 / 1024, 2)) MB / $([math]::Round($totalBytes / 1024 / 1024, 2)) MB"
        }
    }
    
    $requestStream.Close()
    $fileStream.Close()
    
    $response = $request.GetResponse()
    Write-Output "Upload Complete: $($response.StatusDescription)"
    $response.Close()
    
}
catch {
    Write-Error "Upload Failed: $_"
    if ($fileStream) { $fileStream.Close() }
    if ($requestStream) { $requestStream.Close() }
}
