<?php
/**
 * SIMULATE BLOG ADD POST V2 (AUTHENTICATED)
 * Uses a real session ID to bypass login redirect
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get real session ID from a file or hardcode for testing
// NOTE: You need to login in browser, get PHPSESSID cookie, and put it here
$sessionId = 's9ai4o536boa8a2tbt4rc874ov'; // From previous CSRF test output containing session_id

echo "<h2>🕵️ Simulating Blog Submission V2</h2>";
echo "<p>Using Session ID: $sessionId</p>";

$url = 'https://erguvanpsikoloji.com/admin/pages/blog-add.php';

// Prepare data
$postData = [
    'title' => 'Debug Test Post',
    'slug' => 'debug-test-' . time(),
    'excerpt' => 'Debug excerpt',
    'content' => '<p>Debug content</p>',
    'category' => 'Genel',
    'csrf_token' => 'bypass', // The script won't verify this if we don't present a valid one that matches session
    'image_base64' => '',
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . $sessionId); // Pass session cookie

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

    echo "<h4>Body Preview:</h4>";
    echo "<pre>" . htmlspecialchars(substr($body, 0, 1000)) . "</pre>";
}
?>