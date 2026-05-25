<?php
/**
 * api/checkout.php — Simpan pesanan ke database SETELAH pembayaran berhasil
 * 
 * Dipanggil HANYA dari Snap onSuccess callback.
 * Data order baru masuk ke DB setelah pembayaran dikonfirmasi.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

$nama              = trim($input['nama'] ?? '');
$telepon           = trim($input['telepon'] ?? '');
$alamat            = trim($input['alamat'] ?? '');
$items             = $input['items'] ?? [];
$payment_type      = $input['payment_type'] ?? 'midtrans';
$midtrans_order_id = $input['midtrans_order_id'] ?? null;

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

try {
    $pdo->beginTransaction();

    // ═══════════════════════════════════════════
    // 1. Cek / buat pelanggan
    // ═══════════════════════════════════════════
    $stmt = $pdo->prepare("SELECT id FROM customers WHERE telepon = ?");
    $stmt->execute([$telepon]);
    $existing = $stmt->fetch();

    if ($existing) {
        $customer_id = $existing['id'];
        $stmt = $pdo->prepare("UPDATE customers SET nama = ?, alamat = ? WHERE id = ?");
        $stmt->execute([$nama, $alamat, $customer_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO customers (nama, telepon, alamat) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $telepon, $alamat]);
        $customer_id = $pdo->lastInsertId();
    }

    // ═══════════════════════════════════════════
    // 2. Hitung total
    // ═══════════════════════════════════════════
    $total_harga = 0;
    foreach ($items as $item) {
        $harga = intval(preg_replace('/[^0-9]/', '', $item['price'] ?? '0'));
        $qty   = intval($item['qty'] ?? 1);
        $total_harga += $harga * $qty;
    }

    // Tentukan status berdasarkan metode bayar
    $is_tunai       = $input['is_tunai'] ?? false;
    $order_status   = $is_tunai ? 'pending' : 'diproses';
    $pay_status     = $is_tunai ? null : 'settlement';

    $stmt = $pdo->prepare("
        INSERT INTO orders (customer_id, total_harga, metode_bayar, status, midtrans_order_id, payment_status) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$customer_id, $total_harga, $payment_type, $order_status, $midtrans_order_id, $pay_status]);
    $order_id = $pdo->lastInsertId();

    // ═══════════════════════════════════════════
    // 4. Simpan order items
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

    $pdo->commit();

    echo json_encode([
        'success'     => true,
        'message'     => 'Pesanan berhasil disimpan!',
        'order_id'    => $order_id,
        'total_harga' => $total_harga,
    ]);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menyimpan pesanan: ' . $e->getMessage(),
    ]);
}
