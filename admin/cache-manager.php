<?php
/**
 * Cache Version Manager
 * Admin panelinden versiyon numarasını kolayca güncelleme aracı
 */

session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/includes/auth.php';

// Giriş kontrolü
requireLogin();

$message = '';
$messageType = '';

// Mevcut versiyon numarasını oku
function getCurrentVersion() {
    $headerFile = __DIR__ . '/../includes/header.php';
    $content = file_get_contents($headerFile);
    
    if (preg_match('/\$cache_version\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
        return $matches[1];
    }
    
    return '1.0.0';
}

// Versiyon numarasını güncelle
function updateVersion($newVersion) {
    $headerFile = __DIR__ . '/../includes/header.php';
    $content = file_get_contents($headerFile);
    
    $pattern = '/(\$cache_version\s*=\s*[\'"])([^\'"]+)([\'"])/';
    $replacement = '${1}' . $newVersion . '${3}';
    $newContent = preg_replace($pattern, $replacement, $content);
    
    if ($newContent && $newContent !== $content) {
        file_put_contents($headerFile, $newContent);
        return true;
    }
    
    return false;
}

// Otomatik versiyon artırma
function incrementVersion($currentVersion) {
    $parts = explode('.', $currentVersion);
    
    // Son sayıyı artır
    if (count($parts) >= 3) {
        $parts[2] = (int)$parts[2] + 1;
    } else {
        $parts[] = '1';
    }
    
    return implode('.', $parts);
}

// POST işleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'increment') {
        $currentVersion = getCurrentVersion();
        $newVersion = incrementVersion($currentVersion);
        
        if (updateVersion($newVersion)) {
            $message = "Versiyon başarıyla güncellendi: {$currentVersion} → {$newVersion}";
            $messageType = 'success';
        } else {
            $message = "Versiyon güncellenirken hata oluştu!";
            $messageType = 'error';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'custom' && isset($_POST['custom_version'])) {
        $customVersion = trim($_POST['custom_version']);
        
        // Versiyon formatı kontrolü
        if (preg_match('/^\d+\.\d+\.\d+$/', $customVersion)) {
            if (updateVersion($customVersion)) {
                $message = "Versiyon başarıyla güncellendi: {$customVersion}";
                $messageType = 'success';
            } else {
                $message = "Versiyon güncellenirken hata oluştu!";
                $messageType = 'error';
            }
        } else {
            $message = "Geçersiz versiyon formatı! Doğru format: 1.0.1";
            $messageType = 'error';
        }
    }
}

$currentVersion = getCurrentVersion();
$nextVersion = incrementVersion($currentVersion);

$page = 'cache-manager';
$page_title = 'Cache Version Yönetimi';
require_once __DIR__ . '/includes/header.php';
?>

