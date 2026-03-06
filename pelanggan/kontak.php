<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kontak - ShopNeo</title>
  <link rel="stylesheet" href="produk.css"> <!-- Gunakan CSS yang sudah kamu buat -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
  <div class="nav-container">
    <a href="index.php" class="logo">ShopNeo</a>
    <ul class="menu">
      <li><a href="index.php">Beranda</a></li>
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

<!-- Kontak Section -->
<div class="container">
  <h2 class="section-title">Hubungi Kami</h2>

  <div class="kontak-grid">
    <div class="kontak-info">
      <h3><i class="fas fa-map-marker-alt"></i> Alamat</h3>
      <p>Jl. Belanja No. 123, Padang Timur, Sumatera Barat</p>

      <h3><i class="fas fa-phone"></i> Telepon</h3>
      <p>+62 812-3456-7890</p>

      <h3><i class="fas fa-envelope"></i> Email</h3>
      <p>support@shopneo.id</p>

      <h3><i class="fas fa-clock"></i> Jam Operasional</h3>
      <p>Senin - Jumat: 08.00 - 17.00 WIB</p>
    </div>

    <div class="kontak-form">
      <form action="#" method="post">
        <input type="text" name="nama" placeholder="Nama Anda" required>
        <input type="email" name="email" placeholder="Email Anda" required>
        <textarea name="pesan" rows="5" placeholder="Pesan Anda..." required></textarea>
        <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Kirim Pesan</button>
      </form>
    </div>
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
