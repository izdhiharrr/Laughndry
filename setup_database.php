<?php
/**
 * setup_database.php — Auto Setup Database Laughndry
 * 
 * CARA PAKAI:
 * 1. Pastikan Apache & MySQL sudah START di XAMPP
 * 2. Buka browser → http://localhost/Laughndry-coin/setup_database.php
 * 3. Database & tabel akan otomatis terbuat
 * 4. HAPUS file ini setelah selesai setup (demi keamanan)
 */

// Load autoloader dan dotenv jika tersedia
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Inisialisasi dotenv jika file .env ada
    if (file_exists(__DIR__ . '/.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->safeLoad();
    }
}

$db_host = (getenv('DB_HOST') !== false) ? getenv('DB_HOST') : ($_ENV['DB_HOST'] ?? 'localhost');
$db_user = (getenv('DB_USER') !== false) ? getenv('DB_USER') : ($_ENV['DB_USER'] ?? 'root');
$db_pass = (getenv('DB_PASS') !== false) ? getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? '');
$db_name = (getenv('DB_NAME') !== false) ? getenv('DB_NAME') : ($_ENV['DB_NAME'] ?? 'laughndry_db');

// Style untuk halaman
echo '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database — Laughndry</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: "Inter", sans-serif; 
            background: #f0f4f0; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0,67,58,0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        h1 { color: #00433a; font-size: 1.8rem; margin-bottom: 8px; }
        .subtitle { color: #6b7280; margin-bottom: 24px; }
        .step {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        .step.success { background: #ecfdf5; color: #065f46; }
        .step.error { background: #fef2f2; color: #991b1b; }
        .step.info { background: #eff6ff; color: #1e40af; }
        .icon { font-size: 1.2rem; flex-shrink: 0; margin-top: 1px; }
        .summary {
            margin-top: 24px;
            padding: 20px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 1.1rem;
            text-align: center;
        }
        .summary.ok { background: #d1fae5; color: #065f46; }
        .summary.fail { background: #fee2e2; color: #991b1b; }
        .table-list {
            margin-top: 20px;
            background: #f9fafb;
            border-radius: 12px;
            padding: 16px 20px;
            border: 1px solid #e5e7eb;
        }
        .table-list h3 { color: #00433a; margin-bottom: 12px; font-size: 1rem; }
        .table-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.9rem;
        }
        .table-item:last-child { border-bottom: none; }
        .table-name { font-weight: 600; color: #374151; }
        .table-desc { color: #6b7280; }
        .warning {
            margin-top: 20px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            padding: 16px;
            border-radius: 12px;
            font-size: 0.85rem;
            line-height: 1.5;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>🗄️ Setup Database Laughndry</h1>
    <p class="subtitle">Membuat database dan tabel secara otomatis...</p>';

$success_count = 0;
$total_steps = 0;

// Helper function
function logStep($type, $message) {
    $icons = ['success' => '✅', 'error' => '❌', 'info' => 'ℹ️'];
    echo "<div class='step {$type}'>
            <span class='icon'>{$icons[$type]}</span>
            <span>{$message}</span>
          </div>";
}

// ═══════════════════════════════════════════════════════════
// STEP 1: Koneksi ke MySQL
// ═══════════════════════════════════════════════════════════
$total_steps++;
try {
    $pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    logStep('success', 'Berhasil terhubung ke MySQL');
    $success_count++;
} catch (PDOException $e) {
    logStep('error', 'Gagal terhubung ke MySQL: ' . $e->getMessage() . '<br><br><span style="font-size:0.85rem;color:#854d0e;background:#fef9c3;padding:8px 12px;border-radius:8px;display:inline-block;border:1px solid #fef08a;">🔎 <b>Info Terdeteksi di Server:</b><br>Host: <code>' . htmlspecialchars($db_host) . '</code><br>User: <code>' . htmlspecialchars($db_user) . '</code><br>Database: <code>' . htmlspecialchars($db_name) . '</code></span>');
    echo '<div class="summary fail">Setup gagal. Pastikan detail variabel database di Railway sudah benar!</div>';
    echo '</div></body></html>';
    exit;
}

// ═══════════════════════════════════════════════════════════
// STEP 2: Buat Database
// ═══════════════════════════════════════════════════════════
$total_steps++;
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    logStep('success', "Database <b>{$db_name}</b> berhasil dibuat / sudah ada");
    $success_count++;
} catch (PDOException $e) {
    logStep('error', 'Gagal membuat database: ' . $e->getMessage());
}

// Gunakan database
$pdo->exec("USE `$db_name`");

// ═══════════════════════════════════════════════════════════
// DROP TABLES LAMA & BARU AGAR CLEAN REBUILD
// ═══════════════════════════════════════════════════════════
try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("DROP TABLE IF EXISTS `order_item`, `order`, `customer`, `user`, `order_items`, `orders`, `customers`, `users`;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
} catch (PDOException $e) {
    logStep('info', 'Catatan saat membersihkan tabel lama: ' . $e->getMessage());
}

// ═══════════════════════════════════════════════════════════
// STEP 3: Buat Tabel user (untuk login admin & staf)
// ═══════════════════════════════════════════════════════════
$total_steps++;
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `user` (
            `id`            INT AUTO_INCREMENT PRIMARY KEY,
            `username`      VARCHAR(50) NOT NULL UNIQUE,
            `password_hash` VARCHAR(255) NOT NULL,
            `nama_lengkap`  VARCHAR(100) NOT NULL,
            `role`          ENUM('admin', 'staff') DEFAULT 'admin',
            `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
    logStep('success', 'Tabel <b>user</b> berhasil dibuat — Data login admin/staf');
    $success_count++;
} catch (PDOException $e) {
    logStep('error', 'Gagal membuat tabel user: ' . $e->getMessage());
}

// ═══════════════════════════════════════════════════════════
// STEP 4: Buat Tabel customer (data pelanggan)
// ═══════════════════════════════════════════════════════════
$total_steps++;
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `customer` (
            `id`         INT AUTO_INCREMENT PRIMARY KEY,
            `nama`       VARCHAR(100) NOT NULL,
            `telepon`    VARCHAR(20) NOT NULL,
            `alamat`     TEXT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
    logStep('success', 'Tabel <b>customer</b> berhasil dibuat — Data pelanggan');
    $success_count++;
} catch (PDOException $e) {
    logStep('error', 'Gagal membuat tabel customer: ' . $e->getMessage());
}

// ═══════════════════════════════════════════════════════════
// STEP 5: Buat Tabel order (pesanan)
// ═══════════════════════════════════════════════════════════
$total_steps++;
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `order` (
            `id`                INT AUTO_INCREMENT PRIMARY KEY,
            `customer_id`       INT NOT NULL,
            `user_id`           INT DEFAULT NULL,
            `total_harga`       INT NOT NULL DEFAULT 0,
            `metode_bayar`      VARCHAR(30) NOT NULL DEFAULT 'tunai',
            `bank`              VARCHAR(20) DEFAULT NULL,
            `status`            ENUM('pending', 'diproses', 'cuci', 'setrika', 'selesai', 'siap diambil', 'sudah diambil') DEFAULT 'pending',
            `snap_token`        VARCHAR(255) DEFAULT NULL,
            `midtrans_order_id` VARCHAR(100) DEFAULT NULL,
            `payment_status`    VARCHAR(30) DEFAULT NULL,
            `created_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`customer_id`) REFERENCES `customer`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB
    ");
    logStep('success', 'Tabel <b>order</b> berhasil dibuat — Data pesanan');
    $success_count++;
} catch (PDOException $e) {
    logStep('error', 'Gagal membuat tabel order: ' . $e->getMessage());
}

// ═══════════════════════════════════════════════════════════
// STEP 6: Buat Tabel order_item (detail item per pesanan)
// ═══════════════════════════════════════════════════════════
$total_steps++;
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `order_item` (
            `id`          INT AUTO_INCREMENT PRIMARY KEY,
            `order_id`    INT NOT NULL,
            `kategori`    VARCHAR(100) NOT NULL,
            `nama_item`   VARCHAR(100) NOT NULL,
            `harga`       INT NOT NULL,
            `qty`         INT NOT NULL DEFAULT 1,
            `subtotal`    INT NOT NULL DEFAULT 0,
            FOREIGN KEY (`order_id`) REFERENCES `order`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB
    ");
    logStep('success', 'Tabel <b>order_item</b> berhasil dibuat — Detail item pesanan');
    $success_count++;
} catch (PDOException $e) {
    logStep('error', 'Gagal membuat tabel order_item: ' . $e->getMessage());
}

// ═══════════════════════════════════════════════════════════
// STEP 7: Insert default admin user
// ═══════════════════════════════════════════════════════════
$total_steps++;
try {
    // Cek apakah admin sudah ada
    $stmt = $pdo->query("SELECT COUNT(*) FROM `user` WHERE `username` = 'admin'");
    $exists = $stmt->fetchColumn();
    
    if ($exists == 0) {
        $hashed = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO `user` (`username`, `password_hash`, `nama_lengkap`, `role`) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', $hashed, 'Administrator', 'admin']);
        logStep('success', 'Akun admin default berhasil dibuat — <b>admin / admin123</b>');
    } else {
        logStep('info', 'Akun admin sudah ada, tidak dibuat ulang');
    }
    $success_count++;
} catch (PDOException $e) {
    logStep('error', 'Gagal membuat akun admin: ' . $e->getMessage());
}

// ═══════════════════════════════════════════════════════════
// STEP 8: Insert sample customer data
// ═══════════════════════════════════════════════════════════
$total_steps++;
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM `customer`");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $stmt = $pdo->prepare("INSERT INTO `customer` (`nama`, `telepon`, `alamat`) VALUES (?, ?, ?)");
        $stmt->execute(['Budi Santoso', '081234567890', 'Jl. BSD Raya No. 45, Serpong, Tangerang Selatan']);
        $stmt->execute(['Siti Rahayu', '085678901234', 'Jl. Alam Sutera Boulevard, Serpong Utara']);
        $stmt->execute(['Ahmad Rizky', '087890123456', 'Perumahan Graha Raya Blok C5 No. 12, Pondok Aren']);
        logStep('success', 'Data contoh pelanggan berhasil ditambahkan (3 pelanggan)');
    } else {
        logStep('info', 'Data pelanggan sudah ada, tidak ditambahkan ulang');
    }
    $success_count++;
} catch (PDOException $e) {
    logStep('error', 'Gagal menambah data contoh: ' . $e->getMessage());
}

// ═══════════════════════════════════════════════════════════
// STEP 9: Insert sample order data
// ═══════════════════════════════════════════════════════════
$total_steps++;
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM `order`");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Order 1: Budi Santoso (Diproses oleh Admin ID 1)
        $pdo->exec("INSERT INTO `order` (`customer_id`, `user_id`, `total_harga`, `metode_bayar`, `status`) VALUES (1, 1, 47500, 'qris', 'diproses')");
        $order1_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO `order_item` (`order_id`, `kategori`, `nama_item`, `harga`, `qty`, `subtotal`) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$order1_id, 'Cuci - Setrika Min 3 Kg', 'Reguler (3 Hari)', 8000, 5, 40000]);
        $stmt->execute([$order1_id, 'Cuci - Setrika Min 3 Kg', 'Express 6 jam', 14000, 1, 14000]);

        // Order 2: Siti Rahayu (Belum diproses, user_id NULL)
        $pdo->exec("INSERT INTO `order` (`customer_id`, `user_id`, `total_harga`, `metode_bayar`, `status`) VALUES (2, NULL, 35000, 'transfer', 'pending')");
        $order2_id = $pdo->lastInsertId();
        $stmt->execute([$order2_id, 'Satuan Bedcover - Seprei', 'Bedcover Besar', 35000, 1, 35000]);

        // Order 3: Ahmad Rizky (Selesai diproses oleh Admin ID 1)
        $pdo->exec("INSERT INTO `order` (`customer_id`, `user_id`, `total_harga`, `metode_bayar`, `bank`, `status`) VALUES (3, 1, 10000, 'tunai', NULL, 'selesai')");
        $order3_id = $pdo->lastInsertId();
        $stmt->execute([$order3_id, 'Self Service', 'Mesin 8 Kg', 10000, 1, 10000]);

        logStep('success', 'Data contoh pesanan berhasil ditambahkan (3 pesanan)');
    } else {
        logStep('info', 'Data pesanan sudah ada, tidak ditambahkan ulang');
    }
    $success_count++;
} catch (PDOException $e) {
    logStep('error', 'Gagal menambah data pesanan: ' . $e->getMessage());
}

// ═══════════════════════════════════════════════════════════
// HASIL AKHIR
// ═══════════════════════════════════════════════════════════
if ($success_count === $total_steps) {
    echo '<div class="summary ok">✅ Setup berhasil! ' . $success_count . '/' . $total_steps . ' langkah selesai.</div>';
} else {
    echo '<div class="summary fail">⚠️ Sebagian gagal: ' . $success_count . '/' . $total_steps . ' langkah berhasil.</div>';
}

// Tampilkan ringkasan tabel
echo '
<div class="table-list">
    <h3>📋 Tabel yang Dibuat:</h3>
    <div class="table-item">
        <span class="table-name">user</span>
        <span class="table-desc">Login admin/staf (username, password hash, role)</span>
    </div>
    <div class="table-item">
        <span class="table-name">customer</span>
        <span class="table-desc">Data pelanggan (nama, telepon, alamat)</span>
    </div>
    <div class="table-item">
        <span class="table-name">order</span>
        <span class="table-desc">Data pesanan (total, metode bayar, status, user_id pengelola)</span>
    </div>
    <div class="table-item">
        <span class="table-name">order_item</span>
        <span class="table-desc">Detail item per pesanan (kategori, harga, qty, subtotal)</span>
    </div>
</div>

<div class="warning">
    ⚠️ <b>PENTING:</b> Setelah setup berhasil, <b>hapus file ini</b> (setup_database.php) dari folder project demi keamanan.
    Atau pindahkan ke folder yang tidak bisa diakses publik.
</div>';

echo '</div></body></html>';
?>
