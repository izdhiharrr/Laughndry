<?php
/**
 * export_pdf.php — Generate PDF Reports using Dompdf
 */
session_start();

// Ensure user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die('Akses ditolak. Silakan login sebagai admin.');
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$period = $_GET['period'] ?? 'weekly';

// Determine date range
$where_clause = "";
$title_period = "";

switch ($period) {
    case 'monthly':
        $where_clause = "WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $title_period = "Bulanan (" . date('d M Y', strtotime('-1 month')) . " - " . date('d M Y') . ")";
        break;
    case 'yearly':
        $where_clause = "WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        $title_period = "Tahunan (" . date('d M Y', strtotime('-1 year')) . " - " . date('d M Y') . ")";
        break;
    default:
        $where_clause = "";
        $title_period = "Semua Waktu";
        break;
}

// Fetch data
$sql = "
    SELECT 
        o.id,
        c.nama AS customer_nama,
        o.total_harga,
        o.metode_bayar,
        o.status,
        o.created_at,
        GROUP_CONCAT(oi.kategori SEPARATOR ', ') AS kategori_list,
        GROUP_CONCAT(CONCAT(oi.nama_item, ' (x', oi.qty, ')') SEPARATOR ', ') AS item_list,
        SUM(oi.qty) AS total_qty
    FROM orders o
    JOIN customers c ON o.customer_id = c.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    $where_clause
    GROUP BY o.id
    ORDER BY o.created_at DESC
";
$orders = $pdo->query($sql)->fetchAll();

// Calculate total revenue
$total_revenue = 0;
foreach ($orders as $order) {
    if ($order['status'] !== 'batal') { // Assuming batal isn't counted if it exists
        $total_revenue += $order['total_harga'];
    }
}

// Build HTML
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: "Helvetica", "Arial", sans-serif; font-size: 12px; color: #333; }
        h2 { text-align: center; color: #00433a; margin-bottom: 5px; }
        p.subtitle { text-align: center; color: #666; margin-top: 0; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #035D51; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .summary { margin-top: 20px; font-weight: bold; font-size: 14px; text-align: right; }
        .status-badge { text-transform: capitalize; }
    </style>
</head>
<body>
    <h2>Laporan Pesanan Laughndry</h2>
    <p class="subtitle">Periode: ' . $title_period . '</p>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Layanan & Item</th>
                <th>Metode Bayar</th>
                <th>Status</th>
                <th>Total (Rp)</th>
            </tr>
        </thead>
        <tbody>';

if (empty($orders)) {
    $html .= '<tr><td colspan="7" style="text-align:center;">Tidak ada data pesanan pada periode ini.</td></tr>';
} else {
    foreach ($orders as $order) {
        $html .= '<tr>
            <td>#' . $order['id'] . '</td>
            <td>' . date('d/m/Y H:i', strtotime($order['created_at'])) . '</td>
            <td>' . htmlspecialchars($order['customer_nama']) . '</td>
            <td>
                <b>' . htmlspecialchars($order['kategori_list'] ?? '-') . '</b><br>
                ' . htmlspecialchars($order['item_list'] ?? '-') . '
            </td>
            <td style="text-transform: capitalize;">' . $order['metode_bayar'] . '</td>
            <td class="status-badge">' . $order['status'] . '</td>
            <td style="text-align:right;">' . number_format($order['total_harga'], 0, ',', '.') . '</td>
        </tr>';
    }
}

$html .= '
        </tbody>
    </table>
    
    <div class="summary">
        Total Pendapatan: Rp ' . number_format($total_revenue, 0, ',', '.') . '
        <br>
        Total Pesanan: ' . count($orders) . '
    </div>
    
    <p style="margin-top: 50px; font-size: 10px; text-align: center; color: #999;">
        Digenerate pada: ' . date('d M Y H:i:s') . ' oleh Sistem Admin Laughndry
    </p>
</body>
</html>
';

// Setup Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Indonesian Month Helper
$bulan_indo = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
$nama_bulan = $bulan_indo[(int)date('m')];
$tahun = date('Y');

if ($period === 'monthly') {
    $filename = 'Laporan_Laughndry_Bulan_' . $nama_bulan . '_' . $tahun . '.pdf';
} elseif ($period === 'yearly') {
    $filename = 'Laporan_Laughndry_Tahun_' . $tahun . '.pdf';
} else {
    $filename = 'Laporan_Laughndry_Semua_Waktu_' . date('Ymd') . '.pdf';
}

$dompdf->stream($filename, ["Attachment" => true]);
