$loginUrl = "https://erguvanpsikoloji.com/admin/login.php"
$addBlogUrl = "https://erguvanpsikoloji.com/admin/pages/blog-add.php"
$username = "erguvan"
$password = "mihrimah9595"

Write-Host "1. Giris sayfasi aliniyor..."
$loginPage = Invoke-WebRequest -Uri $loginUrl -SessionVariable session

# CSRF Token'i bul (Input hidden name="csrf_token")
$csrfToken = $null
if ($loginPage.ParsedHtml) {
    $csrfToken = $loginPage.ParsedHtml.getElementsByName("csrf_token")[0].value
}
elseif ($loginPage.Content -match 'name="csrf_token" value="([^"]+)"') {
    $csrfToken = $matches[1]
}

if (-not $csrfToken) {
    Write-Host "HATA: CSRF token bulunamadi!" -ForegroundColor Red
    exit
}
Write-Host "Token bulundu: $csrfToken"

Write-Host "2. Giris yapiliyor..."
$loginBody = @{
    username   = $username
    password   = $password
    csrf_token = $csrfToken
}
$loginResponse = Invoke-WebRequest -Uri $loginUrl -WebSession $session -Method Post -Body $loginBody

# Giris basarili mi? (Redirect veya dashboard kontrolu)
# Genellikle basarili giriste redirect olur.
Write-Host "Giris yaniti: $($loginResponse.StatusCode)"


# --- BLOG 1: Ayrilik Kaygisi ---
Write-Host "3. Blog ekleme formuna gidiliyor (Token icin)..."
$addPage = Invoke-WebRequest -Uri $addBlogUrl -WebSession $session

$csrfTokenAdd = $null
if ($addPage.ParsedHtml) {
    $csrfTokenAdd = $addPage.ParsedHtml.getElementsByName("csrf_token")[0].value
}
elseif ($addPage.Content -match 'name="csrf_token" value="([^"]+)"') {
    $csrfTokenAdd = $matches[1]
}

Write-Host "Form Token: $csrfTokenAdd"

Write-Host "4. 'Ayrilik Kaygisi' ekleniyor..."
$blog1 = @{
    csrf_token       = $csrfTokenAdd
    title            = "Ayrılık Kaygısı Nedir? Belirtileri ve Tedavisi"
    slug             = "ayrilik-kaygisi-nedir"
    category_id      = "Çocuk Psikolojisi" # Value lazim, text degil. Kodda kategori nasil?
    # Kategori ID'sini bilmedigimiz icin TEXT veya varsa ID 1-2 deneyelim.
    # Genelde select box vardir. Sayfa kaynaginda value ne?
    # Varsayim: New Category text input veya select. Eger select ise ID sart.
    # Simdilik 'Çocuk Psikolojisi' string olarak gonderelim, belki create on fly vardir?
    # YOKSA: category select box'in value'sunu bulmamiz lazim.
    
    # Eger select box ise, name="category_id"
    meta_description = "Ayrılık kaygısı bozukluğu belirtileri, nedenleri ve tedavi yöntemleri hakkında detaylı bilgi."
    content          = '<h2 id="belirtiler">Ayrılma Kaygısı Bozukluğu Belirtileri</h2><p>Ayrılma kaygısı yaşayan çocuklarda hem duygusal hem de fiziksel belirtiler görülebilir.</p><ul><li>Çocuğun bakım veren kişiden uzaklaştığında aşırı huzursuzluk hissetmesi</li><li>Kabuslar görmesi ve uyku sorunları yaşaması</li><li>Okula gitmek istememe veya okul reddi</li></ul>'
    status           = "1" # Yayinla
}

# Kategori listesini cekelim
# input name="category" veya "category_id"?
# Check blog-add.php source previously viewed
# <select name="category" id="category" class="form-control" required>
#   <option value="">Seçiniz...</option>
#   <?php foreach ($categories as $cat): ?>
#       <option value="<?php echo $cat['name']; ?>"><?php echo $cat['name']; ?></option>

# Value NAME imis! Super.
$blog1["category"] = "Çocuk Psikolojisi"

# Content field name? <textarea id="editor" name="content">
# Title name="title", Slug name="slug"

$postResponse = Invoke-WebRequest -Uri $addBlogUrl -WebSession $session -Method Post -Body $blog1

Write-Host "Blog 1 Sonuc: $($postResponse.StatusCode)"


# --- BLOG 2: Oyun Terapisi ---
Write-Host "5. 'Oyun Terapisi' ekleniyor..."

# Token yenilenmis olabilir mi? Guvenlik icin tekrar token alalim mi? 
# Ayni session devam ediyor. Sayfayi refresh etmeye gerek yok ama yeni form token olabilir.
# Tekrar GET yapalim.
$addPage2 = Invoke-WebRequest -Uri $addBlogUrl -WebSession $session
if ($addPage2.Content -match 'name="csrf_token" value="([^"]+)"') {
    $csrfTokenAdd2 = $matches[1]
}

$blog2 = @{
    csrf_token       = $csrfTokenAdd2
    title            = "Oyun Terapisi Nedir? Çocukların Duygusal Dünyasına Açılan Kapı"
    slug             = "oyun-terapisi-nedir"
    category         = "Çocuk Psikolojisi"
    meta_description = "Oyun terapisi nedir, nasıl uygulanır? Çocukların duygusal ve davranışsal sorunlarında oyun terapisinin etkili yöntemleri."
    content          = '<h2>Oyun Terapisi Nedir?</h2><p>Oyun terapisi, çocukların duygusal, davranışsal ve psikolojik yaşantılarını oyun aracılığıyla ifade etmelerini sağlayan bilimsel bir yöntemdir.</p><h3>Bu Yöntem Neden Gereklidir?</h3><ul><li>Çocuklar karmaşık duygularını kelimelerle ifade etmekte zorlanır</li><li>Travmatik yaşantılar oyun yoluyla güvenli biçimde işlenebilir</li></ul>'
    status           = "1"
}

$postResponse2 = Invoke-WebRequest -Uri $addBlogUrl -WebSession $session -Method Post -Body $blog2
Write-Host "Blog 2 Sonuc: $($postResponse2.StatusCode)"

Write-Host "ISLEM TAMAMLANDI."
