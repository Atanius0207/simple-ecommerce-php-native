<?php
session_start();
include 'db.php'; // koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Ambil user berdasarkan username
    $sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $role = $user['role'];
        $validPassword = false;

        // Admin: password tanpa hash, role lain pakai hash
        if ($role == 'admin') {
            $validPassword = ($password === $user['password']);
        } else {
            $validPassword = password_verify($password, $user['password']);
        }

        if ($validPassword) {
            // Simpan session user
            $_SESSION['user'] = [
                'id'       => $user['id'],
                'username' => $user['username'],
                'role'     => $role,
                'outlet_id' => $user['outlet_id'] ?? null
            ];

            // Redirect sesuai role
            $redirect = [
                'owner'     => 'owner/dashboard.php',
                'admin'     => 'admin/beranda.php?page=home',
                'kasir'     => 'kasir/beranda.php?page=dashboard',
                'pelanggan' => 'pelanggan/index.php'
            ];

            if (isset($redirect[$role])) {
                header("Location: " . $redirect[$role]);
            } else {
                $_SESSION['error'] = "Role tidak dikenali!";
                header("Location: login.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Password salah!";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Username tidak ditemukan!";
        header("Location: login.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Akses tidak sah!";
    header("Location: login.php");
    exit;
}
