<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$page = $_GET['page'] ?? 'home'; // default ke home

include 'sidebar.php'; // Sidebar tetap di semua halaman
?>
<link rel="stylesheet" href="sidebar2.css">
<div class="main-content">
  <?php
  switch ($page) {
      case 'home':
          include 'dashboard.php';
          break;
      case 'penjualan':
          include 'penjualan.php';
          break;
      case 'produk':
          include 'produk.php';
          break;
      case 'produk_edit':
          include 'produk_edit.php';
          break;
      case 'produk_hapus':
          include 'produk_hapus.php';
          break;
      case 'pembelian':
          include 'pembelian.php';
          break;
      case 'laporan':
          include 'laporan.php';
          break;
      case 'outlet':
          include 'outlet.php';
          break;
      case 'outlet_edit':
          include 'outlet_edit.php';
          break;
      case 'outlet_hapus':
          include 'outlet_hapus.php';
          break;
      case 'stok':
          include 'stok.php';
          break;
      case 'akuntansi':
          include 'akuntansi.php';
          break;
      case 'kelola_kasir':
          include 'kelola_kasir.php';
          break;
      case 'tambah-kasir':
          include 'tambah-kasir.php';
          break;
      case 'hapus-kasir':
          include 'hapus-kasir.php';
          break;
      case 'laporanpdf':
          include 'laporan_pdf.php';
          break;
      case 'piutang':
          include 'piutang.php';
          break;
      case 'promo':
          include 'promo.php';
          break;
      default:
          echo "<h2>Halaman tidak ditemukan!</h2>";
          break;
  }
  ?>
</div>
