<?php
include '../db.php';

// Proses tambah kasir
if (isset($_POST['tambah'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $outlet_id = intval($_POST['outlet_id']);

    // Validasi email kosong
    if (empty($email)) {
        echo "<script>alert('Email wajib diisi!'); window.location='beranda.php?page=kelola_kasir';</script>";
        exit;
    }

    // Cek email duplikat
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Email sudah digunakan, gunakan email lain!'); window.location='beranda.php?page=kelola_kasir';</script>";
        exit;
    }

    $sql = "INSERT INTO users (username, email, password, role, outlet_id) 
            VALUES ('$username', '$email', '$password', 'kasir', $outlet_id)";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Kasir berhasil ditambahkan!'); window.location='beranda.php?page=kelola_kasir';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menambahkan kasir: " . mysqli_error($conn) . "');</script>";
    }
}

// Ambil semua outlet untuk dropdown
$outlet_q = mysqli_query($conn, "SELECT id, nama_outlet FROM outlet ORDER BY nama_outlet ASC");
$outlets = [];
while ($o = mysqli_fetch_assoc($outlet_q)) {
    $outlets[$o['id']] = $o['nama_outlet'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Kasir</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
  body { font-family:'Segoe UI',sans-serif; background:#f4f6f9; margin:0; padding:20px; }
  .card { background:#fff; padding:24px; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.08); max-width:500px; margin:0 auto; }
  h2 { display:flex; align-items:center; gap:10px; font-size:22px; margin:0 0 20px; color:#2c3e50; }
  .form-group { margin-bottom:15px; }
  label { display:block; margin-bottom:6px; font-weight:600; }
  input[type=text], input[type=password], input[type=email], select {
    width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; transition:.2s;
  }
  input:focus, select:focus { border-color:#28a745; box-shadow:0 0 8px rgba(40,167,69,0.3); outline:none; }
  .btn { background:#28a745; color:#fff; border:none; padding:10px 16px; border-radius:8px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:.2s; }
  .btn:hover { background:#218838; transform:translateY(-2px); }
  .btn-cancel { background:#6c757d; margin-left:8px; }
  .btn-cancel:hover { background:#5a6268; }
</style>
</head>
<body>

<div class="card">
  <h2><i class="fas fa-user-plus"></i> Tambah Kasir</h2>
  <form method="post">
    <div class="form-group">
      <label><i class="fas fa-user"></i> Username</label>
      <input type="text" name="username" placeholder="Masukkan username" required>
    </div>
    <div class="form-group">
      <label><i class="fas fa-lock"></i> Password</label>
      <input type="password" name="password" placeholder="Masukkan password" required>
    </div>
    <div class="form-group">
      <label><i class="fas fa-envelope"></i> Email</label>
      <input type="email" name="email" placeholder="Masukkan email" required>
    </div>
    <div class="form-group">
      <label><i class="fas fa-store"></i> Pilih Outlet</label>
      <select name="outlet_id" required>
        <option value="">-- Pilih Outlet --</option>
        <?php foreach ($outlets as $oid => $nama): ?>
          <option value="<?= $oid ?>"><?= htmlspecialchars($nama) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" name="tambah" class="btn"><i class="fas fa-save"></i> Simpan</button>
    <a href="beranda.php?page=kelola_kasir" class="btn btn-cancel"><i class="fas fa-arrow-left"></i> Kembali</a>
  </form>
</div>

</body>
</html>
