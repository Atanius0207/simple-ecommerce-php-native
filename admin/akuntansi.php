<?php
include '../db.php';

// Ambil filter tanggal
$tgl_awal  = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

// Hitung pemasukan (penjualan)
$q_penjualan = mysqli_query($conn, "SELECT SUM(total) as pemasukan 
                                       FROM transaksi 
                                       WHERE tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'");
$penjualan = mysqli_fetch_assoc($q_penjualan)['pemasukan'] ?? 0;

// Hitung pengeluaran (pembelian)
$q_pembelian = mysqli_query($conn, "SELECT SUM(total) as total 
                                       FROM pembelian 
                                       WHERE tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'");
$pembelian = mysqli_fetch_assoc($q_pembelian)['total'] ?? 0;

// Laba bersih
$laba = $penjualan - $pembelian;

// Piutang pelanggan (transaksi belum lunas)
$q_piutang = mysqli_query($conn, "SELECT COUNT(*) as jml, SUM(total - dibayar) as sisa
                                  FROM transaksi
                                  WHERE status='belum_lunas'");
$piutang = mysqli_fetch_assoc($q_piutang);
$jml_piutang = $piutang['jml'] ?? 0;
$sisa_piutang = $piutang['sisa'] ?? 0;

// Hutang supplier (pembelian belum lunas)
$q_hutang = mysqli_query($conn, "SELECT COUNT(*) as jml, SUM(total - dibayar) as sisa
                                 FROM pembelian
                                 WHERE status='belum_lunas'");
$hutang = mysqli_fetch_assoc($q_hutang);
$jml_hutang = $hutang['jml'] ?? 0;
$sisa_hutang = $hutang['sisa'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Akuntansi</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
  body { font-family:'Segoe UI',sans-serif; background:#f4f6f9; margin:0; padding:20px; color:#333; }
  h2 { margin-bottom:20px; display:flex; align-items:center; gap:10px; }
  .card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.08); margin-bottom:20px; }
  .form-filter { display:flex; gap:10px; margin-bottom:15px; align-items:center; flex-wrap:wrap; }
  input[type=date] { padding:8px; border:1px solid #ccc; border-radius:6px; }
  .btn { background:#28a745; border:none; padding:8px 14px; border-radius:8px; color:#fff; font-weight:600; cursor:pointer; transition:.2s; display:flex; align-items:center; gap:6px; }
  .btn:hover { background:#218838; transform:translateY(-2px); }
  .summary { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:20px; margin-top:20px; }
  .box { padding:20px; border-radius:12px; color:#fff; font-weight:700; text-align:center; font-size:18px; display:flex; flex-direction:column; align-items:center; gap:10px; }
  .box i { font-size:28px; margin-bottom:5px; }
  .income { background:linear-gradient(120deg,#28a745,#34ce57); }
  .expense { background:linear-gradient(120deg,#dc3545,#e4606d); }
  .profit { background:linear-gradient(120deg,#007bff,#339af0); }
  .piutang { background:linear-gradient(120deg,#f39c12,#f1c40f); }
  .hutang { background:linear-gradient(120deg,#6f42c1,#9b59b6); }
</style>
</head>
<body>

<div class="card">
  <h2><i class="fas fa-calculator"></i> Akuntansi (Laba/Rugi)</h2>
  <form method="get" class="form-filter">
    <input type="hidden" name="page" value="akuntansi">
    <label><i class="fas fa-calendar-alt"></i> Dari: <input type="date" name="tgl_awal" value="<?= $tgl_awal ?>"></label>
    <label><i class="fas fa-calendar-alt"></i> Sampai: <input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>"></label>
    <button type="submit" class="btn"><i class="fas fa-search"></i> Tampilkan</button>
  </form>

  <div class="summary">
    <div class="box income">
      <i class="fas fa-arrow-up"></i>
      Pemasukan<br>Rp <?= number_format($penjualan,0,',','.') ?>
    </div>
    <div class="box expense">
      <i class="fas fa-arrow-down"></i>
      Pengeluaran<br>Rp <?= number_format($pembelian,0,',','.') ?>
    </div>
    <div class="box profit">
      <i class="fas fa-coins"></i>
      Laba Bersih<br>Rp <?= number_format($laba,0,',','.') ?>
    </div>
    <div class="box piutang">
      <i class="fas fa-user-clock"></i>
      Piutang Pelanggan<br><?= $jml_piutang ?> transaksi<br>
      Rp <?= number_format($sisa_piutang,0,',','.') ?>
    </div>
    <div class="box hutang">
      <i class="fas fa-handshake"></i>
      Hutang Supplier<br><?= $jml_hutang ?> transaksi<br>
      Rp <?= number_format($sisa_hutang,0,',','.') ?>
    </div>
  </div>
</div>

</body>
</html>
