<?php
/**
 * check_new_orders.php — API untuk mengecek pesanan baru secara real-time
 * Dipanggil via AJAX polling dari admin.php
 */
session_start();
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

// Cek apakah admin sudah login
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$last_id = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;

if ($last_id <= 0) {
    echo json_encode(['success' => true, 'new_orders' => []]);
    exit;
}

try {
    // Ambil pesanan baru dengan ID lebih besar dari last_id
    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            c.nama AS customer_nama,
            o.total_harga,
            o.created_at,
            GROUP_CONCAT(CONCAT(oi.nama_item, ' (x', oi.qty, ')') SEPARATOR ', ') AS item_list
        FROM `order` o
        JOIN customer c ON o.customer_id = c.id
        LEFT JOIN order_item oi ON o.id = oi.order_id
        WHERE o.id > ?
        GROUP BY o.id
        ORDER BY o.id ASC
    ");
    $stmt->execute([$last_id]);
    $new_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'new_orders' => $new_orders
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
