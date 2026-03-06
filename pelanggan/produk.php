<?php
session_start();
include '../db.php';

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Produk - ShopNeo</title>
  <link rel="stylesheet" href="produk.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<nav class="navbar">
  <div class="nav-container">
    <a href="index.php" class="logo">ShopNeo</a>
    <ul class="menu">
      <li><a href="index.php">Beranda</a></li>
      <li><a href="produk.php" class="active">Produk</a></li>
      <li><a href="promo.php">Promo</a></li>
      <li><a href="kontak.php">Kontak</a></li>
    </ul>
    <div class="nav-actions">
      <div class="search-box">
        <input type="text" name="q" form="formCari" placeholder="Cari produk..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
        <button class="btn-search" form="formCari"><i class="fas fa-search"></i></button>
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
  <h2 class="section-title">Semua Produk</h2>

  <div class="search-filter">
    <form method="GET" action="produk.php" id="formCari">
      <input type="text" name="q" placeholder="Cari produk..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
      <button type="submit"><i class="fas fa-search"></i> Cari</button>
    </form>
  </div>

  <div class="produk-grid">
    <?php
      $query = "SELECT * FROM produk";
      if (isset($_GET['q']) && $_GET['q'] !== '') {
        $search = mysqli_real_escape_string($conn, $_GET['q']);
        $query .= " WHERE nama LIKE '%$search%'";
      }

      $result = mysqli_query($conn, $query);
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) { 
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
            <a href="detail_produk.php?id=<?= $row['id'] ?>" class="btn"><i class="fas fa-info-circle"></i> Detail</a>
          </div>
        <?php }
      } else {
        echo "<p>Tidak ada produk ditemukan.</p>";
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
