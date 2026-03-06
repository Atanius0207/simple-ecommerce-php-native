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
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if(empty($cart)){
    echo "<script>alert('Keranjang masih kosong!'); window.location='index.php';</script>";
    exit;
}

$errors = '';
if(isset($_POST['checkout'])){
    $nama    = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat  = mysqli_real_escape_string($conn, $_POST['alamat']);
    $metode  = mysqli_real_escape_string($conn, $_POST['metode']);
    $user_id = $_SESSION['user']['id'];

    if(empty($nama) || empty($alamat)){
        $errors = "Harap lengkapi data pengiriman.";
    } else {
        $total = 0;
        foreach($cart as $pid=>$c){
            // ambil harga asli dari DB
            $p = mysqli_query($conn, "SELECT harga FROM produk WHERE id=$pid");
            $row = mysqli_fetch_assoc($p);
            $harga_asli = $row['harga'];

            // cek promo
            $promo = getHargaPromo($conn, $pid, $harga_asli);
            $harga = $promo['harga'];

            $total += $harga * $c['qty'];
        }

        // Insert ke tabel transaksi_pelanggan
        $sql = "INSERT INTO transaksi_pelanggan (user_id, nama_penerima, alamat, metode_bayar, total, tanggal, status) 
                VALUES ($user_id, '$nama', '$alamat', '$metode', $total, NOW(), 'pending')";
        if(mysqli_query($conn, $sql)){
            $trans_id = mysqli_insert_id($conn);

            // Simpan detail
            foreach($cart as $pid=>$c){
                $nama_produk = mysqli_real_escape_string($conn, $c['nama']);

                $p = mysqli_query($conn, "SELECT harga FROM produk WHERE id=$pid");
                $row = mysqli_fetch_assoc($p);
                $harga_asli = $row['harga'];
                $promo = getHargaPromo($conn, $pid, $harga_asli);
                $harga = $promo['harga'];

                $qty   = $c['qty'];
                $subtotal = $harga * $qty;

                mysqli_query($conn, "INSERT INTO transaksi_pelanggan_detail (transaksi_id, produk_id, nama_produk, qty, harga, subtotal)
                                     VALUES ($trans_id, $pid, '$nama_produk', $qty, $harga, $subtotal)");
            }

            unset($_SESSION['cart']); // kosongkan keranjang
            echo "<script>alert('Pesanan berhasil!'); window.location='beranda.php?page=riwayat';</script>";
            exit;
        } else {
            $errors = "Gagal menyimpan pesanan: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Checkout</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body { font-family:'Segoe UI',sans-serif; background:#121212; margin:0; padding:0; }
.container { max-width:1000px; margin:40px auto; display:flex; gap:24px; }
.form-box, .summary-box { background:#333; border-radius:10px; padding:20px; box-shadow:0 4px 15px rgba(0,0,0,0.08); }
.form-box { flex:2; }
.summary-box { flex:1; height:fit-content; position:sticky; top:20px; }
h2 { margin:0 0 20px; color:#1db954; }
label { font-weight:600; display:block; margin-top:10px; }
input[type=text], textarea, select {
  width:98%; padding:10px; border:1px solid #333; border-radius:6px; margin-top:6px; background: #191414; color: #eee
}
textarea { resize:vertical; min-height:80px; background: #191414; color: #eee }
.btn { display:inline-block; background:#1db954; color:#fff; padding:12px 20px; border:none; border-radius:6px; font-weight:600; cursor:pointer; transition:.3s; margin-top:15px; }
.btn:hover { background:#17a444; }
.error { color:#e63946; margin-bottom:15px; font-weight:600; }
.summary-box h3 { margin-bottom:15px; font-size:18px; border-bottom:1px solid #eee; padding-bottom:10px; color:#1db954; }
.summary-item { display:flex; justify-content:space-between; margin:8px 0; }
.summary-total { font-weight:700; font-size:16px; border-top:1px solid #eee; padding-top:12px; margin-top:12px; }
</style>
</head>
<body>
<div class="container">
  <div class="form-box">
    <h2><i class="fas fa-credit-card"></i> Checkout</h2>
    <?php if($errors): ?><div class="error"><?= $errors ?></div><?php endif; ?>
    <form method="post">
      <label>Nama Penerima</label>
      <input type="text" name="nama" value="<?= $_SESSION['user']['nama'] ?? '' ?>" required>

      <label>Alamat Pengiriman</label>
      <textarea name="alamat" required></textarea>

      <label>Metode Pembayaran</label>
      <select name="metode" required>
        <option value="COD">Bayar di Tempat (COD)</option>
        <option value="Transfer">Transfer Bank</option>
        <option value="E-Wallet">E-Wallet</option>
      </select>

      <button type="submit" name="checkout" class="btn"><i class="fas fa-paper-plane"></i> Buat Pesanan</button>
    </form>
  </div>

  <div class="summary-box">
    <h3>Ringkasan Belanja</h3>
    <?php 
    $total_normal = 0;
    $total = 0;
    foreach($cart as $pid=>$c): 
      $p = mysqli_query($conn, "SELECT harga FROM produk WHERE id=$pid");
      $row = mysqli_fetch_assoc($p);
      $harga_asli = $row['harga'];

      $promo = getHargaPromo($conn, $pid, $harga_asli);
      $harga = $promo['harga'];

      $subtotal_normal = $harga_asli * $c['qty'];
      $subtotal = $harga * $c['qty'];

      $total_normal += $subtotal_normal;
      $total += $subtotal;
    ?>
      <div class="summary-item">
        <span><?= htmlspecialchars($c['nama']) ?> x <?= $c['qty'] ?></span>
        <span>
          <?php if($promo['diskon'] > 0): ?>
            <span style="text-decoration:line-through; color:#aaa;">Rp <?= number_format($subtotal_normal,0,',','.') ?></span><br>
            <span style="color:red;">Rp <?= number_format($subtotal,0,',','.') ?></span>
          <?php else: ?>
            Rp <?= number_format($subtotal,0,',','.') ?>
          <?php endif; ?>
        </span>
      </div>
    <?php endforeach; 
      $total_diskon = $total_normal - $total;
    ?>
    <div class="summary-item"><span>Total Normal</span><span>Rp <?= number_format($total_normal,0,',','.') ?></span></div>
    <div class="summary-item"><span>Total Diskon</span><span style="color:#1db954;">- Rp <?= number_format($total_diskon,0,',','.') ?></span></div>
    <div class="summary-total">
      <span>Total Bayar:</span>
      <span>Rp <?= number_format($total,0,',','.') ?></span>
    </div>
  </div>
</div>
</body>
</html>
