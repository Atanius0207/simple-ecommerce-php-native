<?php
include '../db.php';

// === Tambah produk ===
if (isset($_POST['tambah'])) {
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga  = intval($_POST['harga']);
    $stok   = intval($_POST['stok']);
    $gambar = "";

    // Upload gambar
    if (!empty($_FILES['gambar']['name'])) {
        $origName = basename($_FILES['gambar']['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];

        if (in_array($ext, $allowed)) {
            // buat nama file aman & unik
            $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
            $file_name = $safeName . '_' . time() . '.' . $ext;
            $targetServerPath = __DIR__ . "/../asset/img/" . $file_name;

            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetServerPath)) {
                $gambar = "asset/img/" . $file_name; // simpan path relatif
            }
        }
    }

    mysqli_query($conn, "INSERT INTO produk (nama, harga, stok, gambar) 
                         VALUES ('$nama', '$harga', '$stok', '$gambar')");
    echo "<script>alert('Produk berhasil ditambahkan'); window.location='beranda.php?page=produk';</script>";
    exit;
}

// === Hapus produk ===
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    // opsional: hapus file gambar dari folder
    $q = mysqli_query($conn, "SELECT gambar FROM produk WHERE id=$id");
    if ($row = mysqli_fetch_assoc($q)) {
        if (!empty($row['gambar']) && file_exists("../" . $row['gambar'])) {
            unlink("../" . $row['gambar']);
        }
    }
    mysqli_query($conn, "DELETE FROM produk WHERE id=$id");
    echo "<script>alert('Produk berhasil dihapus'); window.location='beranda.php?page=produk';</script>";
    exit;
}

// === Ambil semua produk ===
$produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Produk</title>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  body { font-family:'Segoe UI',sans-serif; background:#f4f6f9; margin:0; padding:20px; color:#333; }
  h2 { margin-bottom:20px; display:flex; align-items:center; gap:8px; }
  .card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.08); margin-bottom:20px; }
  .form-group { margin-bottom:12px; }
  label { display:block; font-weight:600; margin-bottom:6px; }
  input[type=text], input[type=number], input[type=file] {
    width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; transition:.2s;
  }
  input:focus { border-color:#28a745; box-shadow:0 0 8px rgba(40,167,69,0.3); outline:none; }
  .btn { background:#28a745; border:none; padding:10px 18px; border-radius:8px; color:#fff; font-weight:600; cursor:pointer; transition:.2s; display:inline-flex; align-items:center; gap:6px; }
  .btn:hover { background:#218838; transform:translateY(-2px); box-shadow:0 6px 15px rgba(0,0,0,0.15); }
  table { width:100%; border-collapse:collapse; margin-top:10px; }
  th,td { padding:12px; border-bottom:1px solid #eee; text-align:left; }
  th { background:#28a745; color:#fff; }
  tr:hover { background:#f1fdf4; transition:.3s; }
  .action a { text-decoration:none; padding:6px 12px; border-radius:6px; font-size:13px; margin-right:4px; display:inline-flex; align-items:center; gap:4px; }
  .edit { background:#007bff; color:#fff; }
  .edit:hover { background:#0069d9; }
  .delete { background:#dc3545; color:#fff; }
  .delete:hover { background:#c82333; }
</style>
</head>
<body>

<div class="card">
  <h2><i class="fas fa-plus-circle"></i> Tambah Produk Baru</h2>
  <form method="post" enctype="multipart/form-data">
    <div class="form-group">
      <label>Nama Produk</label>
      <input type="text" name="nama" required>
    </div>
    <div class="form-group">
      <label>Harga</label>
      <input type="number" name="harga" required>
    </div>
    <div class="form-group">
      <label>Stok</label>
      <input type="number" name="stok" required>
    </div>
    <div class="form-group">
      <label>Gambar Produk</label>
      <input type="file" name="gambar">
    </div>
    <button type="submit" name="tambah" class="btn"><i class="fas fa-plus"></i> Tambah Produk</button>
  </form>
</div>

<div class="card">
  <h2><i class="fas fa-box"></i> Daftar Produk</h2>
  <table>
    <tr>
      <th><i class="fas fa-hashtag"></i> ID</th>
      <th><i class="fas fa-tag"></i> Nama</th>
      <th><i class="fas fa-money-bill-wave"></i> Harga</th>
      <th><i class="fas fa-boxes"></i> Stok</th>
      <th><i class="fas fa-image"></i> Gambar</th>
      <th><i class="fas fa-cogs"></i> Aksi</th>
    </tr>
    <?php while($p = mysqli_fetch_assoc($produk)) { ?>
    <tr>
      <td><?= $p['id'] ?></td>
      <td><?= htmlspecialchars($p['nama']) ?></td>
      <td>Rp <?= number_format($p['harga'],0,',','.') ?></td>
      <td><?= $p['stok'] ?></td>
      <td>
        <?php if($p['gambar']) { ?>
          <img src="../<?= htmlspecialchars($p['gambar']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>" style="height:50px; border-radius:6px;">
        <?php } else { ?>
          <span class="muted">-</span>
        <?php } ?>
      </td>
      <td class="action">
        <a href="beranda.php?page=produk_edit&id=<?= $p['id'] ?>" class="edit"><i class="fas fa-edit"></i> Edit</a>
        <a href="beranda.php?page=produk&hapus=<?= $p['id'] ?>" class="delete" onclick="return confirm('Hapus produk ini?')"><i class="fas fa-trash"></i> Hapus</a>
      </td>
    </tr>
    <?php } ?>
  </table>
</div>

</body>
</html>
