<?php
/**
 * api/update_payment.php — Update status pembayaran setelah Midtrans Snap callback
 * 
 * Dipanggil dari frontend saat Snap popup mengembalikan hasil:
 * - onSuccess → status = 'diproses', payment_status = 'settlement'
 * - onPending → status = 'pending', payment_status = 'pending'
 * 
 * Ini adalah FALLBACK untuk localhost yang tidak bisa menerima webhook.
 * Di production, webhook tetap menjadi sumber utama update status.
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

$order_id       = intval($input['order_id']);
$payment_status = $input['payment_status'] ?? 'pending'; // settlement, pending, error
$payment_type   = $input['payment_type'] ?? 'midtrans';

// Map Midtrans payment status → order status
$status_map = [
    'settlement' => 'diproses',
    'capture'    => 'diproses',
    'pending'    => 'pending',
    'deny'       => 'pending',
    'cancel'     => 'pending',
    'expire'     => 'pending',
];

$new_status = $status_map[$payment_status] ?? 'pending';

try {
    $stmt = $pdo->prepare("
        UPDATE orders 
        SET status = ?, metode_bayar = ?, payment_status = ?, updated_at = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$new_status, $payment_type, $payment_status, $order_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Status pembayaran berhasil diupdate',
        'status'  => $new_status,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal update status: ' . $e->getMessage(),
    ]);
}
