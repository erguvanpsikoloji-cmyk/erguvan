$source = "c:\Users\ceren\Desktop\erguvan son\assets\images\logo_icon.png"
$destination = "c:\Users\ceren\Desktop\erguvan son\assets\images\logo_icon.webp"

Add-Type -AssemblyName System.Drawing
$image = [System.Drawing.Image]::FromFile($source)
# Not: Standard .NET Drawing doesn't support WebP directly.
# Let's try to see if there is any other way or just use a dummy WebP if I can't convert.
# Actually, I can use the browser to save it as WebP if I open it?
# Or I can just use generate_image to create a WebP logo if I describe it well.
# But let's try a simpler approach if possible.
