try {
    $content = (Invoke-WebRequest -Uri "http://www.erguvanpsikoloji.com/" -UseBasicParsing).Content
    
    if ($content -match 'class="hamburger"') {
        Write-Host "Hamburger Menu: FOUND" -ForegroundColor Green
    }
    else {
        Write-Host "Hamburger Menu: NOT FOUND" -ForegroundColor Red
    }

    if ($content -match '@media \(max-width: 900px\)') {
        Write-Host "Mobile CSS: FOUND" -ForegroundColor Green
    }
    else {
        Write-Host "Mobile CSS: NOT FOUND" -ForegroundColor Red
    }
    
    if ($content -match 'navLinks.classList.toggle') {
        Write-Host "Mobile JS: FOUND" -ForegroundColor Green
    }
    else {
        Write-Host "Mobile JS: NOT FOUND" -ForegroundColor Red
    }

}
catch {
    Write-Host "Error fetching URL: $_" -ForegroundColor Red
}
