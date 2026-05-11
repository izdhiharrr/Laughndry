<?php
/**
 * api/midtrans_notification.php — Webhook Handler untuk Midtrans Payment Notification
 * 
 * Midtrans akan mengirim POST request ke URL ini setiap kali ada perubahan status pembayaran.
 * URL ini harus didaftarkan di Midtrans Dashboard > Settings > Payment > Notification URL
 * 
 * Untuk testing lokal, gunakan ngrok/localtunnel agar Midtrans bisa mengakses localhost.
 * Contoh: https://abc123.ngrok.io/Laughndry-coin/api/midtrans_notification.php
 */

header('Content-Type: application/json');

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/midtrans.php';

// Ambil notification body dari Midtrans
$raw_body = file_get_contents('php://input');
$notification = json_decode($raw_body, true);

if (!$notification) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid notification data']);
    exit;
}

// ═══════════════════════════════════════════
// Verifikasi signature key untuk keamanan
// Signature = SHA512(order_id + status_code + gross_amount + server_key)
// ═══════════════════════════════════════════
$order_id     = $notification['order_id'] ?? '';
$status_code  = $notification['status_code'] ?? '';
$gross_amount = $notification['gross_amount'] ?? '';
$signature    = $notification['signature_key'] ?? '';

$expected_signature = hash('sha512', $order_id . $status_code . $gross_amount . MIDTRANS_SERVER_KEY);

if ($signature !== $expected_signature) {
    http_response_code(403);
    echo json_encode(['message' => 'Invalid signature']);
    exit;
}

// ═══════════════════════════════════════════
// Verifikasi ulang ke Midtrans API (double check)
// ═══════════════════════════════════════════
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => MIDTRANS_API_URL . '/' . $order_id . '/status',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode(MIDTRANS_SERVER_KEY . ':'),
    ],
]);

$response = curl_exec($ch);
curl_close($ch);

$status_response = json_decode($response, true);

if (!$status_response) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to verify with Midtrans']);
    exit;
}

$transaction_status = $status_response['transaction_status'] ?? '';
$fraud_status       = $status_response['fraud_status'] ?? '';
$payment_type       = $status_response['payment_type'] ?? '';

// ═══════════════════════════════════════════
// Update status order berdasarkan status transaksi
// ═══════════════════════════════════════════
try {
    // Cari order berdasarkan midtrans_order_id
    $stmt = $pdo->prepare("SELECT id, status FROM orders WHERE midtrans_order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    if (!$order) {
        http_response_code(404);
        echo json_encode(['message' => 'Order not found']);
        exit;
    }

    $new_status = null;
    $new_metode = $payment_type; // simpan metode bayar aktual dari Midtrans

    if ($transaction_status === 'capture') {
        // Untuk kartu kredit, cek fraud status
        if ($fraud_status === 'accept') {
            $new_status = 'diproses'; // Pembayaran berhasil → langsung diproses
        }
    } elseif ($transaction_status === 'settlement') {
        // Pembayaran selesai (VA, QRIS, GoPay, dll)
        $new_status = 'diproses';
    } elseif ($transaction_status === 'pending') {
        // Menunggu pembayaran
        $new_status = 'pending';
    } elseif (in_array($transaction_status, ['deny', 'cancel', 'expire'])) {
        // Pembayaran gagal/dibatalkan/kadaluarsa
        $new_status = 'pending'; // Tetap pending, bisa bayar ulang
    }

    if ($new_status !== null) {
        $stmt = $pdo->prepare("
            UPDATE orders SET status = ?, metode_bayar = ?, payment_status = ?, updated_at = NOW() 
            WHERE midtrans_order_id = ?
        ");
        $stmt->execute([$new_status, $new_metode, $transaction_status, $order_id]);
    }

    // Log notifikasi (optional, untuk debugging)
    error_log("[Midtrans] Order: {$order_id} | Status: {$transaction_status} | Payment: {$payment_type}");

    http_response_code(200);
    echo json_encode(['message' => 'OK']);

} catch (Exception $e) {
    error_log("[Midtrans Error] " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['message' => 'Internal server error']);
}
