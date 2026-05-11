<?php
/**
 * api/snap_token.php — Generate Midtrans Snap Token TANPA simpan ke database
 * 
 * Hanya membuat snap token untuk ditampilkan di popup.
 * Data order BELUM disimpan ke DB. Simpan hanya setelah pembayaran berhasil.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/midtrans.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

$nama    = trim($input['nama'] ?? '');
$telepon = trim($input['telepon'] ?? '');
$alamat  = trim($input['alamat'] ?? '');
$items   = $input['items'] ?? [];

if (empty($nama) || empty($telepon) || empty($alamat)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nama, telepon, dan alamat wajib diisi']);
    exit;
}

if (empty($items)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Keranjang kosong']);
    exit;
}

// Hitung total & siapkan item details untuk Midtrans
$total_harga = 0;
$midtrans_items = [];
$idx = 0;

foreach ($items as $item) {
    $harga = intval(preg_replace('/[^0-9]/', '', $item['price'] ?? '0'));
    $qty   = intval($item['qty'] ?? 1);
    $total_harga += $harga * $qty;

    $midtrans_items[] = [
        'id'       => 'ITEM-' . $idx,
        'price'    => $harga,
        'quantity' => $qty,
        'name'     => mb_substr(($item['name'] ?? '-') . ' (' . ($item['category'] ?? '-') . ')', 0, 50),
    ];
    $idx++;
}

// Generate temporary order ID (belum ada di DB)
$temp_order_id = 'LND-' . time() . '-' . rand(1000, 9999);

$snap_payload = [
    'transaction_details' => [
        'order_id'     => $temp_order_id,
        'gross_amount' => $total_harga,
    ],
    'item_details' => $midtrans_items,
    'customer_details' => [
        'first_name' => $nama,
        'phone'      => $telepon,
        'billing_address' => [
            'address' => $alamat,
        ],
    ],
];

// Panggil Midtrans Snap API
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => MIDTRANS_SNAP_URL,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($snap_payload),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode(MIDTRANS_SERVER_KEY . ':'),
    ],
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'cURL Error: ' . $curl_error]);
    exit;
}

$snap_response = json_decode($response, true);

if ($http_code !== 201 || empty($snap_response['token'])) {
    $error_msg = $snap_response['error_messages'][0] ?? ($snap_response['message'] ?? 'Unknown Midtrans error');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Midtrans Error: ' . $error_msg]);
    exit;
}

// Return snap token TANPA simpan ke DB
echo json_encode([
    'success'           => true,
    'snap_token'        => $snap_response['token'],
    'midtrans_order_id' => $temp_order_id,
    'total_harga'       => $total_harga,
]);
