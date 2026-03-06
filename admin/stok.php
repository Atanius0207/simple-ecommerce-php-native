<?php
include '../db.php';

// Update stok
if (isset($_POST['update_stok'])) {
    $id     = intval($_POST['id_produk']);
    $jumlah = intval($_POST['jumlah']);
    $aksi   = $_POST['aksi']; // tambah atau kurang

    $q = mysqli_query($conn, "SELECT stok FROM produk WHERE id=$id LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        $stok_lama = $row['stok'];

        if ($aksi == "tambah") {
            $stok_baru = $stok_lama + $jumlah;
        } else {
            $stok_baru = max(0, $stok_lama - $jumlah);
        }

        mysqli_query($conn, "UPDATE produk SET stok=$stok_baru WHERE id=$id");
        echo "<script>alert('Stok berhasil diperbarui'); window.location='beranda.php?page=stok';</script>";
        exit;
    }
}

// Ambil semua produk
$produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Stok</title>
<!-- Tambahkan Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  body { font-family:'Segoe UI',sans-serif; background:#f4f6f9; margin:0; padding:20px; color:#333; }
  h2 { margin-bottom:20px; display:flex; align-items:center; gap:8px; }
  .card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.08); margin-bottom:20px; }
  table { width:100%; border-collapse:collapse; margin-top:10px; }
  th,td { padding:12px; border-bottom:1px solid #eee; text-align:left; }
  th { background:#28a745; color:#fff; }
  tr:hover { background:#f1fdf4; transition:.3s; }
  .form-inline { display:flex; gap:8px; align-items:center; }
  input[type=number] { width:80px; padding:8px; border:1px solid #ccc; border-radius:6px; }
  select { padding:8px; border:1px solid #ccc; border-radius:6px; }
  .btn { background:#28a745; border:none; padding:8px 14px; border-radius:8px; color:#fff; font-weight:600; cursor:pointer; transition:.2s; display:flex; align-items:center; gap:6px; }
  .btn:hover { background:#218838; transform:translateY(-2px); box-shadow:0 6px 15px rgba(0,0,0,0.15); }
</style>
</head>
<body>

<div class="card">
  <h2><i class="fas fa-warehouse"></i> Kelola Stok Produk</h2>
  <table>
    <tr>
      <th><i class="fas fa-hashtag"></i> ID</th>
      <th><i class="fas fa-tag"></i> Nama Produk</th>
      <th><i class="fas fa-money-bill-wave"></i> Harga</th>
      <th><i class="fas fa-boxes"></i> Stok Saat Ini</th>
      <th><i class="fas fa-cogs"></i> Update Stok</th>
    </tr>
    <?php while($p = mysqli_fetch_assoc($produk)) { ?>
    <tr>
      <td><?= $p['id'] ?></td>
      <td><?= htmlspecialchars($p['nama']) ?></td>
      <td>Rp <?= number_format($p['harga'],0,',','.') ?></td>
      <td><b><?= $p['stok'] ?></b></td>
      <td>
        <form method="post" class="form-inline">
          <input type="hidden" name="id_produk" value="<?= $p['id'] ?>">
          <select name="aksi">
            <option value="tambah">Tambah</option>
            <option value="kurang">Kurang</option>
          </select>
          <input type="number" name="jumlah" value="1" min="1" required>
          <button type="submit" name="update_stok" class="btn"><i class="fas fa-sync-alt"></i> Update</button>
        </form>
      </td>
    </tr>
    <?php } ?>
  </table>
</div>

</body>
</html>
