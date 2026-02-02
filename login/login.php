<?php
// Hapus session_start() di sini karena sudah ada di config.php (sesuai error di gambar)
include_once '../config/config.php';
$conn = db_connect();

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Mengambil dari name="password"

    // Cek username di tabel 'user'
    $result = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // Verifikasi password hash
        if (password_verify($password, $row['password'])) {
            // Set Session Login
            // Cek apakah session sudah dimulai atau belum untuk menghindari error
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $row['id']; // Ambil ID dari database
            $_SESSION['user'] = $row['username'];

            // Redirect ke dashboard
            header("Location: ../dashboard.php");
            exit;
        }
    }
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RTS 4.0 - Login</title>
    <style>
        /* Style tetap sama seperti sebelumnya */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('https://i.pinimg.com/736x/a8/c8/55/a8c8553ab374d67003f12555734c04db.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
        }

        .login-container {
            position: relative;
            z-index: 1;
            background: rgba(50, 50, 50, 0.95);
            padding: 50px 60px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(10px);
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h1 {
            color: #ffffff;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 2px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #ffffff;
            font-size: 14px;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            background: #ffffff;
            color: #333;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(100, 181, 246, 0.3);
        }

        .login-button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #42a5f5 0%, #2196f3 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        .login-button:hover {
            background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(33, 150, 243, 0.4);
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            color: #ffffff;
            font-size: 14px;
        }

        .register-link a {
            color: #42a5f5;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: #64b5f6;
            text-decoration: underline;
        }

        /* Tambahan style untuk pesan error */
        .error-message {
            background: #ff5252;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1>LOGIN</h1>
        </div>

        <?php if (isset($error)) : ?>
            <div class="error-message">Username atau Password salah!</div>
        <?php endif; ?>

        <form id="loginForm" action="" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" name="login" class="login-button">Login</button>
        </form>

        <div class="register-link">
            Belum punya akun? <a href="register.php">Register</a>
        </div>
    </div>

</body>

</html>