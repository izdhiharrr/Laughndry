<?php
/**
 * api/update_payment.php — Update metode pembayaran setelah user memilih
 * 
 * Dipanggil setelah order disimpan, saat user memilih metode bayar di popup.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['order_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Order ID wajib diisi']);
    exit;
}

$order_id = intval($input['order_id']);
$metode   = $input['metode_bayar'] ?? 'tunai';
$bank     = $input['bank'] ?? null;

// Validasi metode
$valid_methods = ['qris', 'transfer', 'tunai'];
if (!in_array($metode, $valid_methods)) {
    $metode = 'tunai';
}

try {
    $stmt = $pdo->prepare("UPDATE orders SET metode_bayar = ?, bank = ? WHERE id = ?");
    $stmt->execute([$metode, $bank, $order_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Metode pembayaran berhasil diupdate',
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal update metode bayar: ' . $e->getMessage(),
    ]);
}
