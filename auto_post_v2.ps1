$baseUrl = "https://erguvanpsikoloji.com"
$loginUrl = "$baseUrl/admin/login.php?bypass=1"
$addBlogUrl = "$baseUrl/admin/pages/blog-add.php"
$username = "erguvan"
$password = "mihrimah9595"

Write-Host "1. Oturum baslatiliyor ve Giris yapiliyor..." -ForegroundColor Cyan
# Session degiskenini baslat
$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession

# Adim 1: Login sayfasina git ve token al
$loginPage = Invoke-WebRequest -Uri $loginUrl -WebSession $session
$csrfToken = $null
if ($loginPage.Content -match 'name="csrf_token" value="([^"]+)"') {
    $csrfToken = $matches[1]
}

Write-Host "Login CSRF Token: $csrfToken"

# Adim 2: Login POST
$loginBody = @{
    username   = $username
    password   = $password
    csrf_token = $csrfToken
}

$loginResponse = Invoke-WebRequest -Uri $loginUrl -WebSession $session -Method Post -Body $loginBody
Write-Host "Giris Durumu: $($loginResponse.StatusCode)"

# Adim 3: Blog 1 Ekle (Ayrilik Kaygisi)
Write-Host "2. Blog 1 Hazirlaniyor (Ayrilik Kaygisi)..." -ForegroundColor Yellow
$addPage = Invoke-WebRequest -Uri $addBlogUrl -WebSession $session
$csrfTokenAdd = $null
if ($addPage.Content -match 'name="csrf_token" value="([^"]+)"') {
    $csrfTokenAdd = $matches[1]
}

$blog1 = @{
    csrf_token       = $csrfTokenAdd
    title            = "Ayrılık Kaygısı Nedir? Belirtileri ve Tedavisi"
    slug             = "ayrilik-kaygisi-nedir"
    excerpt          = "Ayrılık kaygısı bozukluğu belirtileri, nedenleri ve tedavi yöntemleri hakkında özet bilgi."
    meta_description = "Ayrılık kaygısı bozukluğu belirtileri, nedenleri ve tedavi yöntemleri hakkında detaylı bilgi."
    content          = '<h2 id="belirtiler">Ayrılma Kaygısı Bozukluğu Belirtileri</h2><p>Ayrılma kaygısı yaşayan çocuklarda hem duygusal hem de fiziksel belirtiler görülebilir. Bu belirtiler çocuğun günlük yaşamını, okul uyumunu ve sosyal ilişkilerini olumsuz etkileyebilir.</p><p>En sık görülen belirtiler şunlardır:</p><ul><li>Çocuğun bakım veren kişiden uzaklaştığında aşırı huzursuzluk hissetmesi</li><li>Kabuslar görmesi ve uyku sorunları yaşaması</li><li>Okula gitmek istememe veya okul reddi</li><li>Baş ağrısı, karın ağrısı gibi fiziksel şikayetler</li></ul>'
    category         = "Çocuk Psikolojisi"
    reading_time     = "5 dk"
    status           = "1"
    # Görsel yüklemeyi boş bırakıyoruz (image field bos gidecek, PHP kodunda bos ise hata verebilir, kontrol edelim)
    # blog-add.php:61: if (empty($image)) $missing_fields[] = 'Görsel';
    # HATA! Görsel boş olamazmış. Varsayılan bir yol verelim.
    image            = "assets/images/blog/ayrilik-kaygisi.jpg"
}

$post1 = Invoke-WebRequest -Uri $addBlogUrl -WebSession $session -Method Post -Body $blog1
Write-Host "Blog 1 Yanit: $($post1.StatusCode)"


# Adim 4: Blog 2 Ekle (Oyun Terapisi)
Write-Host "3. Blog 2 Hazirlaniyor (Oyun Terapisi)..." -ForegroundColor Yellow
$addPage2 = Invoke-WebRequest -Uri $addBlogUrl -WebSession $session
$csrfTokenAdd2 = $null
if ($addPage2.Content -match 'name="csrf_token" value="([^"]+)"') {
    $csrfTokenAdd2 = $matches[1]
}

$blog2 = @{
    csrf_token       = $csrfTokenAdd2
    title            = "Oyun Terapisi Nedir? Çocukların Duygusal Dünyasına Açılan Kapı"
    slug             = "oyun-terapisi-nedir"
    excerpt          = "Oyun terapisi nedir, nasıl uygulanır? Çocukların dünyasına oyunla girmek."
    meta_description = "Oyun terapisi nedir, nasıl uygulanır? Çocukların duygusal ve davranışsal sorunlarında oyun terapisinin etkili yöntemleri."
    content          = "<h2>Oyun Terapisi Nedir?</h2><p>Oyun terapisi, çocukların duygusal, davranışsal ve psikolojik yaşantılarını oyun aracılığıyla ifade etmelerini sağlayan bilimsel bir yöntemdir. Çocukların dili oyundur.</p><h3>Bu Yöntem Neden Gereklidir?</h3><ul><li>Çocuklar karmaşık duygularını kelimelerle ifade etmekte zorlanır</li><li>Travmatik yaşantılar oyun yoluyla güvenli biçimde işlenebilir</li><li>Erken dönemde alınan destek, ruhsal sorunları önleyebilir</li></ul>"
    category         = "Çocuk Psikolojisi"
    reading_time     = "5 dk"
    image            = "assets/images/blog/oyun-terapisi.jpg"
}

$post2 = Invoke-WebRequest -Uri $addBlogUrl -WebSession $session -Method Post -Body $blog2
Write-Host "Blog 2 Yanit: $($post2.StatusCode)"

Write-Host "--- ISLEM BITTI ---" -ForegroundColor Green
