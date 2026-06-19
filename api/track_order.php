<?php
/**
 * api/track_order.php — Lacak pesanan berdasarkan nomor telepon
 * Dipanggil via AJAX dari index.php
 */
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/database.php';

$telepon = isset($_GET['telepon']) ? trim($_GET['telepon']) : '';

if (empty($telepon)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nomor telepon wajib diisi']);
    exit;
}

try {
    // Ambil daftar pesanan customer berdasarkan nomor telepon
    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            o.created_at,
            c.nama AS customer_nama,
            o.total_harga,
            o.metode_bayar,
            o.status,
            o.payment_status,
            GROUP_CONCAT(DISTINCT oi.kategori SEPARATOR ', ') AS kategori_list,
            GROUP_CONCAT(CONCAT(oi.nama_item, ' (x', oi.qty, ')') SEPARATOR ', ') AS item_list,
            SUM(oi.qty) AS total_qty
        FROM `order` o
        JOIN customer c ON o.customer_id = c.id
        LEFT JOIN order_item oi ON o.id = oi.order_id
        WHERE c.telepon = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$telepon]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'orders' => $orders
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal melacak pesanan: ' . $e->getMessage()
    ]);
}
