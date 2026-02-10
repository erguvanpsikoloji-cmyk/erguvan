<?php
/**
 * SIMPLE POST TEST
 * Tests if the server can handle basic POST requests without 500ing
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>📮 POST Test</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<p style='color:green'>✅ POST request received!</p>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    echo "<p>RAM Used: " . round(memory_get_usage() / 1024 / 1024, 2) . " MB</p>";
} else {
    echo '<form method="POST">
        <input type="text" name="test_field" value="Test Data">
        <button type="submit">Send POST</button>
    </form>';
}
?>