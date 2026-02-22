<?php
/**
 * proses_excel.php
 * - Memproses file upload Excel (mode excel / birthday)
 * - Menyimpan $dataHasil + meta ke session
 * - Redirect ke hasil_excel.php atau hasil_birthday.php
 */

require 'vendor/autoload.php';
require_once 'config/config.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (session_status() === PHP_SESSION_NONE) session_start();

// Authentication
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login/login.php");
    exit;
}

// Validasi file upload
if (isset($_FILES['file_excel']) && $_FILES['file_excel']['error'] === 0) {
    $mode = "excel";
    $fileTmp = $_FILES['file_excel']['tmp_name'];
} elseif (isset($_FILES['file_birthday']) && $_FILES['file_birthday']['error'] === 0) {
    $mode = "birthday";
    $fileTmp = $_FILES['file_birthday']['tmp_name'];
} else {
    // Jika tidak ada file, kembali ke dashboard atau tampilkan error sederhana
    $_SESSION['flash_error'] = "File Excel tidak ditemukan atau gagal diunggah.";
    header("Location: dashboard.php");
    exit;
}

// Pastikan Zip extension untuk phpspreadsheet
if (!class_exists('ZipArchive')) {
    $_SESSION['flash_error'] = "Ekstensi ZIP belum aktif di PHP.";
    header("Location: dashboard.php");
    exit;
}

// koneksi db (hanya dipakai untuk lookup kode daerah)
$conn = db_connect();

// Load spreadsheet
$spreadsheet = IOFactory::load($fileTmp);
$sheet = $spreadsheet->getActiveSheet();

// Ambil train name (POST override > scan Excel)
$train_name = isset($_POST['nama_ka']) ? trim($_POST['nama_ka']) : '';

// scan area atas untuk pola "Train Name : ..." atau nilai di sel populer
if ($train_name === '') {
    $found = '';
    $cols = range('A','L');
    for ($r = 1; $r <= 10 && $found === ''; $r++) {
        foreach ($cols as $c) {
            try {
                $cellVal = trim((string)$sheet->getCell($c.$r)->getValue());
            } catch (Exception $e) {
                $cellVal = '';
            }
            if ($cellVal === '') continue;

            if (preg_match('/\btrain\s*name\s*[:\-]\s*(.+)/i', $cellVal, $m)) {
                $found = trim($m[1]);
                break;
            }

            if (preg_match('/^train\s*name$/i', $cellVal)) {
                $nextCol = chr(ord($c) + 1);
                try { $right = trim((string)$sheet->getCell($nextCol.$r)->getValue()); } catch (Exception $e) { $right = ''; }
                if ($right !== '') { $found = $right; break; }
            }

            if (preg_match('/\btrain\b/i', $cellVal)) {
                try { $below = trim((string)$sheet->getCell($c.($r+1))->getValue()); } catch (Exception $e) { $below = ''; }
                if ($below !== '' && preg_match('/[A-Za-z]/',$below) && strlen($below) > 2) { $found = $below; break; }
            }
        }
    }
    if ($found !== '') $train_name = $found;
}

// fallback ke sel populer
if ($train_name === '') {
    $candidates = ['A3','A2','B2','B3','C2','C3','E7'];
    foreach ($candidates as $cell) {
        try { $val = trim((string)$sheet->getCell($cell)->getValue()); } catch (Exception $e) { $val = ''; }
        if ($val === '') continue;
        if (preg_match('/\btrain\s*name\b/i', $val) && preg_match('/[:\-]\s*(.+)/', $val, $m)) {
            $train_name = trim($m[1]); break;
        }
        if (preg_match('/[A-Za-z]/',$val) && strlen($val) > 2 && !preg_match('/train|trip|manifest|generated|number|no/i', $val)) {
            $train_name = $val; break;
        }
    }
}

// ambil daftar stasiun unik (kolom I, mulai baris 7)
$stasiunSet = [];
for ($row = 7; $row <= $sheet->getHighestRow(); $row++) {
    $route = trim((string)$sheet->getCell("I{$row}")->getValue());
    $route = str_replace(' ', '', $route);
    if ($route !== '') {
        $parts = explode('-', $route);
        if (isset($parts[0]) && $parts[0] !== '') $stasiunSet[$parts[0]] = true;
        if (isset($parts[1]) && $parts[1] !== '') $stasiunSet[$parts[1]] = true;
    }
}
$stasiunList = array_keys($stasiunSet);
sort($stasiunList);

