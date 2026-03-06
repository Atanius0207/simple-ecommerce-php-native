<?php
session_start();
include '../db.php';

// Cek apakah user login & role kasir
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    header("Location: ../login.php");
    exit;
}

$page = $_GET['page'] ?? 'dashboard_kasir';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kasir Panel</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="sidebar.css">
</head>
<body>
<?php include 'sidebar.php'?>
<div class="main-content">
  <?php
  switch ($page) {
      case 'dashboard':
          include 'dashboard.php';
          break;
      case 'penjualan':
          include 'penjualan.php';
          break;
      case 'riwayat':
          include 'riwayat.php'; // buat file riwayat transaksi kasir
          break;
      case 'detail':
          include 'detail.php';
          break;
      case 'transaksi':
          include 'transaksi.php';
          break;
      case 'detail-online':
          include 'detail-online.php';
          break;
      default:
          echo "<h2>Halaman tidak ditemukan!</h2>";
          break;
  }
  ?>
</div>

</body>
</html>
