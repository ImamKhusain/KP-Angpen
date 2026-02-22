<?php
// index.php — gerbang utama
session_start();

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: dashboard.php");
    exit;
}

// kalau belum login, arahkan ke folder login
header("Location: login/login.php");
exit;