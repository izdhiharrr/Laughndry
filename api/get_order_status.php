<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Order ID tidak valid']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT status, payment_status FROM `order` WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    if ($order) {
        echo json_encode([
            'success' => true,
            'status' => $order['status'],
            'payment_status' => $order['payment_status']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
