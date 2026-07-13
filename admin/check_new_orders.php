<?php
/**
 * check_new_orders.php — API untuk mengecek pesanan yang butuh verifikasi secara real-time
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

try {
    // Ambil pesanan yang memerlukan verifikasi pembayaran dengan detail lengkap
    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            o.created_at,
            c.nama AS customer_nama,
            c.telepon AS customer_telepon,
            c.alamat AS customer_alamat,
            o.total_harga,
            o.metode_bayar,
            o.status,
            o.payment_status,
            o.bukti_bayar,
            o.alasan_tolak,
            GROUP_CONCAT(DISTINCT oi.kategori SEPARATOR ', ') AS kategori_list,
            GROUP_CONCAT(CONCAT(oi.nama_item, ' (x', oi.qty, ')') SEPARATOR ', ') AS item_list,
            GROUP_CONCAT(CONCAT(oi.nama_item, '|', oi.qty, '|', oi.subtotal) SEPARATOR '||') AS item_list_detail,
            SUM(oi.qty) AS total_qty
        FROM `order` o
        JOIN customer c ON o.customer_id = c.id
        LEFT JOIN order_item oi ON o.id = oi.order_id
        WHERE (o.payment_status = 'Menunggu Verifikasi' AND o.status = 'menunggu verifikasi')
           OR (o.metode_bayar = 'tunai' AND o.status = 'pending' AND (o.payment_status = 'Pending' OR o.payment_status = '' OR o.payment_status IS NULL))
        GROUP BY o.id
        ORDER BY o.id ASC
    ");
    $stmt->execute();
    $new_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'new_orders' => $new_orders
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
