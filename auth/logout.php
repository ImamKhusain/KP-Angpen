<?php
include '../config/config.php'; // session_start() ada di sini

// Hapus semua data session
session_unset();
session_destroy();

// Redirect ke halaman login
header("Location: ../login/login.php");
exit;
