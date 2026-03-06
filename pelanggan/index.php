<?php
session_start();
include '../db.php';

// fungsi promo
function getHargaPromo($conn, $produk_id, $harga_asli) {
    $today = date('Y-m-d');
    $q = mysqli_query($conn, "SELECT diskon FROM promo 
                             WHERE produk_id = $produk_id 
                             AND tanggal_mulai <= '$today' 
                             AND tanggal_akhir >= '$today'
                             LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        $diskon = (int)$row['diskon'];
        $harga_diskon = $harga_asli - ($harga_asli * $diskon / 100);
        return ["harga" => $harga_diskon, "diskon" => $diskon];
    }
    return ["harga" => $harga_asli, "diskon" => 0];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopNeo</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
  <div class="nav-container">
    <a href="index.php" class="logo">ShopNeo</a>
    <ul class="menu">
      <li><a href="index.php" class="active">Beranda</a></li>
      <li><a href="produk.php">Produk</a></li>
      <li><a href="promo.php">Promo</a></li>
      <li><a href="kontak.php">Kontak</a></li>
    </ul>
    <div class="nav-actions">
      <div class="search-box">
        <input type="text" placeholder="Cari produk...">
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

<!-- Slider -->
<div class="slider">
  <div class="slides" id="slides">
    <div class="slide">
      <img src="../asset/img/buah1.jpg" alt="Slide 1">
      <div class="caption">Promo Buah Segar Hari Ini!</div>
    </div>
    <div class="slide">
      <img src="../asset/img/buah2.jpg" alt="Slide 2">
      <div class="caption">Diskon Sayuran Segar Sampai 50%</div>
    </div>
    <div class="slide">
      <img src="../asset/img/buah3.jpg" alt="Slide 3">
      <div class="caption">Madu Alami Untuk Kesehatan</div>
    </div>
  </div>
  <div class="slider-dots" id="dots"></div>
</div>

<!-- Info Grid -->
<div class="info-grid">
  <?php
    $info = mysqli_query($conn,"SELECT * FROM info LIMIT 1");
    $dataInfo = mysqli_fetch_assoc($info);
  ?>
  <div class="info-card">
    <h3><?= $dataInfo['jumlah_produk'] ?>+</h3>
    <p>Produk Tersedia</p>
  </div>
  <div class="info-card">
    <h3><?= $dataInfo['rating'] ?>+</h3>
    <p>Rating Pelanggan</p>
  </div>
  <div class="info-card">
    <h3><?= $dataInfo['jumlah_cabang'] ?>+</h3>
    <p>Cabang Toko</p>
  </div>
</div>

<!-- Produk Grid -->
<div class="container">
  <?php
    $produk = mysqli_query($conn, "SELECT * FROM produk LIMIT 3");
    while ($row = mysqli_fetch_assoc($produk)) { 
      $promo = getHargaPromo($conn, $row['id'], $row['harga']);
  ?>
    <div class="card">
      <img src="../<?= $row['gambar'] ?>" alt="<?= htmlspecialchars($row['nama']) ?>">
      <h3><?= htmlspecialchars($row['nama']) ?></h3>

      <?php if($promo['diskon'] > 0): ?>
        <p>
          <span style="text-decoration:line-through; color:#aaa;">
            Rp <?= number_format($row['harga'], 0, ',', '.') ?>
          </span><br>
          <span style="color:red; font-weight:bold;">
            Rp <?= number_format($promo['harga'], 0, ',', '.') ?>
          </span>
        </p>
      <?php else: ?>
        <p>Rp <?= number_format($row['harga'], 0, ',' , '.') ?></p>
      <?php endif; ?>

      <?php if(isset($_SESSION['user']) && $_SESSION['user']['role'] === 'pelanggan'): ?>
        <form action="beranda.php?page=keranjang" method="post">
          <input type="hidden" name="produk_id" value="<?= $row['id'] ?>">
          <input type="hidden" name="gambar" value="<?= $row['gambar'] ?>">
          <input type="hidden" name="nama" value="<?= htmlspecialchars($row['nama']) ?>">
          <input type="hidden" name="harga" value="<?= $promo['harga'] ?>">
          <button type="submit" name="add_cart" class="btn"><i class="fas fa-cart-plus"></i> Beli Sekarang</button>
        </form>
      <?php else: ?>
        <a href="login.php" class="btn"><i class="fas fa-sign-in-alt"></i> Login untuk Beli</a>
      <?php endif; ?>
    </div>
  <?php } ?>
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

<script src="index.js"></script>
</body>
</html>
