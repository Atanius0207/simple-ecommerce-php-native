<?php
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

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pelanggan'){
    header("Location: ../login.php");
    exit;
}

// Tambah ke keranjang
if(isset($_POST['add_cart'])){
    $id     = (string)($_POST['produk_id'] ?? '');
    $nama   = (string)($_POST['nama'] ?? '');
    $harga  = floatval($_POST['harga'] ?? 0);
    $gambar = (string)($_POST['gambar'] ?? '');

    if ($id !== '') {
        if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if(isset($_SESSION['cart'][$id])){
            $_SESSION['cart'][$id]['qty'] += 1;
        } else {
            $_SESSION['cart'][$id] = [
                'nama'   => $nama,
                'harga'  => $harga,
                'gambar' => $gambar,
                'qty'    => 1
            ];
        }
    }
}

// Hapus item tertentu
if(isset($_GET['hapus'])){
    $hid = (string)$_GET['hapus'];
    if(isset($_SESSION['cart'][$hid])) {
        unset($_SESSION['cart'][$hid]);
    }
    header("Location: beranda.php?page=keranjang"); 
    exit;
}

// Kosongkan semua
if(isset($_GET['clear'])){
    unset($_SESSION['cart']);
    header("Location: beranda.php?page=keranjang");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Keranjang Saya</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body { font-family: 'Segoe UI', sans-serif; background:#121212; color:#fff; margin:0; }
.container { max-width:1200px; margin:40px auto; display:flex; gap:20px; padding:0 16px; }
.cart-box, .summary-box { background:#333; border-radius:8px; padding:20px; box-shadow:0 4px 10px rgba(0,0,0,.2); }
.cart-box { flex:3; }
.summary-box { flex:1; height:fit-content; position:sticky; top:20px; }
h2 { margin:0 0 20px; color:#fff; display:flex; align-items:center; gap:10px; }
.table-cart { width:100%; border-collapse:collapse; }
.table-cart th, .table-cart td { padding:14px; border-bottom:1px solid #555; text-align:left; }
.table-cart th { background:#222; font-weight:600; font-size:14px; }
.table-cart img { width:60px; height:60px; object-fit:cover; border-radius:6px; margin-right:8px; }
.img-wrap { display:flex; align-items:center; }
.img-placeholder { width:60px; height:60px; background:#2a2a2a; display:flex; align-items:center; justify-content:center; border-radius:6px; color:#888; margin-right:8px; }
.btn { display:inline-block; padding:10px 16px; border-radius:6px; text-decoration:none; font-weight:500; transition:.2s; }
.btn-danger { background:#e63946; color:#fff; }
.btn-danger:hover { background:#d62839; }
.btn-success { background:#1db954; color:#fff; }
.btn-success:hover { background:#17a444; }
.btn-hapus { color:#e63946; font-size:14px; text-decoration:none; }
.btn-hapus:hover { text-decoration:underline; }
.summary-box h3 { margin-bottom:15px; font-size:18px; border-bottom:1px solid #444; padding-bottom:10px; }
.summary-item { display:flex; justify-content:space-between; margin:8px 0; }
.summary-total { font-weight:700; border-top:1px solid #444; padding-top:12px; }
</style>
</head>
<body>
<div class="container">
  <div class="cart-box">
    <h2><i class="fas fa-shopping-cart"></i> Keranjang Saya</h2>

    <?php if(empty($cart)): ?>
      <p><i class="fas fa-info-circle"></i> Belum ada item di keranjang.</p>
    <?php else: ?>
      <table class="table-cart">
        <tr>
          <th>Produk</th>
          <th>Harga</th>
          <th>Qty</th>
          <th>Subtotal</th>
          <th>Aksi</th>
        </tr>
        <?php 
        $total_normal = 0; // total tanpa diskon
        $total = 0;        // total setelah diskon
        foreach($cart as $id=>$item):
          $nama = htmlspecialchars($item['nama']);
          $qty  = $item['qty'];

          // Ambil harga asli dari DB
          $p = mysqli_query($conn, "SELECT harga FROM produk WHERE id= $id");
          $row = mysqli_fetch_assoc($p);
          $harga_asli = $row['harga'];

          // Cek promo
          $promo = getHargaPromo($conn, $id, $harga_asli);
          $harga = $promo['harga'];

          $subtotal_normal = $harga_asli * $qty;
          $subtotal = $harga * $qty;

          $total_normal += $subtotal_normal;
          $total += $subtotal;

          $gambar = $item['gambar'] ? "../asset/img/".basename($item['gambar']) : '';
        ?>
        <tr>
          <td>
            <div class="img-wrap">
              <?php if($gambar && file_exists(__DIR__."/../asset/img/".basename($item['gambar']))): ?>
                <img src="<?= $gambar ?>" alt="<?= $nama ?>">
              <?php else: ?>
                <div class="img-placeholder"><i class="fas fa-image"></i></div>
              <?php endif; ?>
              <?= $nama ?>
            </div>
          </td>
          <td>
            <?php if($promo['diskon'] > 0): ?>
              <span style="text-decoration:line-through; color:#aaa;">
                Rp <?= number_format($harga_asli,0,',','.') ?>
              </span><br>
              <span style="color:red; font-weight:bold;">
                Rp <?= number_format($harga,0,',','.') ?>
              </span>
            <?php else: ?>
              Rp <?= number_format($harga,0,',','.') ?>
            <?php endif; ?>
          </td>
          <td><?= $qty ?></td>
          <td>Rp <?= number_format($subtotal,0,',','.') ?></td>
          <td><a class="btn-hapus" href="beranda.php?page=keranjang&hapus=<?= urlencode($id) ?>"><i class="fas fa-trash"></i> Hapus</a></td>
        </tr>
        <?php endforeach; ?>
      </table>

      <br>
      <a href="beranda.php?page=keranjang&clear=1" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Kosongkan Keranjang</a>
    <?php endif; ?>
  </div>

  <?php if(!empty($cart)): 
    $total_diskon = $total_normal - $total;
  ?>
  <div class="summary-box">
    <h3><i class="fas fa-receipt"></i> Ringkasan Belanja</h3>
    <div class="summary-item"><span>Total Produk</span><span><?= count($cart) ?></span></div>
    <div class="summary-item"><span>Total Normal</span><span>Rp <?= number_format($total_normal,0,',','.') ?></span></div>
    <div class="summary-item"><span>Total Diskon</span><span style="color:#1db954;">- Rp <?= number_format($total_diskon,0,',','.') ?></span></div>
    <div class="summary-total"><span>Total Bayar: </span><span>Rp <?= number_format($total,0,',','.') ?></span></div>
    <br>
    <a href="beranda.php?page=checkout" class="btn btn-success" style="display:block; text-align:center;">
      <i class="fas fa-credit-card"></i> Checkout
    </a>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
