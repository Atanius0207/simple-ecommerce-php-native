<?php
include '../db.php';

// Pastikan hanya pelanggan yang bisa akses
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pelanggan') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Ambil riwayat transaksi
$sql = "SELECT t.id, t.user_id, t.nama_penerima, t.alamat, t.metode_bayar, t.total, t.status, t.tanggal
        FROM transaksi_pelanggan t
        WHERE t.user_id = '" . mysqli_real_escape_string($conn, $user_id) . "'
        ORDER BY t.tanggal DESC";
$riwayat = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pembelian</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #121212; margin: 0; padding: 20px; color: #eee; }
        .card { background: #333; padding: 24px; border-radius: 14px; box-shadow: 0 6px 20px rgba(0,0,0,0.08); max-width: 1000px; margin: 0 auto; color: #eee }
        h2 { margin: 0 0 20px; font-size: 22px; color: #eee; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: center; }
        th { background: #333; color: #eee; }
        tr:hover { background: #121212; transition: .2s; }
        .status { padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; }
        .done { background: #28a745; color: #fff; }
        .pending { background: #ffc107; color: #fff; }
        .cancel { background: #dc3545; color: #fff; }
        .btn-detail { display: inline-block; padding: 8px 14px; border: 2px solid #007bff; border-radius: 6px; background: transparent; background-color: #007bff; font-size: 14px; font-weight: 500; text-decoration: none; transition: all 0.3s ease; cursor: pointer;}
        .btn-detail i { margin-right: 5px;}
        .btn-detail:hover { background: #007bff; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,123,255,0.3);}
    </style>
</head>
<body>

<div class="card">
    <h2><i class="fas fa-history"></i> Riwayat Pembelian</h2>

    <table>
        <tr>
            <th>ID Transaksi</th>
            <th>Tanggal</th>
            <th>Total Harga</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php if (mysqli_num_rows($riwayat) > 0): ?>
            <?php while($r = mysqli_fetch_assoc($riwayat)): ?>
            <tr>
                <td><?= htmlspecialchars($r['id']) ?></td>
                <td><?= date('d-m-Y H:i', strtotime($r['tanggal'])) ?></td>
                <td>Rp <?= number_format($r['total'], 0, ',', '.') ?></td>
                <td>
                    <?php
                        $statusClass = 'pending';
                        if ($r['status'] === 'Selesai') $statusClass = 'done';
                        elseif ($r['status'] === 'Batal') $statusClass = 'cancel';
                    ?>
                    <span class="status <?= $statusClass ?>"><?= htmlspecialchars($r['status']) ?></span>
                </td>
                <td><a href="beranda.php?page=detail&id=<?= $r['id'] ?>" class="btn-detail"><i class="fas fa-info-circle"></i> Detail</a></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Belum ada riwayat pembelian.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