<style>
    .version-manager {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .current-version {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 40px;
        border-radius: 16px;
        text-align: center;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    
    .current-version h2 {
        color: white;
        margin-bottom: 10px;
        font-size: 1.2rem;
        opacity: 0.9;
    }
    
    .version-number {
        font-size: 4rem;
        font-weight: 700;
        margin: 20px 0;
        text-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    
    .action-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .action-card {
        background: white;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    
    .action-card h3 {
        color: #333;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .action-card p {
        color: #666;
        margin-bottom: 20px;
        line-height: 1.6;
    }
    
    .btn-large {
        width: 100%;
        padding: 15px 30px;
        font-size: 1.1rem;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-secondary {
        background: #f8f9fa;
        color: #333;
        border: 2px solid #e0e0e0;
    }
    
    .btn-secondary:hover {
        background: #e9ecef;
        border-color: #667eea;
    }
    
    .custom-version-input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    
    .custom-version-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .info-box {
        background: #e7f3ff;
        border-left: 4px solid #2196F3;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
    }
    
    .info-box h4 {
        color: #1976D2;
        margin-bottom: 10px;
    }
    
    .info-box ul {
        margin-left: 20px;
        color: #555;
    }
    
    .info-box li {
        margin: 5px 0;
        line-height: 1.6;
    }
    
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 500;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .icon {
        font-size: 2rem;
    }
    
    code {
        background: #f4f4f4;
        padding: 2px 8px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        color: #d63384;
    }
</style>

<div class="version-manager">
    <h1 style="text-align: center; margin-bottom: 30px; color: #333;">
        🔄 Cache Version Yönetimi
    </h1>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <div class="current-version">
        <h2>Mevcut Cache Versiyonu</h2>
        <div class="version-number"><?php echo htmlspecialchars($currentVersion); ?></div>
        <p style="opacity: 0.9;">Son güncelleme: <?php echo date('d.m.Y H:i'); ?></p>
    </div>
    
    <div class="action-cards">
        <div class="action-card">
            <h3><span class="icon">⚡</span> Otomatik Artır</h3>
            <p>Versiyon numarasını otomatik olarak bir artırır.</p>
            <p style="background: #f8f9fa; padding: 10px; border-radius: 8px; font-family: monospace;">
                <?php echo htmlspecialchars($currentVersion); ?> → <strong><?php echo htmlspecialchars($nextVersion); ?></strong>
            </p>
            <form method="POST">
                <input type="hidden" name="action" value="increment">
                <button type="submit" class="btn-large btn-primary">
                    Versiyonu Artır
                </button>
            </form>
        </div>
        
        <div class="action-card">
            <h3><span class="icon">✏️</span> Manuel Güncelleme</h3>
            <p>Özel bir versiyon numarası belirleyin.</p>
            <form method="POST">
                <input type="hidden" name="action" value="custom">
                <input 
                    type="text" 
                    name="custom_version" 
                    class="custom-version-input" 
                    placeholder="Örn: 2.0.0"
                    pattern="^\d+\.\d+\.\d+$"
                    title="Format: 1.0.0"
                    required
                >
                <button type="submit" class="btn-large btn-secondary">
                    Güncelle
                </button>
            </form>
        </div>
    </div>
    
    <div class="info-box">
        <h4>📚 Versiyon Güncelleme Ne Zaman Yapılmalı?</h4>
        <ul>
            <li><strong>CSS değişiklikleri:</strong> Stil dosyalarında değişiklik yaptıysanız</li>
            <li><strong>JavaScript güncellemeleri:</strong> JS dosyalarını değiştirdiyseniz</li>
            <li><strong>Önemli özellikler:</strong> Yeni özellik eklediyseniz</li>
            <li><strong>Hata düzeltmeleri:</strong> Bug fix'ler için</li>
            <li><strong>Cache sorunları:</strong> Kullanıcılar eski versiyonu görüyorsa</li>
        </ul>
    </div>
    
    <div class="info-box" style="background: #fff3cd; border-left-color: #ffc107;">
        <h4 style="color: #856404;">⚠️ Önemli Bilgiler</h4>
        <ul style="color: #856404;">
            <li>Versiyon güncellendikten sonra tüm kullanıcılar yeni CSS/JS dosyalarını indirecektir</li>
            <li>Gereksiz versiyon güncellemeleri sunucu yükünü artırabilir</li>
            <li>Versiyon formatı: <code>major.minor.patch</code> (Örn: 1.0.1)</li>
            <li>Test sonrası production'da versiyonu güncelleyin</li>
        </ul>
    </div>
    
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); margin-top: 20px;">
        <h4 style="margin-bottom: 15px;">🔗 Hızlı Erişim</h4>
        <div style="display: grid; gap: 10px;">
            <a href="<?php echo BASE_URL; ?>/clear-cache.html" target="_blank" class="btn-secondary" style="display: block; text-align: center; padding: 12px; text-decoration: none; border-radius: 8px;">
                📄 Cache Temizleme Sayfası
            </a>
            <a href="<?php echo BASE_URL; ?>/CACHE-README.md" target="_blank" class="btn-secondary" style="display: block; text-align: center; padding: 12px; text-decoration: none; border-radius: 8px;">
                📖 Cache Yönetim Dokümantasyonu
            </a>
            <a href="<?php echo admin_url(); ?>" class="btn-secondary" style="display: block; text-align: center; padding: 12px; text-decoration: none; border-radius: 8px;">
                🏠 Admin Panele Dön
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
