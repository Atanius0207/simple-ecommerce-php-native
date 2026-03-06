<?php
include '../db.php';

// Cek login & role kasir
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    header("Location: ../login.php");
    exit;
}


$kasir_id = $_SESSION['user']['id']; 
$tanggal = date('Y-m-d');

// Ambil outlet tempat kasir bekerja
$q_kasir = mysqli_query($conn, "SELECT u.outlet_id, o.nama_outlet FROM users u LEFT JOIN outlet o ON u.outlet_id = o.id WHERE u.id = $kasir_id");
$kasir_data = mysqli_fetch_assoc($q_kasir);
$outlet_id = $kasir_data['outlet_id'] ?? 1; // default outlet 1 jika tidak ada
$nama_outlet = $kasir_data['nama_outlet'] ?? 'Outlet Default';

// === Total penjualan per outlet (offline) ===
$q_total_offline = mysqli_query($conn, "
    SELECT SUM(total) as total 
    FROM transaksi 
    WHERE DATE(tanggal)='$tanggal' 
    AND status='lunas' 
    AND outlet_id=$outlet_id
");
$total_offline = mysqli_fetch_assoc($q_total_offline)['total'] ?? 0;

// === Total penjualan online (global) ===
$q_total_online = mysqli_query($conn, "
    SELECT SUM(total) as total 
    FROM transaksi_online 
    WHERE DATE(tanggal)='$tanggal' 
    AND status='lunas'
");
$total_online = mysqli_fetch_assoc($q_total_online)['total'] ?? 0;

// === Gabungkan total offline + online ===
$total_harian = $total_offline + $total_online;

// === Jumlah transaksi offline per outlet ===
$q_trans_offline = mysqli_query($conn, "
    SELECT COUNT(*) as jml 
    FROM transaksi 
    WHERE DATE(tanggal)='$tanggal' 
    AND status='lunas' 
    AND outlet_id=$outlet_id
");
$jumlah_trans_offline = mysqli_fetch_assoc($q_trans_offline)['jml'] ?? 0;

// === Jumlah transaksi online (global) ===
$q_trans_online = mysqli_query($conn, "
    SELECT COUNT(*) as jml 
    FROM transaksi_online 
    WHERE DATE(tanggal)='$tanggal' 
    AND status='lunas'
");
$jumlah_trans_online = mysqli_fetch_assoc($q_trans_online)['jml'] ?? 0;

// === Total semua transaksi ===
$jumlah_transaksi = $jumlah_trans_offline + $jumlah_trans_online;

// === Produk terlaris per outlet (offline) ===
$q_top = mysqli_query($conn, "
    SELECT p.nama, SUM(td.qty) as jml 
    FROM transaksi_detail td
    JOIN produk p ON td.produk_id=p.id
    JOIN transaksi t ON td.transaksi_id=t.id
    WHERE DATE(t.tanggal)='$tanggal' 
    AND t.status='lunas' 
    AND t.outlet_id=$outlet_id
    GROUP BY p.id
    ORDER BY jml DESC
    LIMIT 1
");
$top = mysqli_fetch_assoc($q_top);
$produk_terlaris = $top ? $top['nama']." (".$top['jml'].")" : "-";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kasir</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { font-family:'Segoe UI',sans-serif; margin:0; background:#f4f6f9; padding:20px; color:#333; }
        h1 { text-align:center; margin-bottom:20px; color:#2c3e50; }
        .dashboard { max-width:1000px; margin:0 auto; display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:20px; }
        .card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 8px 18px rgba(0,0,0,0.08); text-align:center; transition:.2s; }
        .card:hover { transform:translateY(-4px); box-shadow:0 12px 24px rgba(0,0,0,0.1); }
        .card i { font-size:32px; margin-bottom:12px; color:#28a745; }
        .card h3 { margin:0; font-size:20px; color:#555; font-weight:600; }
        .card p { margin:8px 0 0; font-size:22px; font-weight:700; color:#28a745; }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; background:#28a745; color:#fff; text-decoration:none; padding:12px 20px; border-radius:10px; font-weight:600; transition:.2s; margin-top:20px; }
        .btn:hover { background:#218838; transform:translateY(-2px); box-shadow:0 8px 16px rgba(0,0,0,0.12); }
    </style>
</head>
<body>

<h1><i class="fas fa-cash-register"></i> Dashboard Kasir - Outlet <?= htmlspecialchars($nama_outlet) ?></h1>

<div class="dashboard">
    <div class="card">
        <i class="fas fa-money-bill-wave"></i>
        <h3>Total Penjualan Hari Ini</h3>
        <p>Rp <?= number_format($total_harian,0,',','.') ?></p>
    </div>
    <div class="card">
        <i class="fas fa-receipt"></i>
        <h3>Jumlah Transaksi</h3>
        <p><?= $jumlah_transaksi ?></p>
    </div>
    <div class="card">
        <i class="fas fa-star"></i>
        <h3>Produk Terlaris</h3>
        <p><?= htmlspecialchars($produk_terlaris) ?></p>
    </div>
</div>

<div style="text-align:center;">
    <a href="beranda.php?page=penjualan" class="btn"><i class="fas fa-cart-plus"></i> Buat Transaksi Baru</a>
</div>

</body>
</html>
