<?php
/**
 * export_excel.php — Generate Excel (XLSX) Reports using PhpSpreadsheet
 */
session_start();

// Ensure user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die('Akses ditolak. Silakan login sebagai admin.');
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
    FROM `order` o
    JOIN customer c ON o.customer_id = c.id
    LEFT JOIN order_item oi ON o.id = oi.order_id
    $where_clause
    GROUP BY o.id
    ORDER BY o.created_at DESC
";
$orders = $pdo->query($sql)->fetchAll();

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Pesanan');

// Show grid lines explicitly
$sheet->setShowGridlines(true);

// 1. Title Block
$sheet->mergeCells('A1:I1');
$sheet->setCellValue('A1', 'Laporan Pesanan Laughndry');
$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->mergeCells('A2:I2');
$sheet->setCellValue('A2', 'Periode: ' . $title_period);
$sheet->getStyle('A2')->getFont()->setSize(11)->setItalic(true);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// 2. Table Headers
$headers = [
    'A4' => 'ID',
    'B4' => 'Tanggal',
    'C4' => 'Pelanggan',
    'D4' => 'Jenis Layanan',
    'E4' => 'Detail Item',
    'F4' => 'Qty',
    'G4' => 'Total Harga',
    'H4' => 'Metode Bayar',
    'I4' => 'Status'
];

foreach ($headers as $cell => $text) {
    $sheet->setCellValue($cell, $text);
}

// Header styling
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '035D51'], // Theme green matching background
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'DDDDDD'],
        ],
    ],
];
$sheet->getStyle('A4:I4')->applyFromArray($headerStyle);
$sheet->getRowDimension(4)->setRowHeight(25);

// 3. Write Data Rows
$row = 5;
$total_revenue = 0;

$dataStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'EEEEEE'],
        ],
    ],
    'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];

foreach ($orders as $order) {
    $sheet->setCellValue('A' . $row, '#' . $order['id']);
    $sheet->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($order['created_at'])));
    $sheet->setCellValue('C' . $row, $order['customer_nama']);
    $sheet->setCellValue('D' . $row, $order['kategori_list'] ?? '-');
    $sheet->setCellValue('E' . $row, $order['item_list'] ?? '-');
    $sheet->setCellValue('F' . $row, (int)($order['total_qty'] ?? 0));
    $sheet->setCellValue('G' . $row, (double)$order['total_harga']);
    $sheet->setCellValue('H' . $row, ucfirst($order['metode_bayar']));
    $sheet->setCellValue('I' . $row, ucfirst($order['status']));
    
    // Formatting total harga as rupiah
    $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('"Rp" #,##0');
    
    // Alignments
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($dataStyle);
    $sheet->getRowDimension($row)->setRowHeight(20);
    
    if ($order['status'] !== 'batal') {
        $total_revenue += $order['total_harga'];
    }
    $row++;
}

// 4. Totals Row
// Add a blank row
$row++;

// Summary labels
$sheet->setCellValue('F' . $row, 'Total Pendapatan:');
$sheet->getStyle('F' . $row)->getFont()->setBold(true);
$sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$sheet->setCellValue('G' . $row, $total_revenue);
$sheet->getStyle('G' . $row)->getFont()->setBold(true);
$sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('"Rp" #,##0');

$row++;
$sheet->setCellValue('F' . $row, 'Total Pesanan:');
$sheet->getStyle('F' . $row)->getFont()->setBold(true);
$sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$sheet->setCellValue('G' . $row, count($orders));
$sheet->getStyle('G' . $row)->getFont()->setBold(true);
$sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

// Auto-fit column widths
foreach (range('A', 'I') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Indonesian Month Helper
$bulan_indo = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
$nama_bulan = $bulan_indo[(int)date('m')];
$tahun = date('Y');

if ($period === 'monthly') {
    $filename = 'Laporan_Laughndry_Bulan_' . $nama_bulan . '_' . $tahun . '.xlsx';
} elseif ($period === 'yearly') {
    $filename = 'Laporan_Laughndry_Tahun_' . $tahun . '.xlsx';
} else {
    $filename = 'Laporan_Laughndry_Semua_Waktu_' . date('Ymd') . '.xlsx';
}

// Write to browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
