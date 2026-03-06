<?php
include '../db.php';

$type      = isset($_GET['type']) ? $_GET['type'] : 'penjualan_offline';
$tgl_awal  = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

if ($type == 'penjualan_offline') {
    $q = mysqli_query($conn, "SELECT t.id, t.tanggal, t.total, o.nama_outlet 
                              FROM transaksi t 
                              LEFT JOIN outlet o ON t.outlet_id=o.id
                              WHERE t.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
                              ORDER BY t.tanggal DESC");
    $judul = "Laporan Penjualan Offline";
    $icon = "fa-store";
} elseif ($type == 'penjualan_online') {
    $q = mysqli_query($conn, "SELECT toln.id, toln.tanggal, toln.total, toln.nama_penerima 
                              FROM transaksi_online toln
                              WHERE DATE(toln.tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                              ORDER BY toln.tanggal DESC");
    $judul = "Laporan Penjualan Online";
    $icon = "fa-globe";
} else {
    $q = mysqli_query($conn, "SELECT p.id, p.tanggal, p.supplier, p.total 
                              FROM pembelian p 
                              WHERE p.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
                              ORDER BY p.tanggal DESC");
    $judul = "Laporan Pembelian";
    $icon = "fa-truck";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title><?= $judul ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
  body { font-family:'Segoe UI',sans-serif; background:#f4f6f9; margin:0; padding:20px; color:#333; }
  .card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.08); margin-bottom:20px; }
  h2 { margin-bottom:20px; display:flex; align-items:center; gap:10px; color:#2c3e50; }
  h2 i { color:#28a745; }
  .form-filter { display:flex; gap:10px; margin-bottom:15px; align-items:center; flex-wrap:wrap; }
  input[type=date], select { padding:8px; border:1px solid #ccc; border-radius:6px; }
  .btn { background:#28a745; border:none; padding:8px 14px; border-radius:8px; color:#fff; font-weight:600; cursor:pointer; transition:.2s; display:flex; align-items:center; gap:5px; }
  .btn:hover { background:#218838; transform:translateY(-2px); }
  table { width:100%; border-collapse:collapse; margin-top:10px; }
  th,td { padding:12px; border-bottom:1px solid #eee; text-align:left; }
  th { background:#28a745; color:#fff; }
  tr:hover { background:#f1fdf4; transition:.3s; }
  .total-row { font-weight:700; background:#f1fdf4; }
</style>
</head>
<body>

<div class="card">
  <h2><i class="fas <?= $icon ?>"></i> <?= $judul ?></h2>
  <form method="get" class="form-filter">
    <input type="hidden" name="page" value="laporan">
    <label>Jenis: 
      <select name="type">
        <option value="penjualan_offline" <?= $type=='penjualan_offline'?'selected':'' ?>>Penjualan Offline</option>
        <option value="penjualan_online" <?= $type=='penjualan_online'?'selected':'' ?>>Penjualan Online</option>
        <option value="pembelian" <?= $type=='pembelian'?'selected':'' ?>>Pembelian</option>
      </select>
    </label>
    <label>Dari: <input type="date" name="tgl_awal" value="<?= $tgl_awal ?>"></label>
    <label>Sampai: <input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>"></label>
    <button type="submit" class="btn"><i class="fas fa-search"></i> Tampilkan</button>
  </form>

  <table>
    <tr>
      <?php if ($type=='penjualan_offline') { ?>
        <th><i class="fas fa-hashtag"></i> ID</th>
        <th><i class="fas fa-calendar-day"></i> Tanggal</th>
        <th><i class="fas fa-store"></i> Outlet</th>
        <th><i class="fas fa-money-bill-wave"></i> Total</th>
      <?php } elseif ($type=='penjualan_online') { ?>
        <th><i class="fas fa-hashtag"></i> ID</th>
        <th><i class="fas fa-calendar-day"></i> Tanggal</th>
        <th><i class="fas fa-user"></i> Customer</th>
        <th><i class="fas fa-money-bill-wave"></i> Total</th>
      <?php } else { ?>
        <th><i class="fas fa-hashtag"></i> ID</th>
        <th><i class="fas fa-calendar-day"></i> Tanggal</th>
        <th><i class="fas fa-truck"></i> Supplier</th>
        <th><i class="fas fa-money-bill-wave"></i> Total</th>
      <?php } ?>
    </tr>
    <?php 
    $grand = 0;
    while($row = mysqli_fetch_assoc($q)) { 
        $grand += $row['total'];
    ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= $row['tanggal'] ?></td>
      <?php if ($type=='penjualan_offline') { ?>
        <td><?= $row['nama_outlet'] ?></td>
      <?php } elseif ($type=='penjualan_online') { ?>
        <td><?= $row['nama_penerima'] ?></td>
      <?php } else { ?>
        <td><?= htmlspecialchars($row['supplier']) ?></td>
      <?php } ?>
      <td>Rp <?= number_format($row['total'],0,',','.') ?></td>
    </tr>
    <?php } ?>
    <tr class="total-row">
      <td colspan="3" style="text-align:right;">TOTAL</td>
      <td>Rp <?= number_format($grand,0,',','.') ?></td>
    </tr>
  </table>
</div>
  <a href="beranda.php?page=laporanpdf&type=<?= $type ?>&tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>" target="_blank" class="btn">
  <i class="fas fa-file-pdf"></i> Export PDF
  </a>

</body>
</html>
