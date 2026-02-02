<?php
// 1. Panggil Konfigurasi Database
include '../config/config.php';
$conn = db_connect(); // Pastikan fungsi ini ada di config.php

// 2. Proses Registrasi saat tombol ditekan
if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Cek apakah username sudah ada di database?
    $cek_user = mysqli_query($conn, "SELECT username FROM user WHERE username = '$username'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        // Jika username kembar, simpan pesan error
        $backend_error = "Username sudah terpakai! Silakan pilih yang lain.";
    } else {
        // Jika aman, Hash Password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Masukkan ke Database
        $query = "INSERT INTO user (username, password) VALUES ('$username', '$password_hash')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>
                    alert('Registrasi Berhasil! Silakan Login.');
                    window.location = 'login.php';
                  </script>";
            exit;
        } else {
            $backend_error = "Terjadi kesalahan sistem: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RTS 4.0 - Daftar Akun</title>
    <style>
        /* CSS ASLI KAMU (TIDAK DIUBAH) */
        * { margin: 0; padding: 0; box-sizing: border-box; }
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
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.3);
        }
        .register-container {
            position: relative; z-index: 1;
            background: rgba(50, 50, 50, 0.95);
            padding: 50px 60px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            width: 100%; max-width: 450px;
            backdrop-filter: blur(10px);
        }
        .register-header { text-align: center; margin-bottom: 40px; }
        .register-header h1 { color: #ffffff; font-size: 28px; font-weight: 600; line-height: 1.3; }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; color: #ffffff; font-size: 14px; margin-bottom: 8px; font-weight: 500; }
        .form-group input {
            width: 100%; padding: 15px 20px; border: none; border-radius: 8px;
            font-size: 15px; background: #ffffff; color: #333; transition: all 0.3s ease;
        }
        .form-group input:focus { outline: none; box-shadow: 0 0 0 3px rgba(100, 181, 246, 0.3); }
        .form-group input::placeholder { color: #999; }
        .password-strength { margin-top: 8px; height: 4px; background: #555; border-radius: 2px; overflow: hidden; }
        .password-strength-bar { height: 100%; width: 0%; transition: all 0.3s ease; }
        .password-strength-bar.weak { width: 33%; background: #f44336; }
        .password-strength-bar.medium { width: 66%; background: #ff9800; }
        .password-strength-bar.strong { width: 100%; background: #4caf50; }
        .register-button {
            width: 100%; padding: 15px;
            background: linear-gradient(135deg, #42a5f5 0%, #2196f3 100%);
            color: white; border: none; border-radius: 8px; font-size: 16px;
            font-weight: 600; cursor: pointer; transition: all 0.3s ease;
            text-transform: uppercase; letter-spacing: 1px; margin-top: 10px;
        }
        .register-button:hover {
            background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
            transform: translateY(-2px); box-shadow: 0 5px 20px rgba(33, 150, 243, 0.4);
        }
        .login-link { text-align: center; margin-top: 25px; color: #ffffff; font-size: 14px; }
        .login-link a { color: #42a5f5; text-decoration: none; font-weight: 600; transition: color 0.3s ease; }
        .login-link a:hover { color: #64b5f6; text-decoration: underline; }
        .error-message { color: #f44336; font-size: 12px; margin-top: 5px; display: none; }
        .error-message.show { display: block; }
        
        /* Tambahan style untuk alert backend */
        .backend-alert {
            background: #f44336; color: white; padding: 10px; border-radius: 5px;
            margin-bottom: 20px; text-align: center; font-size: 14px;
        }

        @media (max-width: 480px) {
            .register-container { padding: 40px 30px; margin: 20px; }
            .register-header h1 { font-size: 24px; }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Silahkan Membuat<br>Akun Baru</h1>
        </div>
        
        <?php if(isset($backend_error)): ?>
            <div class="backend-alert">
                <?= $backend_error; ?>
            </div>
        <?php endif; ?>

        <form id="registerForm" action="" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Username" required>
                <div class="error-message" id="usernameError"></div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password (Min 8 char, 1 angka, 1 simbol)" required>
                <div class="password-strength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                </div>
                <div class="error-message" id="passwordError"></div>
            </div>
            
            <button type="submit" name="register" class="register-button" id="registerBtn">Register</button>
        </form>
        
        <div class="login-link">
            Sudah punya akun? <a href="login.php">Login</a>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        const usernameInput = document.getElementById('username');
        const registerForm = document.getElementById('registerForm');
        const passwordError = document.getElementById('passwordError');
        const usernameError = document.getElementById('usernameError');

        // Password strength checker
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            
            strengthBar.className = 'password-strength-bar';
            
            if (password.length === 0) {
                strengthBar.className = 'password-strength-bar';
            } else if (strength < 3) {
                strengthBar.classList.add('weak');
            } else if (strength < 5) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
            }
            
            validatePassword();
        });

        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            return strength;
        }

        function validatePassword() {
            const password = passwordInput.value;
            const hasMinLength = password.length >= 8;
            const hasNumber = /[0-9]/.test(password);
            const hasSymbol = /[^a-zA-Z0-9]/.test(password);
            
            if (password.length === 0) {
                passwordError.classList.remove('show');
                return false;
            }
            
            // Note: Saya sesuaikan validasi JS agar sinkron dengan kebutuhan form
            if (!hasMinLength || !hasNumber || !hasSymbol) {
                let errors = [];
                if (!hasMinLength) errors.push('min 8 char');
                if (!hasNumber) errors.push('1 angka');
                if (!hasSymbol) errors.push('1 simbol');
                
                passwordError.textContent = 'Syarat: ' + errors.join(', ');
                passwordError.classList.add('show');
                return false;
            } else {
                passwordError.classList.remove('show');
                return true;
            }
        }

        function validateUsername() {
            const username = usernameInput.value;
            if (username.length === 0) {
                usernameError.classList.remove('show');
                return false;
            }
            if (username.length < 3) {
                usernameError.textContent = 'Username minimal 3 karakter';
                usernameError.classList.add('show');
                return false;
            } else {
                usernameError.classList.remove('show');
                return true;
            }
        }

        usernameInput.addEventListener('input', validateUsername);

        // MODIFIKASI PENTING DI SINI:
        registerForm.addEventListener('submit', function(e) {
            
            const isUsernameValid = validateUsername();
            const isPasswordValid = validatePassword();
            
            // Jika ada yang tidak valid, batalkan submit (cegah kirim ke PHP)
            if (!isUsernameValid || !isPasswordValid) {
                e.preventDefault(); 
            }
            
            // Jika valid, biarkan form terkirim secara normal ke server PHP
            // (e.preventDefault() dihapus untuk kondisi sukses)
        });
    </script>
</body>
</html>