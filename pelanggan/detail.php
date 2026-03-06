<?php
include '../db.php';

// Pastikan hanya pelanggan yang bisa akses
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pelanggan') {
    header("Location: ../login.php");
    exit;
}

$transaksi_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil detail transaksi
$sql = "SELECT nama_produk, qty, harga, subtotal 
        FROM transaksi_pelanggan_detail
        WHERE transaksi_id = $transaksi_id";
$detail = mysqli_query($conn, $sql);

// Ambil total harga
$total = 0;
if (mysqli_num_rows($detail) > 0) {
    while($row = mysqli_fetch_assoc($detail)) {
        $total += $row['subtotal'];
        $data[] = $row;
    }
} else {
    $data = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Transaksi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="detail.css">
</head>
<body>

<div class="card">
    <h2><i class="fas fa-receipt"></i> Detail Transaksi #<?= $transaksi_id ?></h2>

    <table>
        <tr>
            <th>Nama Produk</th>
            <th>Qty</th>
            <th>Harga Satuan</th>
            <th>Subtotal</th>
        </tr>
        <?php if (!empty($data)): ?>
            <?php foreach($data as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['nama_produk']) ?></td>
                <td><?= $d['qty'] ?></td>
                <td>Rp <?= number_format($d['harga'], 0, ',', '.') ?></td>
                <td>Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td>Rp <?= number_format($total, 0, ',', '.') ?></td>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="4">Tidak ada detail transaksi.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
