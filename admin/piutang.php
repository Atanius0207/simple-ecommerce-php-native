<?php
include '../db.php';

// ==== Proses update lunas ==== //
if (isset($_GET['bayar_pelanggan'])) {
    $id = intval($_GET['bayar_pelanggan']);
    mysqli_query($conn, "UPDATE transaksi_pelanggan SET status='selesai' WHERE id=$id");
    echo "<script>alert('Piutang pelanggan ID $id sudah dilunasi'); window.location='beranda.php?page=piutang';</script>";
    exit;
}
if (isset($_GET['bayar_supplier'])) {
    $id = intval($_GET['bayar_supplier']);
    mysqli_query($conn, "UPDATE pembelian SET status ='lunas' WHERE id=$id");
    echo "<script>alert('Hutang ke supplier ID $id sudah dibayar'); window.location='beranda.php?page=piutang';</script>";
    exit;
}

// ==== Ambil piutang pelanggan (status pending/diproses) ==== //
$q = mysqli_query($conn, "
    SELECT tp.id, tp.tanggal, tp.nama_penerima, tp.total, tp.status, u.username 
    FROM transaksi_pelanggan tp
    LEFT JOIN users u ON tp.user_id = u.id
    WHERE tp.status IN ('pending','diproses')
    ORDER BY tp.tanggal DESC
");

// Hitung total piutang
$q_sum = mysqli_query($conn, "
    SELECT SUM(total) as total_piutang 
    FROM transaksi_pelanggan 
    WHERE status IN ('pending','diproses')
");
$total_piutang = mysqli_fetch_assoc($q_sum)['total_piutang'] ?? 0;

// ==== Ambil hutang ke supplier (belum lunas) ==== //
$q_supplier = mysqli_query($conn, "
    SELECT id, tanggal, supplier, total, status 
    FROM pembelian
    WHERE status = 'belum_lunas'
    ORDER BY tanggal DESC
");

// Hitung total hutang
$q_sum_sup = mysqli_query($conn, "
    SELECT SUM(total) as total_hutang 
    FROM pembelian WHERE status ='belum_lunas'
");
$total_hutang = mysqli_fetch_assoc($q_sum_sup)['total_hutang'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Piutang & Hutang</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
  body { font-family:'Segoe UI',sans-serif; background:#f4f6f9; margin:0; padding:20px; color:#333; }
  .card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.08); margin-bottom:30px; }
  h2 { margin-bottom:20px; display:flex; align-items:center; gap:10px; color:#2c3e50; }
  table { width:100%; border-collapse:collapse; margin-top:10px; font-size:14px; }
  th,td { padding:12px; border-bottom:1px solid #eee; text-align:left; }
  th { background:#28a745; color:#fff; }
  tr:hover { background:#f1fdf4; transition:.2s; }
  .summary { display:flex; gap:20px; margin-bottom:20px; flex-wrap:wrap; }
  .box { flex:1; padding:20px; border-radius:12px; color:#fff; font-weight:700; text-align:center; font-size:18px; display:flex; flex-direction:column; align-items:center; gap:6px; }
  .box i { font-size:28px; margin-bottom:5px; }
  .piutang { background:linear-gradient(120deg,#17a2b8,#20c997); }
  .hutang { background:linear-gradient(120deg,#dc3545,#e4606d); }
  .btn { padding:6px 12px; border-radius:6px; text-decoration:none; font-size:13px; font-weight:600; }
  .btn-lunas { background:#28a745; color:#fff; }
  .btn-lunas:hover { background:#218838; }
</style>
</head>
<body>

<div class="card">
  <h2><i class="fas fa-balance-scale"></i> Ringkasan</h2>
  <div class="summary">
    <div class="box piutang">
      <i class="fas fa-hand-holding-usd"></i>
      Piutang Pelanggan<br>Rp <?= number_format($total_piutang,0,',','.') ?>
    </div>
    <div class="box hutang">
      <i class="fas fa-truck-loading"></i>
      Hutang ke Supplier<br>Rp <?= number_format($total_hutang,0,',','.') ?>
    </div>
  </div>
</div>

<div class="card">
  <h2><i class="fas fa-users"></i> Piutang Pelanggan</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Tanggal</th>
      <th>Nama Penerima</th>
      <th>User</th>
      <th>Status</th>
      <th>Total</th>
      <th>Aksi</th>
    </tr>
    <?php while($row=mysqli_fetch_assoc($q)): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= $row['tanggal'] ?></td>
      <td><?= htmlspecialchars($row['nama_penerima']) ?></td>
      <td><?= htmlspecialchars($row['username']) ?></td>
      <td><?= ucfirst($row['status']) ?></td>
      <td>Rp <?= number_format($row['total'],0,',','.') ?></td>
      <td><a href="beranda.php?page=piutang&bayar_pelanggan=<?= $row['id'] ?>" class="btn btn-lunas"><i class="fas fa-check"></i> Tandai Lunas</a></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

<div class="card">
  <h2><i class="fas fa-truck"></i> Hutang ke Supplier</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Tanggal</th>
      <th>Supplier</th>
      <th>Status Bayar</th>
      <th>Total</th>
      <th>Aksi</th>
    </tr>
    <?php while($sup=mysqli_fetch_assoc($q_supplier)): ?>
    <tr>
      <td><?= $sup['id'] ?></td>
      <td><?= $sup['tanggal'] ?></td>
      <td><?= htmlspecialchars($sup['supplier']) ?></td>
      <td><?= ucfirst($sup['status']) ?></td>
      <td>Rp <?= number_format($sup['total'],0,',','.') ?></td>
      <td><a href="beranda.php?page=piutang&bayar_supplier=<?= $sup['id'] ?>" class="btn btn-lunas"><i class="fas fa-money-bill"></i> Bayar</a></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

</body>
</html>
