<?php
include '../db.php'; // sesuaikan path koneksi

// Cek apakah user sudah login & role admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil total pendapatan semua outlet
$sql_total = "SELECT SUM(total) AS total 
              FROM transaksi 
              WHERE status = 'lunas'";
$result_total = mysqli_query($conn, $sql_total);
$data_total = mysqli_fetch_assoc($result_total);
$total_pendapatan = $data_total['total'] ?? 0;

// Ambil pendapatan per outlet
$sql_outlet = "SELECT o.nama_outlet, COALESCE(SUM(t.total),0) AS total 
               FROM outlet o
               LEFT JOIN transaksi t ON t.outlet_id = o.id AND t.status = 'lunas'
               GROUP BY o.id, o.nama_outlet
               ORDER BY o.nama_outlet ASC";
$result_outlet = mysqli_query($conn, $sql_outlet);

$outlet_data = [];
while ($row = mysqli_fetch_assoc($result_outlet)) {
    $outlet_data[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ShopNeo | Dashboard Admin</title>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
    :root {
        --green:#27ae60;
        --green-dark:#1e8449;
        --bg:#f5f6fa;
        --card:#ffffff;
        --muted:#6b7280;
    }
    body { font-family:'Segoe UI',Arial,sans-serif; margin:0; padding:0; background:var(--bg); }
    .dashboard-container { max-width:960px; margin:30px auto; padding:20px; background:var(--card); border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.06); }
    h1 { text-align:center; margin-bottom:20px; font-size:28px; font-weight:700; color:#2c3e50; }
    .card { background:linear-gradient(120deg,var(--green),var(--green-dark)); color:#fff; padding:20px; border-radius:12px; margin-bottom:25px; text-align:center; box-shadow:0 6px 15px rgba(0,0,0,0.12); transition: transform .2s; }
    .card:hover { transform: translateY(-4px); }
    .card h2 { margin:0; font-size:22px; font-weight:600; display:flex; align-items:center; justify-content:center; gap:10px; }
    .card p { font-size:26px; font-weight:800; margin:8px 0 0; }
    table { width:100%; border-collapse:collapse; margin-top:10px; font-size:15px; }
    table th, table td { padding:12px 14px; text-align:center; color: #333 }
    table th { background:var(--green); color:#fff; font-weight:700; }
    table tr { background:#fff; transition:background .2s, transform .15s; }
    table tr:nth-child(even) { background:#fafafa; }
    table tr:hover { background:#f6fff6; transform:translateY(-2px); box-shadow:0 4px 12px rgba(0,0,0,0.05); }
    .table-icon { color:var(--green); margin-right:6px; }
</style>
</head>
<body>
    <div class="dashboard-container">
        <h1><i class="fas fa-chart-line"></i> Dashboard Admin</h1>

        <!-- Total Pendapatan -->
        <div class="card">
            <h2><i class="fas fa-money-bill-wave"></i> Total Pendapatan</h2>
            <p>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></p>
        </div>

        <!-- Tabel Pendapatan Per Outlet -->
        <h2 style="margin-bottom:12px;"><i class="fas fa-store"></i> Pendapatan per Outlet</h2>
        <table>
            <tr>
                <th><i class="fas fa-store-alt"></i> Outlet</th>
                <th><i class="fas fa-coins"></i> Total Pendapatan</th>
            </tr>
            <?php if (!empty($outlet_data)): ?>
                <?php foreach ($outlet_data as $outlet): ?>
                <tr>
                    <td><i class="fas fa-store table-icon"></i> <?= htmlspecialchars($outlet['nama_outlet']) ?></td>
                    <td><i class="fas fa-money-check-alt table-icon"></i> Rp <?= number_format($outlet['total'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2"><i class="fas fa-exclamation-circle"></i> Belum ada data transaksi.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
