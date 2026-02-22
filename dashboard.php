<?php
require_once 'config/config.php';
$conn = db_connect();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login/login.php");
    exit;
}

$hasil_pencarian = "";
$status_class = "";

if (isset($_POST['generate'])) {

    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_penumpang']));
    $nik  = mysqli_real_escape_string($conn, trim($_POST['id_passanger']));

    if (empty($nik) || strlen($nik) < 4) {
        $hasil_pencarian = "NIK minimal 4 digit.";
        $status_class = "error";
    } else {

        $kode_awal = substr($nik, 0, 4);
        $query = mysqli_query(
            $conn,
            "SELECT asal_daerah FROM kode_daerah WHERE kode_awal = '$kode_awal'"
        );

        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_assoc($query);

            $hasil_pencarian = "
                <strong>Nama Penumpang:</strong> " . htmlspecialchars($nama ?: '-') . "<br>
                <strong>NIK:</strong> " . htmlspecialchars($nik) . "<br>
                <strong>Asal Daerah:</strong> " . htmlspecialchars($row['asal_daerah']);

            $status_class = "success";
        } else {
            $hasil_pencarian = "Kode wilayah <b>$kode_awal</b> tidak ditemukan.";
            $status_class = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Asal Penumpang KA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .top-right {
            position: absolute;
            top: 20px;
            right: 30px;
            color: #fff;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

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

        .header {
            background: #0052A3;
            color: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 4px solid #DC143C;
        }

        .logo {
            height: 35px;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        .title-box {
            background: #FFC107;
            text-align: center;
            padding: 20px;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        /* TAB */
        .tabs {
            display: flex;
            border-bottom: 3px solid #0052A3;
            margin-bottom: 30px;
        }

        .tab-btn {
            flex: 1;
            padding: 15px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            background: #e0e0e0;
        }

        .tab-btn.active {
            background: #0052A3;
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* FORM */
        .form-container {
            background: white;
            padding: 35px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 25px;
        }

        .input-group:last-child {
            margin-bottom: 0;
        }

        .input-label {
            background: #FFC107;
            padding: 8px 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .input-field {
            padding: 12px;
            border: 1px solid #ccc;
        }

        select.input-field {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23555' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
            cursor: pointer;
        }

        .section-divider {
            border: none;
            border-top: 2px dashed #ddd;
            margin: 10px 0 25px 0;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #0052A3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .generate-button {
            background: #FFC107;
            border: none;
            padding: 12px 50px;
            font-weight: bold;
            cursor: pointer;
        }

        .result-box {
            margin-top: 25px;
            border: 1px solid #ddd;
            padding: 20px;
            background: #fafafa;
        }

        .success {
            color: #2e7d32;
        }

        .error {
            color: #c62828;
        }

        @media (max-width: 768px) {
            .input-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="assets/logo.jpeg" class="logo">
        <div>
            <div class="top-right">
                Welcome, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong>
                <a href="auth/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="title-box">
            Aplikasi Mencari Asal Penumpang Kereta Api
        </div>

        <div class="tabs">
            <button class="tab-btn active" onclick="openTab('manual', this)">Pencarian Manual</button>
            <button class="tab-btn" onclick="openTab('excel', this)">Pencarian Otomatis</button>
            <button class="tab-btn" onclick="openTab('birthday', this)">Pencarian Birthday</button>
        </div>

        <!-- TAB MANUAL -->
        <div id="manual" class="tab-content active">
            <div class="form-container">
                <form method="POST">
                    <div class="input-row">
                        <div class="input-group">
                            <label class="input-label">Nama Penumpang</label>
                            <input type="text" name="nama_penumpang" class="input-field"
                                value="<?= htmlspecialchars($_POST['nama_penumpang'] ?? '') ?>">
                        </div>

                        <div class="input-group">
                            <label class="input-label">NIK Penumpang</label>
                            <input type="text" name="id_passanger" maxlength="16" class="input-field"
                                value="<?= htmlspecialchars($_POST['id_passanger'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="button-container">
                        <button type="submit" name="generate" class="generate-button">Generate</button>
                    </div>

                    <div class="result-box">
                        <strong>Hasil Analisis:</strong><br><br>
                        <span class="<?= $status_class ?>">
                            <?= $hasil_pencarian ?: '<em>Masukkan data untuk memulai.</em>' ?>
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <!-- TAB EXCEL (Pencarian Otomatis) -->
        <div id="excel" class="tab-content">
            <div class="form-container">
                <form action="proses_excel.php" method="POST" enctype="multipart/form-data">
                    <div class="input-group">
                        <label class="input-label">Upload File Excel (.xls)</label>
                        <input type="file" name="file_excel" class="input-field" accept=".xls" required>
                    </div>

                    <hr class="section-divider">
                    <div class="section-title">Filter Stasiun</div>

                    <!-- Filter Stasiun (dipindahkan ke sini) -->
                    <div class="input-row">
                        <div class="input-group">
                            <label class="input-label">Stasiun Keberangkatan</label>
                            <select name="stasiun_asal" class="input-field">
                                <option value="">-- pilih Stasiun Asal --</option>
                                <option value="MN">Madiun (MN)</option>
                                <option value="JG">Jombang (JG)</option>
                                <option value="KTS">Kertosono (KTS)</option>
                                <option value="KD">Kediri (KD)</option>
                                <option value="TA">Tulungagung (TA)</option>
                                <option value="BL">Blitar (BL)</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label class="input-label">Stasiun Tujuan</label>
                            <select name="stasiun_tujuan" class="input-field">
                                <option value="">-- Pilih Stasiun Tujuan --</option>
                                <option value="MN">Madiun (MN)</option>
                                <option value="JG">Jombang (JG)</option>
                                <option value="KTS">Kertosono (KTS)</option>
                                <option value="KD">Kediri (KD)</option>
                                <option value="TA">Tulungagung (TA)</option>
                                <option value="BL">Blitar (BL)</option>
                            </select>
                        </div>
                    </div>

                    <div class="button-container">
                        <button type="submit" class="generate-button">Generate</button>
                    </div>

                    <script>
                        document.querySelector('#excel form').addEventListener('submit', function(e) {
                            const asal = this.stasiun_asal.value;
                            const tujuan = this.stasiun_tujuan.value;

                            if (asal !== '' && tujuan !== '' && asal === tujuan) {
                                alert("Stasiun asal dan tujuan tidak boleh sama.");
                                e.preventDefault();
                            }
                        });
                    </script>
                </form>
            </div>
        </div>

        <!-- TAB BIRTHDAY (Pencarian Birthday) -->
        <div id="birthday" class="tab-content">
            <div class="form-container">
                <form action="proses_excel.php" method="POST" enctype="multipart/form-data">

                    <!-- Upload File -->
                    <div class="input-group">
                        <label class="input-label">Upload File Excel (.xls)</label>
                        <input type="file" name="file_birthday" class="input-field" accept=".xls" required>
                    </div>

                    <!-- (Filter stasiun dipindahkan ke tab Excel) -->

                    <div class="button-container">
                        <button type="submit" class="generate-button">Generate</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function openTab(tabId, el) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

            document.getElementById(tabId).classList.add('active');
            el.classList.add('active');
        }
    </script>

</body>

</html>