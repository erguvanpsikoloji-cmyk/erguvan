<?php
// Basit şifre hash oluşturucu
$passwords = [
    'admin123',
    'erguvan2026',
    'test123',
    '123456'
];

echo "<h2>Password Hashes</h2>";
foreach ($passwords as $pass) {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    echo "<p><strong>$pass</strong>:<br><code>$hash</code></p>";
}

echo "<hr><h3>Test Hash:</h3>";
$test_hash = '$2y$10$abcdefghijklmnopqrstuuVlmSCcP8yB.HlHK7xFm3.4E2'; // örnek
$test_password = 'admin123';
echo "<p>Testing password: <strong>$test_password</strong></p>";
if (password_verify($test_password, $test_hash)) {
    echo "<p style='color:green;'>✓ Password matches!</p>";
} else {
    echo "<p style='color:red;'>✗ Password does NOT match</p>";
}
?>