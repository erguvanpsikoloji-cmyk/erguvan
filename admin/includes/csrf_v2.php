<?php
/**
 * CSRF TOKEN - ALTERNATİF YÖNTEM
 * Session kaynaklı kilitlenmeleri önlemek için basitleştirilmiş yapı
 */

function generateCSRFToken()
{
    if (session_status() === PHP_SESSION_NONE) {
        // Hata bastırma ile başlat
        @session_start();
    }

    if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            // Fallback for random_bytes failure
            $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
        }
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token)
{
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }

    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}
?>