<?php
include '../db.php';

// Update outlet kasir
if (isset($_POST['update'])) {
    $kasir_id  = intval($_POST['kasir_id']);
    $outlet_id = intval($_POST['outlet_id']);

    $sql = "UPDATE users SET outlet_id = $outlet_id WHERE id = $kasir_id AND role='kasir'";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Outlet kasir berhasil diperbarui!'); window.location='beranda.php?page=kelola_kasir';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal update outlet: " . mysqli_error($conn) . "');</script>";
    }
}

// Ambil semua kasir
$kasir_q = mysqli_query($conn, "SELECT u.id, u.username, u.outlet_id, o.nama_outlet 
                                FROM users u 
                                LEFT JOIN outlet o ON u.outlet_id = o.id 
                                WHERE u.role = 'kasir' 
                                ORDER BY u.username ASC");

// Ambil semua outlet untuk dropdown
$outlet_q = mysqli_query($conn, "SELECT id, nama_outlet FROM outlet ORDER BY nama_outlet ASC");
$outlets = [];
while($o = mysqli_fetch_assoc($outlet_q)) {
    $outlets[$o['id']] = $o['nama_outlet'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Kasir</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
  body { font-family:'Segoe UI',sans-serif; background:#f4f6f9; margin:0; padding:20px; color:#333; }
  .card { background:#fff; padding:24px; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.08); max-width:1000px; margin:0 auto; }
  h2 { margin:0 0 20px; font-size:22px; color:#2c3e50; display:flex; align-items:center; gap:10px; }
  table { width:100%; border-collapse:collapse; font-size:14px; margin-top:10px; }
  th,td { padding:12px; border-bottom:1px solid #eee; text-align:left; }
  th { background:#28a745; color:#fff; }
  th i { margin-right:6px; }
  tr:hover { background:#f1fdf4; transition:.2s; }
  select { padding:6px; border-radius:6px; border:1px solid #ccc; }
  .btn { background:#28a745; color:#fff; border:none; padding:8px 14px; border-radius:6px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px; }
  .btn:hover { background:#218838; }
</style>
</head>
<body>

<div class="card">
  <h2><i class="fas fa-users-cog"></i> Kelola Kasir</h2>
  <a href="beranda.php?page=tambah-kasir" class="btn" style="text-decoration:none"><i class="fas fa-user-plus"></i> Tambah Kasir</a>
  <table>
    <tr>
      <th><i class="fas fa-user"></i> Username</th>
      <th><i class="fas fa-store"></i> Outlet Sekarang</th>
      <th><i class="fas fa-exchange-alt"></i> Ganti Outlet</th>
      <th><i class="fas fa-cogs"></i> Aksi</th>
    </tr>
    <?php while($k = mysqli_fetch_assoc($kasir_q)): ?>
    <tr>
      <td><i class="fas fa-id-card"></i> <?= htmlspecialchars($k['username']) ?></td>
      <td><?= $k['nama_outlet'] ? '<i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($k['nama_outlet']) : '<span style="color:#999"><i class="fas fa-times-circle"></i> Belum diatur</span>' ?></td>
      <td>
        <form method="post" style="display:flex; gap:6px; align-items:center;">
          <input type="hidden" name="kasir_id" value="<?= $k['id'] ?>">
          <select name="outlet_id" required>
            <option value="">-- Pilih Outlet --</option>
            <?php foreach($outlets as $oid => $nama): ?>
              <option value="<?= $oid ?>" <?= ($oid == $k['outlet_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($nama) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button type="submit" name="update" class="btn"><i class="fas fa-save"></i> Simpan</button>
        </form>
      </td>
      <td>
        <!-- Tambah tombol hapus kasir jika diperlukan -->
        <a class="btn" style="background:#dc3545; text-decoration:none; " href="beranda.php?page=hapus-kasir&id=<?= $k['id'] ?>"><i class="fas fa-trash-alt"></i> Hapus</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

</body>
</html>
