<?php
/**
 * SEO Helper Fonksiyonları
 * Veritabanından SEO ayarlarını çeker ve kullanır
 */

function getSEOSettings($page_type = 'home') {
    static $seo_cache = [];
    
    if (!isset($seo_cache[$page_type])) {
        try {
            require_once __DIR__ . '/../database/db.php';
            $db = getDB();
            
            $stmt = $db->prepare("SELECT * FROM seo_settings WHERE page_type = ?");
            $stmt->execute([$page_type]);
            $seo = $stmt->fetch();
            
            $seo_cache[$page_type] = $seo ?: [];
        } catch (Exception $e) {
            error_log('SEO settings error: ' . $e->getMessage());
            $seo_cache[$page_type] = [];
        }
    }
    
    return $seo_cache[$page_type];
}

function getGoogleSettings() {
    static $google_cache = null;
    
    if ($google_cache === null) {
        try {
            require_once __DIR__ . '/../database/db.php';
            $db = getDB();
            
            $settings = $db->query("SELECT setting_key, setting_value FROM google_settings")->fetchAll();
            $google_cache = [];
            foreach ($settings as $setting) {
                $google_cache[$setting['setting_key']] = $setting['setting_value'];
            }
        } catch (Exception $e) {
            error_log('Google settings error: ' . $e->getMessage());
            $google_cache = [];
        }
    }
    
    return $google_cache;
}

