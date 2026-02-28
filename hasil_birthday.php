<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login/login.php");
    exit;
}

// ambil data dari session
$dataHasil = $_SESSION['export_data'] ?? [];
$train_name = $_SESSION['export_nama_ka'] ?? '';
$userName = $_SESSION['user'] ?? 'Admin';
$jumlahData = count($dataHasil);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Hasil Birthday Penumpang</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: #f5f5f5; color: #374151; }

    /* HEADER */
    .header {
        background: #0052A3;
        color: #fff;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 4px solid #DC143C;
    }
    .logo-wrap { background: #fff; padding: 6px 10px; border-radius: 4px; display: flex; align-items: center; }
    .logo-wrap img { height: 35px; }
    .nav-right { display:flex; align-items:center; gap:14px; font-size:14px; }
    .logout-btn { padding:6px 12px; background:#FFC107; color:#fff; border-radius:5px; text-decoration:none; font-size:13px; font-weight:600; }

    /* CONTAINER */
    .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
    .back-link { color: #0052A3; display:inline-flex; align-items:center; gap:6px; margin-bottom:20px; text-decoration:none; font-weight:600; font-size:14px; }

    /* TITLE */
    .title-box {
        background: #FFC107;
        text-align: center;
        padding: 18px;
        font-size: 24px;        /* sedikit diperbesar */
        font-weight: 700;
        color: #000;            /* hitam pekat */
        margin-bottom: 20px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(255,193,7,0.3);
        text-shadow: 0 1px 0 rgba(0,0,0,0.15); /* membuat teks lebih tajam */
    }

    /* CONTENT CARD (satu kotak besar putih yang berisi no-data atau list) */
    .content-card {
        background: #fff;
        border-radius: 10px;
        padding: 28px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    }

    /* area utama card */
    .card-body {
        min-height: 160px; /* jaga tinggi minimal agar pesan no-data terlihat seimbang */
        display: flex;
        align-items: center;
        justify-content: center;
    }

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

    .no-data {
        text-align: center;
        color: #9ca3af;
        font-size: 16px;
    }
    .no-data-icon { font-size: 48px; margin-bottom: 12px; }

    /* cards container (kartu penumpang ketika ada data) */
    .cards-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        width: 100%;
    }
    .passenger-card {
        background: linear-gradient(135deg, #ff9800 0%, #ff6d00 100%);
        border-radius: 12px;
        padding: 16px 18px;
        color: #fff;
        box-shadow: 0 6px 16px rgba(255,109,0,0.25);
    }
    .card-row { display:flex; align-items:baseline; margin-bottom:8px; font-size:14px; }
    .card-label { font-weight:700; min-width:90px; text-transform:uppercase; font-size:13px; }
    .card-value { font-weight:400; font-size:14px; }

    /* footer di dalam content-card: jumlah + eksport (di satu bar) */
    .card-footer {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        border-top: 1px solid #f3f4f6;
        padding-top: 16px;
    }
    .total-info { font-size:15px; font-weight:600; color:#374151; }
    .total-info strong { color:#0052A3; font-size:17px; font-weight:700; }

    .btn-export { padding:10px 20px; background:#16a34a; color:#fff; border-radius:6px; text-decoration:none; font-weight:700; font-size:14px; }
    .btn-export.disabled { opacity:0.5; pointer-events:none; cursor:not-allowed; }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .cards-container { grid-template-columns: 1fr; }
        .card-body { padding: 8px 0; }
        .card-footer { flex-direction: column; align-items: stretch; text-align: center; gap:10px; }
        .btn-export { align-self:center; }
    }
    </style>
</head>
<body>

<div class="header">
    <div class="logo-wrap">
        <img src="assets/logo.jpeg" alt="Logo">
    </div>
    <div class="nav-right">
        Welcome, <strong><?= htmlspecialchars($userName) ?></strong>
        <a href="auth/logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="container">
    <a href="index.php" class="back-link">← Kembali</a>

    <div class="title-box">Hasil Birthday Penumpang KA</div>

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

    <!-- SATU KARTU BESAR: berisi pesan/list + jumlah + export -->
    <div class="content-card">
        <div class="card-body">
            <?php if ($jumlahData > 0): ?>
                <div class="cards-container">
                    <?php foreach ($dataHasil as $d): ?>
                    <div class="passenger-card">
                        <div class="card-row">
                            <span class="card-label">NAME :</span>
                            <span class="card-value"><?= htmlspecialchars($d['nama']) ?></span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">NIK :</span>
                            <span class="card-value"><?= htmlspecialchars($d['nik']) ?></span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">SEAT :</span>
                            <span class="card-value"><?= htmlspecialchars($d['seat'] ?? '-') ?></span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">JENIS KA :</span>
                            <span class="card-value"><?= htmlspecialchars($d['jenis_ka'] ?? $train_name) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <div class="no-data-icon">🎂</div>
                    <div>Tidak ada penumpang yang berulang tahun hari ini.</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- footer tetap berada di dalam content-card sehingga jadi satu kesatuan -->
        <div class="card-footer">
            <div class="total-info">
                Jumlah Penumpang yang Berulang Tahun Hari Ini: <strong><?= $jumlahData ?></strong> Person
            </div>

            <?php if ($jumlahData > 0): ?>
                <a href="export_excel.php" class="btn-export">Export Excel</a>
            <?php else: ?>
                <span class="btn-export disabled">Export Excel</span>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>