<?php
include '../db.php';

// Ambil data outlet berdasarkan ID
if (!isset($_GET['id'])) {
    echo "<script>alert('ID Outlet tidak ditemukan'); window.location='beranda.php?page=outlet';</script>";
    exit;
}
$id = intval($_GET['id']);
$outlet = mysqli_query($conn, "SELECT * FROM outlet WHERE id=$id LIMIT 1");
if (mysqli_num_rows($outlet) == 0) {
    echo "<script>alert('Outlet tidak ditemukan'); window.location='beranda.php?page=outlet';</script>";
    exit;
}
$data = mysqli_fetch_assoc($outlet);

// Update data outlet
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_outlet']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    mysqli_query($conn, "UPDATE outlet SET nama_outlet='$nama', lokasi='$lokasi' WHERE id=$id");
    echo "<script>alert('Outlet berhasil diperbarui'); window.location='beranda.php?page=outlet';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Outlet</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  body { font-family:'Segoe UI',sans-serif; background:#f4f6f9; margin:0; padding:20px; }
  .card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.08); max-width:600px; margin:0 auto; }
  h2 { margin-bottom:20px; color:#28a745; }
  .form-group { margin-bottom:15px; }
  label { display:block; font-weight:600; margin-bottom:6px; }
  input[type=text] { width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; transition:.2s; }
  input[type=text]:focus { border-color:#28a745; box-shadow:0 0 8px rgba(40,167,69,0.3); outline:none; }
  .btn { background:#28a745; border:none; padding:10px 18px; border-radius:8px; color:#fff; font-weight:600; cursor:pointer; transition:.2s; display:inline-flex; align-items:center; gap:8px; }
  .btn:hover { background:#218838; transform:translateY(-2px); box-shadow:0 6px 15px rgba(0,0,0,0.15); }
  .btn-cancel { background:#6c757d; margin-left:10px; }
  .btn-cancel:hover { background:#5a6268; }
</style>
</head>
<body>
<div class="card">
  <h2><i class="fas fa-edit"></i> Edit Outlet</h2>
  <form method="post">
    <div class="form-group">
      <label><i class="fas fa-store"></i> Nama Outlet</label>
      <input type="text" name="nama_outlet" value="<?= htmlspecialchars($data['nama_outlet']) ?>" required>
    </div>
    <div class="form-group">
      <label><i class="fas fa-map-marker-alt"></i> Lokasi</label>
      <input type="text" name="lokasi" value="<?= htmlspecialchars($data['lokasi']) ?>" required>
    </div>
    <button type="submit" name="update" class="btn"><i class="fas fa-save"></i> Update</button>
    <a href="beranda.php?page=outlet" class="btn btn-cancel"><i class="fas fa-arrow-left"></i> Kembali</a>
  </form>
</div>
</body>
</html>
