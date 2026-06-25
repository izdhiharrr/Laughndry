<?php
// Set default timezone to WIB (Asia/Jakarta)
date_default_timezone_set('Asia/Jakarta');


// Load autoloader dan dotenv jika tersedia
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    
    // Inisialisasi dotenv jika file .env ada
    if (file_exists(__DIR__ . '/../.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->safeLoad();
    }
}

$db_host = (getenv('DB_HOST') !== false) ? getenv('DB_HOST') : ($_ENV['DB_HOST'] ?? 'localhost');
$db_name = (getenv('DB_NAME') !== false) ? getenv('DB_NAME') : ($_ENV['DB_NAME'] ?? 'laughndry_db');
$db_user = (getenv('DB_USER') !== false) ? getenv('DB_USER') : ($_ENV['DB_USER'] ?? 'root');
$db_pass = (getenv('DB_PASS') !== false) ? getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? '');


try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
    // Set MySQL connection timezone to Asia/Jakarta (GMT+7)
    $pdo->exec("SET time_zone = '+07:00'");
} catch (PDOException $e) {
    die("❌ Koneksi database gagal: " . $e->getMessage());
}
