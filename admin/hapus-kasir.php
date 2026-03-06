<?php
include '../db.php';

// Pastikan ada ID kasir yang dikirim
if (!isset($_GET['id'])) {
    echo "<script>alert('ID kasir tidak ditemukan!'); window.location='beranda.php?page=kelola_kasir';</script>";
    exit;
}

$id = intval($_GET['id']);

// Cek apakah kasir ada di database
$q = mysqli_query($conn, "SELECT * FROM users WHERE id=$id AND role='kasir' LIMIT 1");
if (mysqli_num_rows($q) == 0) {
    echo "<script>alert('Kasir tidak ditemukan!'); window.location='beranda.php?page=kelola_kasir';</script>";
    exit;
}

$data = mysqli_fetch_assoc($q);

// Hapus kasir
if (isset($_POST['hapus'])) {
    mysqli_query($conn, "DELETE FROM users WHERE id=$id AND role='kasir'");
    echo "<script>alert('Kasir berhasil dihapus!'); window.location='beranda.php?page=kelola_kasir';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Hapus Kasir</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
  body { font-family:'Segoe UI',sans-serif; background:#f4f6f9; margin:0; padding:20px; color:#333; }
  .card { background:#fff; padding:24px; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.08); max-width:500px; margin:40px auto; text-align:center; }
  h2 { font-size:20px; color:#c82333; margin-bottom:20px; }
  p { margin-bottom:20px; }
  .btn { padding:10px 16px; border:none; border-radius:8px; font-weight:600; cursor:pointer; margin:4px; display:inline-flex; align-items:center; gap:6px; }
  .btn-danger { background:#dc3545; color:#fff; }
  .btn-danger:hover { background:#c82333; }
  .btn-secondary { background:#6c757d; color:#fff; }
  .btn-secondary:hover { background:#5a6268; }
</style>
</head>
<body>

<div class="card">
  <h2><i class="fas fa-trash-alt"></i> Hapus Kasir</h2>
  <p>Apakah Anda yakin ingin menghapus kasir <b><?= htmlspecialchars($data['username']) ?></b>?</p>
  <form method="post">
    <button type="submit" name="hapus" class="btn btn-danger"><i class="fas fa-check"></i> Ya, Hapus</button>
    <a href="beranda.php?page=kelola_kasir" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
  </form>
</div>

</body>
</html>
