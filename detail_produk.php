<?php
include 'db.php';

// Ambil ID produk
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$produk = mysqli_query($conn, "SELECT * FROM produk WHERE id = $id");
$data = mysqli_fetch_assoc($produk);

if (!$data) {
  echo "<p style='color:white; text-align:center;'>Produk tidak ditemukan.</p>";
  exit;
}

// fungsi ambil harga promo
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
        return [
            "harga"  => $harga_diskon,
            "diskon" => $diskon
        ];
    }
    return [
        "harga"  => $harga_asli,
        "diskon" => 0
    ];
}

$promo = getHargaPromo($conn, $data['id'], $data['harga']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($data['nama']) ?> - ShopNeo</title>
  <link rel="stylesheet" href="pelanggan/produk.css">
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

<!-- Detail Produk -->
<div class="container">
  <div class="detail-card">
    <img src="<?= $data['gambar'] ?>" alt="<?= htmlspecialchars($data['nama']) ?>">
    <div class="detail-info">
      <h2><?= htmlspecialchars($data['nama']) ?></h2>
      
      <?php if ($promo['diskon'] > 0): ?>
        <p>
          <span style="text-decoration:line-through; color:#aaa;">
            Rp <?= number_format($data['harga'], 0, ',', '.') ?>
          </span>
        </p>
        <p style="color:red; font-weight:bold">
          Rp <?= number_format($promo['harga'], 0, ',', '.') ?> 
          (Diskon <?= $promo['diskon'] ?>%)
        </p>
      <?php else: ?>
        <p class="harga">Rp <?= number_format($data['harga'], 0, ',', '.') ?></p>
      <?php endif; ?>

      <p><?= nl2br(htmlspecialchars($data['nama'])) ?></p>

      <?php if(isset($_SESSION['user']) && $_SESSION['user']['role'] === 'pelanggan'): ?>
        <form action="beranda.php?page=keranjang" method="post">
          <input type="hidden" name="produk_id" value="<?= $data['id'] ?>">
          <input type="hidden" name="gambar" value="<?= $data['gambar'] ?>">
          <input type="hidden" name="nama" value="<?= htmlspecialchars($data['nama']) ?>">
          <input type="hidden" name="harga" value="<?= $promo['harga'] ?>">
          <button type="submit" name="add_cart" class="btn">
            <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
          </button>
        </form>
      <?php else: ?>
        <a href="login.php" class="btn"><i class="fas fa-sign-in-alt"></i> Login untuk Beli</a>
      <?php endif; ?>
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
