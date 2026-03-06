<?php
include '../db.php';

// Cek ID produk
if (!isset($_GET['id'])) {
    echo "<script>alert('ID produk tidak ditemukan'); window.location='beranda.php?page=produk';</script>";
    exit;
}
$id = intval($_GET['id']);
$q = mysqli_query($conn, "SELECT * FROM produk WHERE id=$id LIMIT 1");
if (mysqli_num_rows($q) == 0) {
    echo "<script>alert('Produk tidak ditemukan'); window.location='beranda.php?page=produk';</script>";
    exit;
}
$data = mysqli_fetch_assoc($q);

// Update produk
if (isset($_POST['update'])) {
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga  = intval($_POST['harga']);
    $stok   = intval($_POST['stok']);
    $gambar = $data['gambar'];

    // Upload gambar baru jika ada
    if (!empty($_FILES['gambar']['name'])) {
        $file_name = time() . "_" . basename($_FILES['gambar']['name']);
        $target = "../asset/img/" . $file_name;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
            $gambar = $file_name;
        }
    }

    mysqli_query($conn, "UPDATE produk SET nama='$nama', harga='$harga', stok='$stok', gambar='$gambar' WHERE id=$id");
    echo "<script>alert('Produk berhasil diperbarui'); window.location='beranda.php?page=produk';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Produk</title>
<!-- Tambahkan Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  body { font-family:'Segoe UI',sans-serif; background:#f4f6f9; margin:0; padding:20px; }
  .card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.08); max-width:600px; margin:0 auto; }
  h2 { margin-bottom:20px; display:flex; align-items:center; gap:8px; }
  .form-group { margin-bottom:12px; }
  label { display:block; font-weight:600; margin-bottom:6px; }
  input[type=text], input[type=number], input[type=file] {
    width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; transition:.2s;
  }
  input:focus { border-color:#28a745; box-shadow:0 0 8px rgba(40,167,69,0.3); outline:none; }
  img { margin-top:10px; border-radius:6px; }
  .btn { background:#28a745; border:none; padding:10px 18px; border-radius:8px; color:#fff; font-weight:600; cursor:pointer; transition:.2s; display:inline-flex; align-items:center; gap:6px; text-decoration:none; }
  .btn:hover { background:#218838; transform:translateY(-2px); box-shadow:0 6px 15px rgba(0,0,0,0.15); }
  .btn-cancel { background:#6c757d; margin-left:10px; }
  .btn-cancel:hover { background:#5a6268; }
</style>
</head>
<body>
<div class="card">
  <h2><i class="fas fa-pen-to-square"></i> Edit Produk</h2>
  <form method="post" enctype="multipart/form-data">
    <div class="form-group">
      <label><i class="fas fa-tag"></i> Nama Produk</label>
      <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>
    </div>
    <div class="form-group">
      <label><i class="fas fa-money-bill-wave"></i> Harga</label>
      <input type="number" name="harga" value="<?= $data['harga'] ?>" required>
    </div>
    <div class="form-group">
      <label><i class="fas fa-boxes"></i> Stok</label>
      <input type="number" name="stok" value="<?= $data['stok'] ?>" required>
    </div>
    <div class="form-group">
      <label><i class="fas fa-image"></i> Gambar Produk</label>
      <input type="file" name="gambar">
      <?php if($data['gambar']) { ?>
        <img src="../<?= $data['gambar'] ?>" alt="gambar produk" height="60">
      <?php } ?>
    </div>
    <button type="submit" name="update" class="btn"><i class="fas fa-save"></i> Update</button>
    <a href="beranda.php?page=produk" class="btn btn-cancel"><i class="fas fa-arrow-left"></i> Kembali</a>
  </form>
</div>
</body>
</html>