// filter (dari form)
$filter_asal   = isset($_POST['stasiun_asal']) ? trim($_POST['stasiun_asal']) : '';
$filter_tujuan = isset($_POST['stasiun_tujuan']) ? trim($_POST['stasiun_tujuan']) : '';

// proses data baris per baris
$dataHasil = [];
$total = $valid = $tidak_ditemukan = 0;
$today = date('m-d');

for ($row = 7; $row <= $sheet->getHighestRow(); $row++) {
    $nama = trim((string)$sheet->getCell("J{$row}")->getValue());
    $nik  = trim((string)$sheet->getCell("K{$row}")->getValue());
    $route = trim((string)$sheet->getCell("I{$row}")->getValue());
    $route = str_replace(' ', '', $route);
    $tripDate = trim((string)$sheet->getCell("F{$row}")->getFormattedValue());

    if ($nik === '') continue;
    $total++;

    if ($mode === "excel") {
        $route_parts = explode('-', $route);
        $stasiun_asal_excel   = isset($route_parts[0]) ? trim($route_parts[0]) : '';
        $stasiun_tujuan_excel = isset($route_parts[1]) ? trim($route_parts[1]) : '';

        if ($filter_asal !== '' && $stasiun_asal_excel !== $filter_asal) continue;
        if ($filter_tujuan !== '' && $stasiun_tujuan_excel !== $filter_tujuan) continue;
        if (!preg_match('/^[0-9]{16}$/', $nik)) continue;

        $valid++;
        $kode_awal = substr($nik, 0, 4);
        $query = mysqli_query($conn, "SELECT asal_daerah FROM kode_daerah WHERE kode_awal = '$kode_awal'");
        if ($query && mysqli_num_rows($query) > 0) {
            $asal = mysqli_fetch_assoc($query)['asal_daerah'];
            $status = 'found';
        } else {
            $asal = 'Tidak ditemukan';
            $status = 'notfound';
            $tidak_ditemukan++;
        }

        $dataHasil[] = [
            'nama' => $nama,
            'nik'  => $nik,
            'route'=> $route,
            'asal_daerah' => $asal,
            'status' => $status
        ];
    }

    if ($mode === "birthday") {
        if ($tripDate == '' || !preg_match('/^[0-9]{16}$/', $nik)) continue;
        // Ambil tanggal trip
        try {
            $tripDateObj = new DateTime($tripDate);
        } catch (Exception $e) {
            continue;
        }

        // Ambil tanggal lahir dari NIK
        // NIK: ddmmyy....
        $dd = (int) substr($nik, 6, 2);
        $mm = (int) substr($nik, 8, 2);
        $yy = (int) substr($nik, 10, 2);

        // Koreksi untuk perempuan (DD + 40)
        if ($dd > 40) {
            $dd -= 40;
        }

        // Tentukan tahun trip
        $yearTrip = (int) $tripDateObj->format('Y');

        // Buat tanggal ulang tahun di tahun trip
        try {
            $birthThisYear = new DateTime($yearTrip . '-' . $mm . '-' . $dd);
        } catch (Exception $e) {
            continue;
        }

        // Hitung selisih hari (birth - trip)
        $diffDays = (int) $tripDateObj->diff($birthThisYear)->format('%r%a');

        // Cek H-2 s/d H+2
        if ($diffDays >= -2 && $diffDays <= 2) {
            $dataHasil[] = [
                'nama' => $nama,
                'nik'  => $nik,
                'trip' => $tripDate,
                'selisih_hari' => $diffDays
            ];
        }
    }
}

// simpan ke session untuk ditampilkan / diekspor
$_SESSION['export_data'] = $dataHasil;
$_SESSION['export_mode'] = $mode;
$_SESSION['export_filename'] = ($mode === 'excel') ? 'hasil_penumpang.xlsx' : 'ulang_tahun_penumpang.xlsx';
$_SESSION['export_nama_ka'] = $train_name;
$_SESSION['export_meta'] = [
    'total_rows' => $total,
    'valid' => $valid,
    'tidak_ditemukan' => $tidak_ditemukan,
    'stasiun_list' => $stasiunList
];

// redirect ke view yang sesuai
if ($mode === 'birthday') {
    header("Location: hasil_birthday.php");
    exit;
} else {
    header("Location: hasil_excel.php");
    exit;
}