<?php
/**
 * SIMULATE BLOG ADD POST
 * Sends a full POST request to blog-add.php locally to catch output
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🕵️ Simulating Blog Submission</h2>";

$url = 'https://erguvanpsikoloji.com/admin/pages/blog-add.php';

// Prepare data
$postData = [
    'title' => 'Debug Test Post',
    'slug' => 'debug-test-' . time(),
    'excerpt' => 'Debug excerpt',
    'content' => '<p>Debug content</p>',
    'category' => 'Genel',
    'csrf_token' => 'bypass', // Note: This might fail if CSRF check is too strict
    'image_base64' => '', // Empty for now to test logic
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . $_COOKIE['PHPSESSID']); // Pass current session if possible

echo "<p>Target: $url</p>";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "<h3>Result (HTTP $httpCode)</h3>";

if ($error) {
    echo "<p style='color:red'>cURL Error: $error</p>";
} else {
    // Separate header and body
    list($header, $body) = explode("\r\n\r\n", $response, 2);

    echo "<h4>Headers:</h4>";
    echo "<pre>" . htmlspecialchars($header) . "</pre>";

    echo "<h4>Body Preview (First 500 chars):</h4>";
    echo "<pre>" . htmlspecialchars(substr($body, 0, 500)) . "</pre>";

    if ($httpCode == 500) {
        echo "<p style='color:red; font-weight:bold'>❌ 500 ERROR CONFIRMED</p>";
    } else {
        echo "<p style='color:green'>✅ Response received</p>";
    }
}
?>