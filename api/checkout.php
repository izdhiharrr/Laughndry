<?php
/**
 * api/checkout.php — API Endpoint untuk menyimpan pesanan ke database
 * 
 * Menerima data JSON via POST dari halaman daftar-laundry.php
 * dan menyimpan ke tabel customers, orders, dan order_items.
 */

header('Content-Type: application/json');

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Koneksi database
require_once __DIR__ . '/../config/database.php';

// Ambil data JSON dari request body
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

// Validasi field wajib
$nama    = trim($input['nama'] ?? '');
$telepon = trim($input['telepon'] ?? '');
$alamat  = trim($input['alamat'] ?? '');
$metode  = $input['metode_bayar'] ?? 'tunai';
$bank    = $input['bank'] ?? null;
$items   = $input['items'] ?? [];

if (empty($nama) || empty($telepon) || empty($alamat)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nama, telepon, dan alamat wajib diisi']);
    exit;
}

if (empty($items)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Keranjang kosong, tidak ada item']);
    exit;
}

// Validasi metode bayar
$valid_methods = ['qris', 'transfer', 'tunai'];
if (!in_array($metode, $valid_methods)) {
    $metode = 'tunai';
}

try {
    // Mulai transaction — semua harus berhasil atau tidak sama sekali
    $pdo->beginTransaction();

    // ═══════════════════════════════════════════
    // 1. Cek apakah pelanggan sudah ada (by telepon)
    //    Jika sudah ada, update nama & alamat
    //    Jika belum, buat baru
    // ═══════════════════════════════════════════
    $stmt = $pdo->prepare("SELECT id FROM customers WHERE telepon = ?");
    $stmt->execute([$telepon]);
    $existing = $stmt->fetch();

    if ($existing) {
        $customer_id = $existing['id'];
        // Update data pelanggan jika ada perubahan
        $stmt = $pdo->prepare("UPDATE customers SET nama = ?, alamat = ? WHERE id = ?");
        $stmt->execute([$nama, $alamat, $customer_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO customers (nama, telepon, alamat) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $telepon, $alamat]);
        $customer_id = $pdo->lastInsertId();
    }

    // ═══════════════════════════════════════════
    // 2. Hitung total harga dari items
    // ═══════════════════════════════════════════
    $total_harga = 0;
    foreach ($items as $item) {
        $harga = intval(preg_replace('/[^0-9]/', '', $item['price'] ?? '0'));
        $qty   = intval($item['qty'] ?? 1);
        $total_harga += $harga * $qty;
    }

    // ═══════════════════════════════════════════
    // 3. Simpan order
    // ═══════════════════════════════════════════
    $stmt = $pdo->prepare("
        INSERT INTO orders (customer_id, total_harga, metode_bayar, bank, status) 
        VALUES (?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([$customer_id, $total_harga, $metode, $bank]);
    $order_id = $pdo->lastInsertId();

    // ═══════════════════════════════════════════
    // 4. Simpan setiap item pesanan
    // ═══════════════════════════════════════════
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, kategori, nama_item, harga, qty, subtotal) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($items as $item) {
        $kategori  = $item['category'] ?? '-';
        $nama_item = $item['name'] ?? '-';
        $harga     = intval(preg_replace('/[^0-9]/', '', $item['price'] ?? '0'));
        $qty       = intval($item['qty'] ?? 1);
        $subtotal  = $harga * $qty;

        $stmt->execute([$order_id, $kategori, $nama_item, $harga, $qty, $subtotal]);
    }

    // Commit semua perubahan
    $pdo->commit();

    // Berhasil!
    echo json_encode([
        'success'     => true,
        'message'     => 'Pesanan berhasil disimpan!',
        'order_id'    => $order_id,
        'customer_id' => $customer_id,
        'total_harga' => $total_harga,
    ]);

} catch (Exception $e) {
    // Gagal — rollback semua perubahan
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menyimpan pesanan: ' . $e->getMessage(),
    ]);
}
