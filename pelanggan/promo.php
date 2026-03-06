<?php
session_start();
include '../db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Promo - ShopNeo</title>
  <link rel="stylesheet" href="produk.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .harga-asli {
      text-decoration: line-through;
      color: #aaa;
      font-size: 14px;
      margin-right: 6px;
    }
    .harga-diskon {
      color: #1db954;
      font-weight: 700;
      font-size: 16px;
    }
    .diskon-badge {
      display: inline-block;
      background: #e63946;
      color: #fff;
      font-size: 13px;
      font-weight: bold;
      padding: 3px 8px;
      border-radius: 4px;
      margin-bottom: 6px;
    }
  </style>
</head>
<body>
<nav class="navbar">
  <div class="nav-container">
    <a href="index.php" class="logo">ShopNeo</a>
    <ul class="menu">
      <li><a href="index.php">Beranda</a></li>
      <li><a href="produk.php">Produk</a></li>
      <li><a href="promo.php" class="active">Promo</a></li>
      <li><a href="kontak.php">Kontak</a></li>
    </ul>
    <div class="nav-actions">
      <div class="search-box">
        <input type="text" placeholder="Cari promo...">
        <button class="btn-search"><i class="fas fa-search"></i></button>
      </div>
      <?php if(isset($_SESSION['user']) && $_SESSION['user']['role'] === 'pelanggan'): ?>
        <a href="beranda.php?page=keranjang" class="btn-login"><i class="fas fa-shopping-cart"></i> Keranjang Saya</a>
        <a href="../logout.php" class="btn-login" style="background:#e63946;"><i class="fas fa-sign-out-alt"></i> Logout</a>
      <?php else: ?>
        <a href="login.php" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container">
  <h2 class="section-title">Promo Spesial</h2>

  <div class="produk-grid">
    <?php
      $today = date("Y-m-d");
      $query = "SELECT produk.*, promo.diskon, promo.tanggal_mulai, promo.tanggal_akhir 
                FROM produk 
                JOIN promo ON produk.id = promo.produk_id
                WHERE promo.tanggal_mulai <= '$today' AND promo.tanggal_akhir >= '$today'";
      $result = mysqli_query($conn, $query);
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $harga_asli = $row['harga'];
          $diskon = $row['diskon'];
          $harga_diskon = $harga_asli - ($harga_asli * $diskon / 100);
    ?>
          <div class="card">
            <span class="diskon-badge">-<?= $diskon ?>%</span>
            <img src="../<?= $row['gambar'] ?>" alt="<?= $row['nama'] ?>">
            <h3><?= $row['nama'] ?></h3>
            <p>
              <span class="harga-asli">Rp <?= number_format($harga_asli, 0, ',' , '.') ?></span>
              <span class="harga-diskon">Rp <?= number_format($harga_diskon, 0, ',' , '.') ?></span>
            </p>
            <a href="detail_produk.php?id=<?= $row['id'] ?>" class="btn"><i class="fas fa-info-circle"></i> Detail</a>
          </div>
    <?php
        }
      } else {
        echo "<p>Tidak ada promo aktif saat ini.</p>";
      }
    ?>
  </div>
</div>

<!-- Footer -->
<footer class="footer">
  <div class="footer-container">
    <div class="footer-section">
      <h3>ShopNeo</h3>
      <p>Toko online terpercaya dengan produk segar dan berkualitas tinggi. Nikmati belanja mudah dan cepat!</p>
    </div>
    <div class="footer-section">
      <h4>Informasi</h4>
      <ul>
        <li><a href="#">Tentang Kami</a></li>
        <li><a href="#">Kebijakan Privasi</a></li>
        <li><a href="#">Syarat & Ketentuan</a></li>
        <li><a href="#">FAQ</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h4>Bantuan</h4>
      <ul>
        <li><a href="#">Hubungi Kami</a></li>
        <li><a href="#">Pengiriman</a></li>
        <li><a href="#">Pembayaran</a></li>
        <li><a href="#">Pengembalian</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h4>Ikuti Kami</h4>
      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-youtube"></i></a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2025 ShopNeo. All Rights Reserved.</p>
  </div>
</footer>

</body>
</html>
