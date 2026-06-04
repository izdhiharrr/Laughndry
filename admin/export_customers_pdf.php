<?php
/**
 * export_customers_pdf.php — Generate Customer PDF Report using Dompdf
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

$period = $_GET['period'] ?? 'all';

// Determine date range
$where_clause = "";
$title_period = "";

switch ($period) {
    case 'monthly':
        $where_clause = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $title_period = "Bulanan (" . date('d M Y', strtotime('-1 month')) . " - " . date('d M Y') . ")";
        break;
    case 'yearly':
        $where_clause = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        $title_period = "Tahunan (" . date('d M Y', strtotime('-1 year')) . " - " . date('d M Y') . ")";
        break;
    default:
        $where_clause = "";
        $title_period = "Semua Waktu";
        break;
}

// Fetch customers
$customers = $pdo->query("SELECT * FROM customers $where_clause ORDER BY nama ASC")->fetchAll();

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
    </style>
</head>
<body>
    <h2>Laporan Profil Pelanggan Laughndry</h2>
    <p class="subtitle">Periode: ' . $title_period . ' | Total Pelanggan Terdaftar: ' . count($customers) . '</p>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 25%;">Nama</th>
                <th style="width: 40%;">Alamat</th>
                <th style="width: 15%;">Nomor Telepon</th>
                <th style="width: 15%;">Terdaftar</th>
            </tr>
        </thead>
        <tbody>';

if (empty($customers)) {
    $html .= '<tr><td colspan="5" style="text-align:center;">Tidak ada data pelanggan terdaftar pada periode ini.</td></tr>';
} else {
    foreach ($customers as $cust) {
        $html .= '<tr>
            <td>' . $cust['id'] . '</td>
            <td>' . htmlspecialchars($cust['nama']) . '</td>
            <td>' . htmlspecialchars($cust['alamat']) . '</td>
            <td>' . htmlspecialchars($cust['telepon']) . '</td>
            <td>' . date('d M Y', strtotime($cust['created_at'])) . '</td>
        </tr>';
    }
}

$html .= '
        </tbody>
    </table>
    
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
    $filename = 'Laporan_Pelanggan_Bulan_' . $nama_bulan . '_' . $tahun . '.pdf';
} elseif ($period === 'yearly') {
    $filename = 'Laporan_Pelanggan_Tahun_' . $tahun . '.pdf';
} else {
    $filename = 'Laporan_Pelanggan_Semua_Waktu_' . date('Ymd') . '.pdf';
}

$dompdf->stream($filename, ["Attachment" => true]);
