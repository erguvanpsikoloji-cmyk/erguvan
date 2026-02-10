<?php
require_once __DIR__ . '/database/db.php';
try {
    $db = getDB();
    if ($db instanceof MockPDO) {
        echo "DATABASE CONNECTION FAILED (Using MockPDO)\n";
    } else {
        $stmt = $db->query("SELECT * FROM team_members");
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print_r($members);
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>