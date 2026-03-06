<?php
include '../db.php';

// Pastikan hanya kasir atau admin yang bisa mengakses
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['kasir', 'admin'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil ID transaksi dari URL
$id = intval($_GET['id'] ?? 0);

// Ambil data transaksi dari transaksi_online
$query = mysqli_query($conn, "SELECT * FROM transaksi_online WHERE id = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan');window.location='riwayat-transaksi.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi Online</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: #f5f5f5;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 80%;
            margin: 30px auto;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .detail-box {
            margin: 20px 0;
            border: 2px solid #1db954;
            border-radius: 8px;
            padding: 15px;
            background: #1db954;
        }

        .detail-box p {
            margin: 8px 0;
            font-size: 16px;
        }

        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #1db954;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #d1fae5;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detail Transaksi Online</h2>
        <div class="detail-box">
            <p><strong>ID Transaksi:</strong> <?= $data['id']; ?></p>
            <p><strong>Nama Penerima:</strong> <?= htmlspecialchars($data['nama_penerima']); ?></p>
            <p><strong>Tanggal:</strong> <?= $data['tanggal']; ?></p>
            <p><strong>Alamat Pengiriman:</strong> <?= htmlspecialchars($data['alamat_pengiriman']); ?></p>
            <p><strong>Metode Pembayaran:</strong> <?= $data['metode_pembayaran']; ?></p>
            <p><strong>Total:</strong> Rp <?= number_format($data['total'], 0, ',', '.'); ?></p>
            <p><strong>Status:</strong> <?= ucfirst($data['status']); ?></p>
        </div>
        <a href="beranda.php?page=riwayat" class="btn-back">Kembali</a>
    </div>
</body>
</html>
