<?php
include '../db.php';

// Pastikan ada ID transaksi
$trans_id = intval($_GET['id'] ?? 0);
if ($trans_id <= 0) {
    die("<h3 style='color:red'>ID transaksi tidak valid!</h3>");
}

// Ambil data transaksi
$sql_trans = "SELECT t.*, o.nama_outlet 
              FROM transaksi t 
              LEFT JOIN outlet o ON t.outlet_id = o.id
              WHERE t.id = $trans_id LIMIT 1";
$res_trans = mysqli_query($conn, $sql_trans);
if (!$res_trans || mysqli_num_rows($res_trans) == 0) {
    die("<h3 style='color:red'>Transaksi tidak ditemukan.</h3>");
}
$trans = mysqli_fetch_assoc($res_trans);

// Ambil detail transaksi
$sql_detail = "SELECT td.*, p.nama 
               FROM transaksi_detail td 
               JOIN produk p ON td.produk_id = p.id 
               WHERE td.transaksi_id = $trans_id";
$res_detail = mysqli_query($conn, $sql_detail);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Transaksi #<?= $trans_id ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
  body { font-family:'Segoe UI',sans-serif; background:#f4f6f9; margin:0; padding:20px; color:#333; }
  .card { background:#fff; padding:24px; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.08); max-width:900px; margin:0 auto; }
  h2 { margin:0 0 20px; font-size:22px; color:#2c3e50; }
  .info { margin-bottom:12px; font-size:15px; }
  .info strong { color:#111; }
  table { width:100%; border-collapse:collapse; margin-top:14px; font-size:14px; }
  th,td { padding:12px; border-bottom:1px solid #eee; text-align:left; }
  th { background:#28a745; color:#fff; }
  tr:hover { background:#f1fdf4; transition:.2s; }
  .total-box { margin-top:18px; padding:14px; border-radius:10px; background:#e6f9ed; font-weight:700; color:#065f46; display:flex; justify-content:space-between; align-items:center; }
  .btn-back { display:inline-block; margin-top:20px; background:#28a745; color:#fff; padding:10px 18px; border-radius:8px; text-decoration:none; font-weight:600; }
  .btn-back:hover { background:#218838; }
</style>
</head>
<body>

<div class="card">
  <h2><i class="fas fa-receipt"></i> Detail Transaksi #<?= $trans['id'] ?></h2>

  <div class="info"><strong>Tanggal:</strong> <?= htmlspecialchars($trans['tanggal']) ?></div>
  <div class="info"><strong>Outlet:</strong> <?= htmlspecialchars($trans['nama_outlet'] ?? '-') ?></div>
  <div class="info"><strong>Status:</strong> <?= htmlspecialchars($trans['status']) ?></div>

  <table>
    <tr>
      <th>Produk</th>
      <th>Harga</th>
      <th>Qty</th>
      <th>Subtotal</th>
    </tr>
    <?php while($d = mysqli_fetch_assoc($res_detail)): ?>
    <tr>
      <td><?= htmlspecialchars($d['nama']) ?></td>
      <td>Rp <?= number_format($d['harga'],0,',','.') ?></td>
      <td><?= $d['qty'] ?></td>
      <td>Rp <?= number_format($d['subtotal'],0,',','.') ?></td>
    </tr>
    <?php endwhile; ?>
  </table>

  <div class="total-box">
    <span><i class="fas fa-coins"></i> Total Transaksi</span>
    <span>Rp <?= number_format($trans['total'],0,',','.') ?></span>
  </div>

  <a href="beranda.php?page=riwayat" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

</body>
</html>
