<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) { 
    header("Location: login/login.php"); 
    exit; 
}

$dataHasil  = $_SESSION['export_data'] ?? [];
$train_name = $_SESSION['export_nama_ka'] ?? '';
$userName   = $_SESSION['username'] ?? 'Admin';
$jumlahData = count($dataHasil);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Hasil Asal Daerah Penumpang Kereta Api</title>
<meta name="viewport" content="width=device-width,initial-scale=1">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

<style>
:root{
    --blue:#0052A3;
    --green:#16a34a;
    --yellow:#FFC107;
    --red:#DC143C;
    --shadow:0 6px 18px rgba(0,0,0,.08);
}

*{box-sizing:border-box;margin:0;padding:0;}

body{
    font-family:'Inter',sans-serif;
    background:#f4f6f8;
}

/* ================= NAVBAR ================= */
.header{
    background:var(--blue);
    color:#fff;
    padding:15px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    position:relative;
}
.header::after{
    content:"";
    position:absolute;
    left:0;right:0;bottom:0;
    height:4px;
    background:var(--red);
}
.logo-wrap{
    background:#fff;
    padding:6px 10px;
    border-radius:4px;
}
.logo-wrap img{height:35px;}

.nav-right{
    display:flex;
    align-items:center;
    gap:14px;
    font-size:14px;
}

/* LOGOUT BUTTON (SESUAI PERMINTAANMU) */
.logout-btn {
    padding: 6px 12px;
    background: #FFC107;
    color: #fff;
    border-radius: 5px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: 0.3s;
}
.logout-btn:hover {
    background: #ffff;
}

/* ================= CONTAINER ================= */
.container{
    max-width:1200px;
    margin:24px auto;
    padding:0 20px;
}

.back-link{
    color:var(--blue);
    display:inline-block;
    margin-bottom:12px;
    text-decoration:none;
    font-weight:600;
}

/* TITLE: diperbesar sedikit tulisan dan padding supaya lebih proposional */
.title-wrap{
    text-align:center;
    margin-bottom:14px;
}
.title{
    background:var(--yellow);
    display:inline-block;
    /* === PERUBAHAN UTAMA === */
    padding:12px 36px;      /* sedikit lebih besar dari sebelumnya */
    font-size:20px;         /* memperbesar tulisan */
    border-radius:6px;      /* lebih lembut */
    box-shadow:var(--shadow);
    color:#000;
    font-weight:700;        /* lebih tebal agar kontras */
    letter-spacing:0.2px;   /* sedikit spasi huruf agar rapi */
}

/* ================= TABLE ================= */
.table-box{
    background:#fff;
    border-radius:8px;
    box-shadow:var(--shadow);
    overflow:hidden;
    margin-top:12px;
}
.table-header{
    padding:14px 18px;
    border-bottom:1px solid #eee;
}
.table-header-title{
    font-weight:700;
    color:#374151;
    font-size:15px;
}
.table-scroll{
    max-height:460px;
    overflow:auto;
}

table{
    width:100%;
    border-collapse:collapse;
}
thead th{
    position:sticky;
    top:0;
    background:var(--blue);
    color:#fff;
    padding:12px 14px;
    text-align:left;
    font-size:13px;
    font-weight:600;
}
tbody td{
    padding:11px 14px;
    border-bottom:1px solid #f0f0f0;
    font-size:13px;
    color:#374151;
}
tbody tr:nth-child(even){background:#f9fbfd;}
tbody tr:hover{background:#eff6ff;}

/* ================= EXPORT ================= */
.export-wrap{
    padding:12px 18px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-top:1px dashed #eef2f7;
    background:#fff;
}
.btn-export{
    padding:8px 14px;
    background:var(--green);
    color:#fff;
    border-radius:6px;
    text-decoration:none;
    font-weight:700;
    font-size:13px;
}
.btn-export.disabled{
    opacity:.6;
    pointer-events:none;
}

.no-data{
    padding:30px;
    text-align:center;
    color:#9ca3af;
}
</style>
</head>
<body>

<!-- NAVBAR -->
<div class="header">
    <div class="logo-wrap">
        <img src="assets/logo.jpeg" alt="Logo">
    </div>
    <div class="nav-right">
        Welcome, <strong><?= htmlspecialchars($userName) ?></strong>
        <a href="login/logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="container">
    <a href="dashboard.php" class="back-link">← Kembali</a>

    <div class="title-wrap">
        <span class="title">Hasil Asal Daerah Penumpang Kereta Api</span>
    </div>

    <div class="table-box">
        <div class="table-header">
            <div class="table-header-title">
                Data Penumpang
                <?php if ($train_name !== ''): ?>
                    - <span style="font-weight:600;color:#1f2937;">
                        <?= htmlspecialchars($train_name) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-scroll">
            <?php if ($jumlahData > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Route</th>
                        <th>Asal Daerah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataHasil as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['nama']) ?></td>
                        <td><?= htmlspecialchars($d['nik']) ?></td>
                        <td><?= htmlspecialchars($d['route']) ?></td>
                        <td><?= htmlspecialchars($d['asal_daerah']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="no-data">Tidak ada data yang sesuai dengan filter yang dipilih.</div>
            <?php endif; ?>
        </div>

        <div class="export-wrap">
            <div style="font-weight:600;color:#374151;">
                Jumlah Penumpang yang turun di stasiun ini :
                <strong><?= $jumlahData ?></strong> Penumpang
            </div>

            <?php if ($jumlahData > 0): ?>
                <a href="export_excel.php" class="btn-export">Unduh Excel</a>
            <?php else: ?>
                <span class="btn-export disabled">Unduh Excel</span>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>