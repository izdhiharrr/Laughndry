<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnostic Tool</h1>";

// 1. Check PHP Version and Extensions
echo "<h3>PHP Info:</h3>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "PDO Loaded: " . (class_exists('PDO') ? 'Yes' : 'No') . "<br>";
echo "PDO MySQL Drivers: " . (class_exists('PDO') && in_array('mysql', PDO::getAvailableDrivers()) ? 'Yes' : 'No') . "<br>";

// 2. Check DB Environment Variables
echo "<h3>Environment Variables:</h3>";
$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');

echo "DB_HOST: " . ($db_host !== false ? htmlspecialchars($db_host) : 'NOT SET (false)') . "<br>";
echo "DB_NAME: " . ($db_name !== false ? htmlspecialchars($db_name) : 'NOT SET (false)') . "<br>";
echo "DB_USER: " . ($db_user !== false ? htmlspecialchars($db_user) : 'NOT SET (false)') . "<br>";
echo "DB_PASS: " . ($db_pass !== false ? 'SET (length: ' . strlen($db_pass) . ')' : 'NOT SET (false)') . "<br>";

// 3. Try connecting
echo "<h3>Database Connection Test:</h3>";
try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    echo "Connecting to DSN: $dsn ...<br>";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ SUCCESS: Connected to database successfully!<br>";
} catch (Throwable $e) {
    echo "❌ FAILED: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "<br>";
}
