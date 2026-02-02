<?php
require 'vendor/autoload.php';
include 'config/config.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$conn = db_connect();

if (!isset($_FILES['file_excel'])) {
    die("File tidak ditemukan");
}

$fileTmp = $_FILES['file_excel']['tmp_name'];
$spreadsheet = IOFactory::load($fileTmp);
$sheet = $spreadsheet->getActiveSheet();

$total = 0;
$valid = 0;
$tidak_ditemukan = 0;

$dataHasil = [];

for ($row = 7; $row <= $sheet->getHighestRow(); $row++) {

    $nama = trim($sheet->getCell("J$row")->getValue());
    $passenger_id = trim($sheet->getCell("K$row")->getValue());

    if (empty($passenger_id)) continue;
    $total++;

    // Validasi NIK 16 digit
    if (!preg_match('/^[0-9]{16}$/', $passenger_id)) continue;

    $valid++;
    $kode_awal = substr($passenger_id, 0, 4);

    $query = mysqli_query(
        $conn,
        "SELECT asal_daerah FROM kode_daerah WHERE kode_awal = '$kode_awal'"
    );

    if (mysqli_num_rows($query) > 0) {
        $asal = mysqli_fetch_assoc($query)['asal_daerah'];
        $status = "found";
    } else {
        $asal = "Tidak ditemukan";
        $status = "notfound";
        $tidak_ditemukan++;
    }

    $dataHasil[] = [
        'nama' => $nama,
        'nik' => $passenger_id,
        'kode' => $kode_awal,
        'asal' => $asal,
        'status' => $status
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Hasil Analisis Excel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6f8;
    margin: 0;
    padding: 30px;
}

h2 {
    margin-bottom: 20px;
}

.summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px,1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.card {
    background: white;
    padding: 20px;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.card h3 {
    margin: 0;
    font-size: 14px;
    color: #666;
}

.card p {
    font-size: 26px;
    font-weight: bold;
    margin-top: 10px;
}

.table-wrapper {
    background: white;
    padding: 20px;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    overflow: auto;
    max-height: 500px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead th {
    position: sticky;
    top: 0;
    background: #0052A3;
    color: white;
    padding: 12px;
    text-align: left;
}

tbody td {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.badge {
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.found {
    background: #c8e6c9;
    color: #256029;
}

.notfound {
    background: #ffcdd2;
    color: #b71c1c;
}

.back {
    margin-top: 25px;
    display: inline-block;
    text-decoration: none;
    background: #FFC107;
    color: #000;
    padding: 10px 20px;
    font-weight: bold;
    border-radius: 4px;
}
</style>
</head>

<body>

<h2>📊 Hasil Analisis Data Penumpang (Excel)</h2>

<div class="summary">
    <div class="card">
        <h3>Total Data Dibaca</h3>
        <p><?= $total ?></p>
    </div>
    <div class="card">
        <h3>NIK Valid</h3>
        <p><?= $valid ?></p>
    </div>
    <div class="card">
        <h3>Tidak Ditemukan</h3>
        <p><?= $tidak_ditemukan ?></p>
    </div>
</div>

<div class="table-wrapper">
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>NIK</th>
            <th>Kode</th>
            <th>Asal Daerah</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($dataHasil as $i => $d): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($d['nama']) ?></td>
            <td><?= $d['nik'] ?></td>
            <td><?= $d['kode'] ?></td>
            <td><?= htmlspecialchars($d['asal']) ?></td>
            <td>
                <span class="badge <?= $d['status'] ?>">
                    <?= $d['status'] === 'found' ? 'Ditemukan' : 'Tidak ditemukan' ?>
                </span>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<a href="dashboard.php" class="back">← Kembali ke Dashboard</a>

</body>
</html>
