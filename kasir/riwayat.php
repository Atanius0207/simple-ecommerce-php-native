<?php
include '../db.php';
// cek login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$role = $_SESSION['user']['role'];
$outlet_id = $_SESSION['user']['outlet_id'] ?? null;

// Ambil data transaksi offline, hanya 24 jam terakhir
if ($role === 'admin') {
    $sql_offline = "SELECT t.id, t.tanggal, t.total, t.status, o.nama_outlet
            FROM transaksi t
            LEFT JOIN outlet o ON t.outlet_id = o.id
            WHERE TIMESTAMPDIFF(HOUR, t.tanggal, NOW()) < 24
            ORDER BY t.tanggal DESC";
} else {
    $sql_offline = "SELECT t.id, t.tanggal, t.total, t.status, o.nama_outlet
            FROM transaksi t
            LEFT JOIN outlet o ON t.outlet_id = o.id
            WHERE t.outlet_id = $outlet_id
              AND TIMESTAMPDIFF(HOUR, t.tanggal, NOW()) < 24
            ORDER BY t.tanggal DESC";
}
$q_offline = mysqli_query($conn, $sql_offline);

// Ambil data transaksi online, hanya 24 jam terakhir
$sql_online = "SELECT to1.id, to1.tanggal, to1.total, to1.status, to1.metode_pembayaran, 
                      to1.alamat_pengiriman, tp.nama_penerima
               FROM transaksi_online to1
               JOIN transaksi_pelanggan tp ON to1.pelanggan_id = tp.id
               WHERE TIMESTAMPDIFF(HOUR, to1.tanggal, NOW()) < 24
               ORDER BY to1.tanggal DESC";
$q_online = mysqli_query($conn, $sql_online);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Riwayat Transaksi</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
  body { font-family:'Segoe UI',sans-serif; background:#ffffff; margin:0; padding:20px; color:#333; }
  .card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.08); max-width:1100px; margin:20px auto; }
  h2 { margin-bottom:15px; color:#1db954; }
  .note { text-align:center; margin-bottom:15px; font-size:14px; color:#555; background:#e9fbe9; padding:8px; border-radius:6px; border:1px dashed #1db954; }
  table { width:100%; border-collapse:collapse; margin-top:10px; table-layout:fixed; border-radius:8px; overflow:hidden; }
  th,td { padding:12px; border-bottom:1px solid #eee; text-align:left; vertical-align:top; }
  th { background:#1db954; color:#fff; }
  tr { transition:.2s; }
  tr:hover { background:#f1fdf4; }

  /* kolom alamat pengiriman */
  td:nth-child(4), th:nth-child(4) {
    max-width: 250px;
    white-space: normal;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }

  /* kolom aksi */
  td:last-child, th:last-child {
    text-align: center;
    width: 100px;
  }

  .status { font-weight:600; padding:6px 10px; border-radius:6px; font-size:13px; }
  .lunas { background:#d1fae5; color:#065f46; }
  .pending { background:#fef3c7; color:#92400e; }
  .batal { background:#fee2e2; color:#991b1b; }

  .btn-detail { 
    background:#1db954; 
    color:#fff; 
    padding: 6px 12px; 
    border-radius:6px; 
    text-decoration:none; 
    font-size:13px; 
    transition:.2s; 
    display:inline-block; 
    min-width:80px; 
    text-align:center;
  }
  .btn-detail:hover { background:#17a84b; }
</style>
</head>
<body>

<!-- Note -->
<div class="note">
  <i class="fas fa-info-circle"></i> Data transaksi hanya tampil selama 24 jam terakhir, tetapi tetap tersimpan di database.
</div>

<!-- Card Offline -->
<div class="card">
  <h2><i class="fas fa-store"></i> Riwayat Transaksi Offline</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Tanggal</th>
      <th>Outlet</th>
      <th>Total</th>
      <th>Status</th>
      <th>Aksi</th>
    </tr>
    <?php if (mysqli_num_rows($q_offline) > 0): ?>
      <?php while($t = mysqli_fetch_assoc($q_offline)): ?>
        <tr>
          <td>#<?= $t['id'] ?></td>
          <td><?= date('d-m-Y', strtotime($t['tanggal'])) ?></td>
          <td><?= $t['nama_outlet'] ?: '-' ?></td>
          <td>Rp <?= number_format($t['total'],0,',','.') ?></td>
          <td>
            <span class="status <?= $t['status']=='lunas'?'lunas':($t['status']=='pending'?'pending':'batal') ?>">
              <?= ucfirst($t['status']) ?>
            </span>
          </td>
          <td><a class="btn-detail" href="beranda.php?page=detail&id=<?= $t['id'] ?>"><i class="fas fa-eye"></i> Detail</a></td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="6" style="text-align:center;">Tidak ada transaksi offline dalam 24 jam terakhir.</td></tr>
    <?php endif; ?>
  </table>
</div>

<!-- Card Online -->
<div class="card">
  <h2><i class="fas fa-globe"></i> Riwayat Transaksi Online</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Nama Pelanggan</th>
      <th>Tanggal</th>
      <th>Alamat Pengiriman</th>
      <th>Metode Pembayaran</th>
      <th>Total</th>
      <th>Status</th>
      <th>Aksi</th>
    </tr>
    <?php if (mysqli_num_rows($q_online) > 0): ?>
      <?php while($o = mysqli_fetch_assoc($q_online)): ?>
        <tr>
          <td>#<?= $o['id'] ?></td>
          <td><?= $o['nama_penerima'] ?></td>
          <td><?= date('d-m-Y', strtotime($o['tanggal'])) ?></td>
          <td><?= $o['alamat_pengiriman'] ?: '-' ?></td>
          <td><?= $o['metode_pembayaran'] ?: '-' ?></td>
          <td>Rp <?= number_format($o['total'],0,',','.') ?></td>
          <td>
            <span class="status <?= $o['status']=='lunas'?'lunas':($o['status']=='pending'?'pending':'batal') ?>">
              <?= ucfirst($o['status']) ?>
            </span>
          </td>
          <td><a class="btn-detail" href="beranda.php?page=detail-online&id=<?= $o['id'] ?>"><i class="fas fa-eye"></i> Detail</a></td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="8" style="text-align:center;">Tidak ada transaksi online dalam 24 jam terakhir.</td></tr>
    <?php endif; ?>
  </table>
</div>

</body>
</html>
