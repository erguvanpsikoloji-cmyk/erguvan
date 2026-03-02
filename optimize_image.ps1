Add-Type -AssemblyName System.Drawing
$source = "c:\Users\ceren\Desktop\erguvan son\assets\images\hero-psikolojik-destek.jpg"
$dest = "c:\Users\ceren\Desktop\erguvan son\assets\images\hero-psikolojik-destek.webp"

if (Test-Path $source) {
    try {
        $bmp = [System.Drawing.Bitmap]::FromFile($source)
        # Check if WebP is supported by GDI+. (Probably not directly, but we can try)
        # If not, we'll use a compressed JPG or PNG as fallback.
        # Standard GDI+ doesn't support WebP directly without extra codecs.
        # Let's check for available codecs.
        $codecs = [System.Drawing.Imaging.ImageCodecInfo]::GetImageEncoders()
        $webpCodec = $codecs | Where-Object { $_.FormatDescription -eq "WebP" }
        
        if ($webpCodec) {
            $bmp.Save($dest, [System.Drawing.Imaging.ImageFormat]::Webp)
            Write-Host "Success: Converted to WebP"
        }
        else {
            # Fallback: Save as a highly compressed JPG
            $destJpg = "c:\Users\ceren\Desktop\erguvan son\assets\images\hero-psikolojik-destek-opt.jpg"
            $encoder = [System.Drawing.Imaging.Encoder]::Quality
            $encoderParams = New-Object System.Drawing.Imaging.EncoderParameters(1)
            $encoderParams.Param[0] = New-Object System.Drawing.Imaging.EncoderParameter($encoder, 70) # 70% quality
            $jpgCodec = $codecs | Where-Object { $_.FormatDescription -eq "JPEG" }
            $bmp.Save($destJpg, $jpgCodec, $encoderParams)
            Write-Host "Success: Optimized JPG created as fallback (WebP codec not found)"
        }
        $bmp.Dispose()
    }
    catch {
        Write-Host "Error: $($_.Exception.Message)"
    }
}
else {
    Write-Host "Error: Source not found"
}
