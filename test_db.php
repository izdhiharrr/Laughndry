<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnostic Tool - Part 2</h1>";

$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    echo "✅ Connected to database.<br><br>";

    // 1. List all tables
    echo "<h3>Tables:</h3>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Found tables: " . implode(', ', $tables) . "<br><br>";

    // 2. Describe order table columns and check if enum status is matching
    if (in_array('order', $tables)) {
        echo "<h3>Describe `order` table:</h3>";
        $columns = $pdo->query("DESCRIBE `order`")->fetchAll();
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($col['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Default']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    } else {
        echo "❌ `order` table NOT found!<br>";
    }

    // 3. Test typical query from admin.php
    echo "<h3>Testing Admin Orders Query:</h3>";
    try {
        $stmt = $pdo->query("
            SELECT 
                o.id,
                c.nama AS customer_nama,
                c.id AS customer_id,
                o.total_harga,
                o.metode_bayar,
                o.status,
                o.created_at
            FROM `order` o
            JOIN customer c ON o.customer_id = c.id
            LIMIT 1
        ");
        $stmt->fetchAll();
        echo "✅ Admin query success!<br>";
    } catch (Throwable $e) {
        echo "❌ Admin query failed: " . htmlspecialchars($e->getMessage()) . "<br>";
    }

    // 4. Test pending_orders count query
    echo "<h3>Testing Pending Orders Count Query:</h3>";
    try {
        $pending_orders = $pdo->query("SELECT COUNT(*) FROM `order` WHERE status = 'pending'")->fetchColumn();
        echo "✅ Pending orders query success! Count: $pending_orders<br>";
    } catch (Throwable $e) {
        echo "❌ Pending orders query failed: " . htmlspecialchars($e->getMessage()) . "<br>";
    }

} catch (Throwable $e) {
    echo "❌ FAILED: " . htmlspecialchars($e->getMessage()) . "<br>";
}
