<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login/login.php");
    exit;
}

// ambil data dari session
$dataHasil = $_SESSION['export_data'] ?? [];
$train_name = $_SESSION['export_nama_ka'] ?? '';
$userName = $_SESSION['username'] ?? 'Admin';
$jumlahData = count($dataHasil);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Hasil Birthday Penumpang</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
    :root { --blue:#0052A3; --green:#16a34a; --yellow:#FFC107; --shadow:0 6px 18px rgba(0,0,0,.08); }
    body { font-family: 'Inter', sans-serif; background:#f4f6f8; margin:0; padding:0; }

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
        background:#DC143C;
    }

    .logo-wrap{
        background:#fff;
        padding:6px 10px;
        border-radius:4px;
        display:flex;
        align-items:center;
    }
    .logo-wrap img{height:38px;}

    .nav-right{
        display:flex;
        align-items:center;
        gap:14px;
        font-size:14px;
    }

    /* === REVISI WARNA LOGOUT BUTTON === */
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

    .container{max-width:1200px;margin:24px auto;padding:0 20px;}
    .back-link{color:var(--blue);display:inline-block;margin-bottom:12px;text-decoration:none;font-weight:600;}
    .title{background:var(--yellow);display:inline-block;padding:10px 28px;border-radius:4px;box-shadow:var(--shadow);color:#000;font-weight:600;}

    .table-box{background:#fff;border-radius:8px;box-shadow:var(--shadow);overflow:hidden;margin-top:12px;}
    .table-header{padding:14px 18px;border-bottom:1px solid #eee;}
    .table-header-title{font-weight:700;color:#374151;font-size:15px;}
    .table-scroll{max-height:460px;overflow:auto;}

    table{width:100%;border-collapse:collapse;}
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
    }

    .no-data{padding:30px;text-align:center;color:#9ca3af;}
    </style>
</head>
<body>

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
    <div style="text-align:center;margin-bottom:14px;">
        <span class="title">Penumpang Kereta Api yang Berulang Tahun Hari Ini</span>
    </div>

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

    <div class="table-box">
        <div class="table-header">
            <div class="table-header-title">Hasil Birthday</div>
        </div>

        <div class="table-scroll">
            <?php if ($jumlahData > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Trip Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataHasil as $i => $d): ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td><?= htmlspecialchars($d['nama']) ?></td>
                        <td><?= htmlspecialchars($d['nik']) ?></td>
                        <td><?= htmlspecialchars($d['trip']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="no-data">🎂 Tidak ada penumpang yang berulang tahun hari ini.</div>
            <?php endif; ?>
        </div>

        <div class="export-wrap">
            <div style="font-weight:600;color:#374151;">
                Total : <strong><?= $jumlahData ?></strong> orang
            </div>
            <?php if ($jumlahData > 0): ?>
                <a href="export_excel.php" class="btn-export" title="Unduh hasil ulang tahun ke Excel">Unduh Excel</a>
            <?php else: ?>
                <span class="btn-export" style="opacity:.6;pointer-events:none;">Unduh Excel</span>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>