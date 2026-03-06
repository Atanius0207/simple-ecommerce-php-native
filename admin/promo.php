<?php
include '../db.php';

// Tambah Promo
if (isset($_POST['tambah'])) {
    $produk_id = intval($_POST['produk_id']);
    $diskon = intval($_POST['diskon']);
    $tgl_mulai = $_POST['tanggal_mulai'];
    $tgl_akhir = $_POST['tanggal_akhir'];

    mysqli_query($conn, "INSERT INTO promo (produk_id, diskon, tanggal_mulai, tanggal_akhir)
                         VALUES ($produk_id, $diskon, '$tgl_mulai', '$tgl_akhir')");

    echo "<script>alert('Promo berhasil ditambahkan'); window.location='beranda.php?page=promo';</script>";
    exit;
}

// Hapus Promo
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM promo WHERE id=$id");
    echo "<script>alert('Promo berhasil dihapus'); window.location='beranda.php?page=promo';</script>";
    exit;
}

// Ambil data promo
$promo_q = mysqli_query($conn, "SELECT promo.*, produk.nama 
                                FROM promo 
                                JOIN produk ON promo.produk_id = produk.id 
                                ORDER BY promo.id DESC");

// Ambil data produk untuk pilihan
$produk_q = mysqli_query($conn, "SELECT id, nama FROM produk ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Promo</title>
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; color:#333; }
    h2 { color:#1db954; margin-bottom:20px; }
    .card { background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); margin-bottom:20px; }
    .form-group { margin-bottom:15px; }
    label { display:block; margin-bottom:5px; font-weight:600; }
    input, select { width:100%; padding:8px; border:1px solid #ccc; border-radius:6px; }
    table { width:100%; border-collapse:collapse; margin-top:20px; }
    th, td { padding:12px; border-bottom:1px solid #ddd; text-align:left; }
    th { background:#1db954; color:#fff; }
    tr:hover { background:#f9f9f9; }
    .btn { padding:8px 14px; border:none; border-radius:6px; cursor:pointer; text-decoration:none; }
    .btn-add { background:#1db954; color:#fff; }
    .btn-del { background:#e63946; color:#fff; }
  </style>
</head>
<body>

<div class="card">
  <h2><i class="fas fa-tags"></i> Tambah Promo</h2>
  <form method="post">
    <div class="form-group">
      <label><i class="fas fa-box"></i> Produk</label>
      <select name="produk_id" required>
        <option value="">-- Pilih Produk --</option>
        <?php while($p = mysqli_fetch_assoc($produk_q)) { ?>
          <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
        <?php } ?>
      </select>
    </div>
    <div class="form-group">
      <label><i class="fas fa-percent"></i> Diskon (%)</label>
      <input type="number" name="diskon" min="1" max="100" required>
    </div>
    <div class="form-group">
      <label><i class="fas fa-calendar-day"></i> Tanggal Mulai</label>
      <input type="date" name="tanggal_mulai" required>
    </div>
    <div class="form-group">
      <label><i class="fas fa-calendar-check"></i> Tanggal Akhir</label>
      <input type="date" name="tanggal_akhir" required>
    </div>
    <button type="submit" name="tambah" class="btn btn-add"><i class="fas fa-save"></i> Simpan Promo</button>
  </form>
</div>

<div class="card">
  <h2><i class="fas fa-list"></i> Daftar Promo</h2>
  <table>
    <tr>
      <th><i class="fas fa-box-open"></i> Produk</th>
      <th><i class="fas fa-percent"></i> Diskon</th>
      <th><i class="fas fa-calendar-day"></i> Tanggal Mulai</th>
      <th><i class="fas fa-calendar-check"></i> Tanggal Akhir</th>
      <th><i class="fas fa-cogs"></i> Aksi</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($promo_q)) { ?>
    <tr>
      <td><?= htmlspecialchars($row['nama']) ?></td>
      <td><?= $row['diskon'] ?>%</td>
      <td><?= $row['tanggal_mulai'] ?></td>
      <td><?= $row['tanggal_akhir'] ?></td>
      <td>
        <a href="promo.php?hapus=<?= $row['id'] ?>" class="btn btn-del" onclick="return confirm('Hapus promo ini?')">
          <i class="fas fa-trash"></i> Hapus
        </a>
      </td>
    </tr>
    <?php } ?>
  </table>
</div>

</body>
</html>
