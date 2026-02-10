<?php
/**
 * TEST CSRF IMPLEMENTATION
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the new CSRF file
require_once __DIR__ . '/admin/includes/csrf.php';

echo "<h2>🛡️ CSRF Test</h2>";

echo "<p>Generating Token...</p>";
$token = generateCSRFToken();
echo "<p>Token: " . htmlspecialchars($token) . "</p>";
echo "<p>Session ID: " . session_id() . "</p>";

echo "<h3>Verification Test:</h3>";
if (verifyCSRFToken($token)) {
    echo "<p style='color:green'>✅ Token verification PASSED</p>";
} else {
    echo "<p style='color:red'>❌ Token verification FAILED</p>";
}

// Test with wrong token
if (!verifyCSRFToken('wrong_token')) {
    echo "<p style='color:green'>✅ Wrong token rejected correctly</p>";
} else {
    echo "<p style='color:red'>❌ Wrong token ACCEPTED (Security Risk!)</p>";
}
?>