<?php 
// Session'ı sadece bir kez başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../database/db.php';

$db = getDB();
$success = '';
$error = '';

// Google ayarlarını getir
$google_settings = $db->query("SELECT * FROM google_settings ORDER BY setting_key")->fetchAll();
$settings_map = [];
foreach ($google_settings as $setting) {
    $settings_map[$setting['setting_key']] = $setting;
}

// Ayarları güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Geçersiz istek!';
    } else {
        try {
            $stmt = $db->prepare("UPDATE google_settings SET setting_value = :value WHERE setting_key = :key");
            
            foreach ($_POST as $key => $value) {
                if ($key !== 'csrf_token' && $key !== 'action') {
                    $stmt->execute([
                        ':key' => $key,
                        ':value' => trim($value)
                    ]);
                }
            }
            
            $success = 'Google ayarları başarıyla güncellendi!';
            // Ayarları yeniden yükle
            $google_settings = $db->query("SELECT * FROM google_settings ORDER BY setting_key")->fetchAll();
            $settings_map = [];
            foreach ($google_settings as $setting) {
                $settings_map[$setting['setting_key']] = $setting;
            }
        } catch (PDOException $e) {
            $error = 'Hata: ' . $e->getMessage();
        }
    }
}

$page = 'google';
$page_title = 'Google Hizmetleri';
require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($success): ?>
    <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="admin-card">
    <h2 style="margin: 0 0 20px 0;">Google Hizmetleri Entegrasyonu</h2>
    
    <form method="POST" action="">
        <?php echo csrfField(); ?>
        
        <!-- Google Analytics -->
        <div class="form-group" style="margin-bottom: 25px; padding: 20px; background: #f8fafc; border-radius: 8px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">
                Google Analytics ID
            </label>
            <input type="text" name="google_analytics_id" class="form-control" 
                   value="<?php echo htmlspecialchars($settings_map['google_analytics_id']['setting_value'] ?? ''); ?>" 
                   placeholder="G-XXXXXXXXXX">
            <small style="color: #64748b; display: block; margin-top: 5px;">
                <?php echo htmlspecialchars($settings_map['google_analytics_id']['description'] ?? ''); ?>
            </small>
        </div>
        
        <!-- Google Search Console -->
        <div class="form-group" style="margin-bottom: 25px; padding: 20px; background: #f8fafc; border-radius: 8px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">
                Google Search Console Verification Code
            </label>
            <input type="text" name="google_search_console" class="form-control" 
                   value="<?php echo htmlspecialchars($settings_map['google_search_console']['setting_value'] ?? ''); ?>" 
                   placeholder="Verification code veya meta tag content">
            <small style="color: #64748b; display: block; margin-top: 5px;">
                <?php echo htmlspecialchars($settings_map['google_search_console']['description'] ?? ''); ?>
            </small>
        </div>
        
        <!-- Google Tag Manager -->
        <div class="form-group" style="margin-bottom: 25px; padding: 20px; background: #f8fafc; border-radius: 8px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">
                Google Tag Manager ID
            </label>
            <input type="text" name="google_tag_manager" class="form-control" 
                   value="<?php echo htmlspecialchars($settings_map['google_tag_manager']['setting_value'] ?? ''); ?>" 
                   placeholder="GTM-XXXXXXX">
            <small style="color: #64748b; display: block; margin-top: 5px;">
                <?php echo htmlspecialchars($settings_map['google_tag_manager']['description'] ?? ''); ?>
            </small>
        </div>
        
        <!-- Google Ads -->
        <div class="form-group" style="margin-bottom: 25px; padding: 20px; background: #f8fafc; border-radius: 8px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">
                Google Ads ID
            </label>
            <input type="text" name="google_ads_id" class="form-control" 
                   value="<?php echo htmlspecialchars($settings_map['google_ads_id']['setting_value'] ?? ''); ?>" 
                   placeholder="AW-XXXXXXXXX">
            <small style="color: #64748b; display: block; margin-top: 5px;">
                <?php echo htmlspecialchars($settings_map['google_ads_id']['description'] ?? ''); ?>
            </small>
        </div>
        
        <!-- Google Business Profile -->
        <div class="form-group" style="margin-bottom: 25px; padding: 20px; background: #f8fafc; border-radius: 8px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">
                Google Business Profile URL
            </label>
            <input type="url" name="google_business_profile" class="form-control" 
                   value="<?php echo htmlspecialchars($settings_map['google_business_profile']['setting_value'] ?? ''); ?>" 
                   placeholder="https://www.google.com/maps/...">
            <small style="color: #64748b; display: block; margin-top: 5px;">
                <?php echo htmlspecialchars($settings_map['google_business_profile']['description'] ?? ''); ?>
            </small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Ayarları Kaydet</button>
        </div>
    </form>
</div>

<div class="admin-card" style="margin-top: 20px;">
    <h3 style="margin: 0 0 15px 0;">Kurulum Rehberi</h3>
    <div style="color: #64748b; line-height: 1.8;">
        <p><strong>Google Analytics:</strong> Google Analytics hesabınızdan Measurement ID'yi (G-XXXXXXXXXX) alın ve buraya girin.</p>
        <p><strong>Google Search Console:</strong> Search Console'dan verification code'unuzu veya meta tag content değerini girin.</p>
        <p><strong>Google Tag Manager:</strong> Tag Manager container ID'nizi (GTM-XXXXXXX) girin.</p>
        <p><strong>Google Ads:</strong> Google Ads conversion tracking ID'nizi (AW-XXXXXXXXX) girin.</p>
        <p><strong>Google Business Profile:</strong> İşletme profilinizin URL'sini girin.</p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

