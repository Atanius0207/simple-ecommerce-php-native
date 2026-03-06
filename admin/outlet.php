<?php
include '../db.php';

// Tambah outlet
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_outlet']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    if ($nama != "") {
        mysqli_query($conn, "INSERT INTO outlet (nama_outlet, lokasi) VALUES ('$nama', '$lokasi')");
        echo "<script>alert('Outlet berhasil ditambahkan'); window.location='beranda.php?page=outlet';</script>";
        exit;
    }
}

// Hapus outlet
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM outlet WHERE id=$id");
    echo "<script>alert('Outlet berhasil dihapus'); window.location='beranda.php?page=outlet';</script>";
    exit;
}

// Ambil data outlet
$outlets = mysqli_query($conn, "SELECT * FROM outlet ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Outlet</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  body { font-family: 'Segoe UI', sans-serif; background:#f4f6f9; margin:0; padding:20px; color:#333; }
  h2 { margin-bottom:20px; color:#28a745; }
  .card {
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 6px 18px rgba(0,0,0,0.08);
    margin-bottom:20px;
  }
  .form-group { margin-bottom:12px; }
  label { display:block; font-weight:600; margin-bottom:6px; }
  input[type=text] {
    width:100%; padding:10px; border:1px solid #ccc; border-radius:8px;
    transition:.2s;
  }
  input[type=text]:focus { border-color:#28a745; box-shadow:0 0 8px rgba(40,167,69,0.3); outline:none; }
  .btn {
    background:#28a745; border:none; padding:10px 18px; border-radius:8px;
    color:#fff; font-weight:600; cursor:pointer; transition:.2s;
  }
  .btn:hover { background:#218838; transform:translateY(-2px); box-shadow:0 6px 15px rgba(0,0,0,0.15); }
  table {
    width:100%; border-collapse:collapse; margin-top:10px;
  }
  th,td {
    padding:12px; border-bottom:1px solid #eee; text-align:left;
  }
  th { background:#28a745; color:#fff; }
  tr:hover { background:#f1fdf4; transition:.3s; }
  .action a {
    text-decoration:none; padding:6px 12px; border-radius:6px; font-size:13px; margin-right:4px;
    display:inline-flex; align-items:center; gap:6px;
  }
  .edit { background:#007bff; color:#fff; }
  .edit:hover { background:#0069d9; }
  .delete { background:#dc3545; color:#fff; }
  .delete:hover { background:#c82333; }
</style>
</head>
<body>

<div class="card">
  <h2><i class="fas fa-store"></i> Tambah Outlet Baru</h2>
  <form method="post">
    <div class="form-group">
      <label><i class="fas fa-signature"></i> Nama Outlet</label>
      <input type="text" name="nama_outlet" required>
    </div>
    <div class="form-group">
      <label><i class="fas fa-map-marker-alt"></i> Lokasi</label>
      <input type="text" name="lokasi" required>
    </div>
    <button type="submit" name="tambah" class="btn"><i class="fas fa-plus"></i> Tambah Outlet</button>
  </form>
</div>

<div class="card">
  <h2><i class="fas fa-list"></i> Daftar Outlet</h2>
  <table>
    <tr>
      <th><i class="fas fa-id-badge"></i> ID</th>
      <th><i class="fas fa-store"></i> Nama Outlet</th>
      <th><i class="fas fa-map"></i> Lokasi</th>
      <th><i class="fas fa-cogs"></i> Aksi</th>
    </tr>
    <?php while($o = mysqli_fetch_assoc($outlets)) { ?>
    <tr>
      <td><?= $o['id'] ?></td>
      <td><?= htmlspecialchars($o['nama_outlet']) ?></td>
      <td><?= htmlspecialchars($o['lokasi']) ?></td>
      <td class="action">
        <a href="beranda.php?page=outlet_edit&id=<?= $o['id'] ?>" class="edit">
          <i class="fas fa-edit"></i> Edit
        </a>
        <a href="beranda.php?page=outlet&hapus=<?= $o['id'] ?>" class="delete" onclick="return confirm('Hapus outlet ini?')">
          <i class="fas fa-trash"></i> Hapus
        </a>
      </td>
    </tr>
    <?php } ?>
  </table>
</div>

</body>
</html>
