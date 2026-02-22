<?php
// export_excel.php
require 'vendor/autoload.php';
session_start();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// validasi session
if (!isset($_SESSION['export_data']) || !is_array($_SESSION['export_data'])) {
    die('Tidak ada data untuk diekspor.');
}

$data = $_SESSION['export_data'];
$mode = isset($_SESSION['export_mode']) ? $_SESSION['export_mode'] : 'excel';
$filename = isset($_SESSION['export_filename']) ? $_SESSION['export_filename'] : 'hasil_penumpang.xlsx';

// buat spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// tulis header dan isi sesuai mode
if ($mode === 'excel') {
    // header
    $sheet->setCellValue('A1', 'Nama')
          ->setCellValue('B1', 'NIK')
          ->setCellValue('C1', 'Route')
          ->setCellValue('D1', 'Asal Daerah');

    $row = 2;
    foreach ($data as $r) {
        $sheet->setCellValue('A' . $row, $r['nama'] ?? '');
        $sheet->setCellValue('B' . $row, $r['nik'] ?? '');
        $sheet->setCellValue('C' . $row, $r['route'] ?? '');
        $sheet->setCellValue('D' . $row, $r['asal_daerah'] ?? '');
        $row++;
    }
} else {
    // birthday mode
    $sheet->setCellValue('A1', 'No')
          ->setCellValue('B1', 'Nama')
          ->setCellValue('C1', 'NIK')
          ->setCellValue('D1', 'Trip Date');

    $row = 2;
    foreach ($data as $i => $r) {
        $sheet->setCellValue('A' . $row, $i + 1);
        $sheet->setCellValue('B' . $row, $r['nama'] ?? '');
        $sheet->setCellValue('C' . $row, $r['nik'] ?? '');
        $sheet->setCellValue('D' . $row, $r['trip'] ?? '');
        $row++;
    }
}

// optional: autosize columns (agar rapi)
foreach (range('A', $sheet->getHighestColumn()) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// kirim ke browser sebagai file download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// bersihkan data export dari session supaya tidak tersisa
unset($_SESSION['export_data'], $_SESSION['export_mode'], $_SESSION['export_filename']);
exit;