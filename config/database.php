<?php
/**
 * database.php — Koneksi Database PDO untuk Laughndry
 * 
 * File ini mengatur koneksi ke MySQL menggunakan PDO.
 * Default XAMPP: host=localhost, user=root, password kosong.
 */

$db_host = 'localhost';
$db_name = 'laughndry_db';
$db_user = 'root';
$db_pass = '';  // Default XAMPP password kosong

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
} catch (PDOException $e) {
    die("❌ Koneksi database gagal: " . $e->getMessage());
}
