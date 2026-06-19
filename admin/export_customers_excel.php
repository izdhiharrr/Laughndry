<?php
/**
 * export_customers_excel.php — Generate Customer Excel (XLSX) Reports using PhpSpreadsheet
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
$customers = $pdo->query("SELECT * FROM customer $where_clause ORDER BY nama ASC")->fetchAll();

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Pelanggan');

// Show grid lines
$sheet->setShowGridlines(true);

// 1. Title Block
$sheet->mergeCells('A1:E1');
$sheet->setCellValue('A1', 'Laporan Profil Pelanggan Laughndry');
$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->mergeCells('A2:E2');
$sheet->setCellValue('A2', 'Periode: ' . $title_period . ' | Total Pelanggan: ' . count($customers));
$sheet->getStyle('A2')->getFont()->setSize(11)->setItalic(true);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// 2. Table Headers
$headers = [
    'A4' => 'ID Pelanggan',
    'B4' => 'Nama Pelanggan',
    'C4' => 'Alamat',
    'D4' => 'Nomor Telepon',
    'E4' => 'Tanggal Terdaftar'
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
$sheet->getStyle('A4:E4')->applyFromArray($headerStyle);
$sheet->getRowDimension(4)->setRowHeight(25);

// 3. Write Data Rows
$row = 5;

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

foreach ($customers as $cust) {
    $sheet->setCellValue('A' . $row, $cust['id']);
    $sheet->setCellValue('B' . $row, $cust['nama']);
    $sheet->setCellValue('C' . $row, $cust['alamat']);
    $sheet->setCellValue('D' . $row, $cust['telepon']);
    $sheet->setCellValue('E' . $row, date('d M Y', strtotime($cust['created_at'])));
    
    // Alignments
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($dataStyle);
    $sheet->getRowDimension($row)->setRowHeight(20);
    $row++;
}

// Auto-fit column widths
foreach (range('A', 'E') as $col) {
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
    $filename = 'Laporan_Pelanggan_Bulan_' . $nama_bulan . '_' . $tahun . '.xlsx';
} elseif ($period === 'yearly') {
    $filename = 'Laporan_Pelanggan_Tahun_' . $tahun . '.xlsx';
} else {
    $filename = 'Laporan_Pelanggan_Semua_Waktu_' . date('Ymd') . '.xlsx';
}

// Write to browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
