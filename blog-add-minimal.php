<?php
// Minimal Debug Mode
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🚀 Blog Add Minimal Loaded</h1>";

// 1. Config Check
try {
    require_once __DIR__ . '/../../config.php';
    echo "<p>✅ Config Loaded</p>";
} catch (Throwable $e) {
    die("❌ Config Error: " . $e->getMessage());
}

// 2. DB Check
try {
    require_once __DIR__ . '/../../database/db.php';
    $db = getDB();
    echo "<p>✅ DB Connected</p>";
} catch (Throwable $e) {
    die("❌ DB Error: " . $e->getMessage());
}

// 3. Auth Check
try {
    require_once __DIR__ . '/../includes/auth.php';
    requireLogin(); // Enabled to test SESSION
    echo "<p>✅ Auth Loaded & Login Checked</p>";
} catch (Throwable $e) {
    die("❌ Auth Error: " . $e->getMessage());
}

// 4. CSRF Check
try {
    require_once __DIR__ . '/../includes/csrf.php';
    echo "<p>✅ CSRF Loaded</p>";
} catch (Throwable $e) {
    die("❌ CSRF Error: " . $e->getMessage());
}

// 5. Upload Handler Check
try {
    require_once __DIR__ . '/../includes/upload-handler.php';
    echo "<p>✅ Upload Handler Loaded</p>";
} catch (Throwable $e) {
    die("❌ Upload Handler Error: " . $e->getMessage());
}


echo "<h3>🎉 If you see this, basic includes are FINE. The issue is in the logic below.</h3>";
?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Test Title">
    <button type="submit">Submit Minimal</button>
</form>