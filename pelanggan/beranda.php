<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!=='pelanggan'){
    header("Location: ../login.php");
    exit;
}

$page = $_GET['page'] ?? 'keranjang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Beranda Pelanggan</title>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
  <?php
  switch($page){
      case 'keranjang':
          include 'keranjang.php';
          break;
      case 'checkout':
          include 'checkout.php';
          break;
      case 'riwayat':
          include 'riwayat.php';
          break;
      case 'detail':
          include 'detail.php';
          break;
      default:
          echo "<p>Halaman tidak ditemukan.</p>";
  }
  ?>
</div>
</body>
</html>
