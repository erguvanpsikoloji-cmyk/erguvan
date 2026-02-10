<?php
try {
    $dbPath = __DIR__ . '/antigravity hosting/database/erguvan.db';
    // Fix path if needed, user root is d:\Erguvan antigravity hosting
    $dbPath = __DIR__ . '/database/erguvan.db';

    if (!file_exists($dbPath)) {
        echo "Database not found at $dbPath\n";
        exit(1);
    }

    $pdo = new PDO('sqlite:' . $dbPath);
    $stmt = $pdo->query("SELECT id, username FROM admin_users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($users) . " users:\n";
    foreach ($users as $user) {
        echo "ID: " . $user['id'] . " - Username: " . $user['username'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>