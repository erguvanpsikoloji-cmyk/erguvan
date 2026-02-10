<?php
session_start();
if (!isset($_SESSION['test_count'])) {
    $_SESSION['test_count'] = 0;
}
$_SESSION['test_count']++;

echo "Session ID: " . session_id() . "<br>";
echo "Test Count: " . $_SESSION['test_count'] . "<br>";
echo "Session Save Path: " . session_save_path() . "<br>";
echo "Is writable? " . (is_writable(session_save_path() ?: sys_get_temp_dir()) ? "Yes" : "No") . "<br>";
echo "<a href='session_test.php'>Refresh to test persistence</a>";
?>