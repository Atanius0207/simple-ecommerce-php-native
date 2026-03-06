<?php
include '../db.php';

// Cek role kasir
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'kasir') {
    header("Location: ../login.php");
    exit;
}

// Proses update status transaksi
if (isset($_POST['update_status'])) {
    $id_transaksi = intval($_POST['id_transaksi']);
    $status_baru = mysqli_real_escape_string($conn, $_POST['status']);

    // Ambil data transaksi
    $transaksi = mysqli_query($conn, "SELECT * FROM transaksi_pelanggan WHERE id = $id_transaksi");
    $data = mysqli_fetch_assoc($transaksi);

    // Update status di transaksi_pelanggan
    $update = mysqli_query($conn, "UPDATE transaksi_pelanggan SET status = '$status_baru' WHERE id = $id_transaksi");

    // Jika status selesai, pindahkan ke transaksi_online
    if ($update && $status_baru === "Selesai") {
        $nama_penerima = mysqli_real_escape_string($conn, $data['nama_penerima']);
        $tanggal = mysqli_real_escape_string($conn, $data['tanggal']);
        $alamat_pengiriman = mysqli_real_escape_string($conn, $data['alamat']);
        $metode_pembayaran = mysqli_real_escape_string($conn, $data['metode_bayar']);
        $total = $data['total'];

        mysqli_query($conn, "INSERT INTO transaksi_online (pelanggan_id, nama_penerima, tanggal, alamat_pengiriman, metode_pembayaran, total, status) 
                             VALUES ($id_transaksi, '$nama_penerima', '$tanggal', '$alamat_pengiriman', '$metode_pembayaran', $total, 'lunas')");
    }

    echo "<script>alert('Status berhasil diupdate');window.location='beranda.php?page=transaksi';</script>";
    exit;
}

// Ambil data transaksi pelanggan, hanya yang kurang dari 24 jam
$transaksi = mysqli_query($conn, "
    SELECT * FROM transaksi_pelanggan
    WHERE TIMESTAMPDIFF(HOUR, tanggal, NOW()) < 24
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Pelanggan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; margin: 0; padding: 20px; background: #ffffff; color: #333; }
        .container { max-width: 1100px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);}
        h2 { text-align: center; margin-bottom: 15px; color: #1db954; }
        .note { text-align: center; margin-bottom: 15px; font-size: 14px; color: #555; background: #e9fbe9; padding: 8px; border-radius: 6px; border: 1px dashed #1db954; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px 15px; text-align: center; border-bottom: 1px solid #ddd; }
        th { background: #1db954; color: #fff; font-weight: 600; }
        tr:hover { background: #f1fdf4; }
        .btn { padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; transition: 0.3s; }
        .btn-update { background: #1db954; color: #fff; }
        .btn-update:hover { background: #17a84b; }
        select { padding: 6px; border-radius: 6px; border: 1px solid #bbb; background: #fff; color: #333; }
        .status-pending { color: #ff9800; font-weight: bold; }
        .status-proses { color: #03a9f4; font-weight: bold; }
        .status-selesai { color: #4caf50; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fas fa-cash-register"></i> Daftar Transaksi Pelanggan</h2>
    <div class="note">
        <i class="fas fa-info-circle"></i> Data transaksi akan otomatis hilang dari daftar setelah 24 jam, tetapi tetap tersimpan di database.
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Alamat</th>
                <th>Metode Bayar</th>
                <th>Status</th>
                <th>Update Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($transaksi) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($transaksi)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['nama_penerima']) ?></td>
                        <td><?= $row['tanggal'] ?></td>
                        <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                        <td><?= $row['alamat'] ?></td>
                        <td><?= $row['metode_bayar'] ?></td>
                        <td class="status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="id_transaksi" value="<?= $row['id'] ?>">
                                <select name="status">
                                    <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Proses" <?= $row['status'] == 'Proses' ? 'selected' : '' ?>>Proses</option>
                                    <option value="Selesai" <?= $row['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-update"><i class="fas fa-sync-alt"></i> Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8">Tidak ada transaksi dalam 24 jam terakhir.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
