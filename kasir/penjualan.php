<?php
include '../db.php';

$outlet_id = $_SESSION['user']['outlet_id'] ?? null;

// kalau kasir tapi tidak ada outlet → blokir
if ($_SESSION['user']['role'] === 'kasir' && !$outlet_id) {
    die("Outlet kasir belum diatur. Hubungi admin.");
}

$outlet_id = $_SESSION['user']['outlet_id']; // outlet otomatis dari kasir login

// Ambil nama outlet
$outlet = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_outlet FROM outlet WHERE id=$outlet_id"));

// Ambil daftar produk
$produk_q = mysqli_query($conn, "SELECT id, nama, harga, stok FROM produk ORDER BY nama ASC");

// Proses transaksi
$errors = '';
if (isset($_POST['simpan'])) {
    $selected = $_POST['produk'] ?? [];
    $qtys     = $_POST['qty'] ?? [];

    if (empty($selected)) {
        $errors = "Pilih minimal satu produk untuk disimpan.";
    } else {
        $tanggal = date('Y-m-d');
        $status  = 'lunas';
        $total   = 0;
        $items   = [];

        foreach ($selected as $prodId => $val) {
            $prodId = intval($prodId);
            $qty    = intval($qtys[$prodId] ?? 1);
            if ($qty < 1) $qty = 1;

            $res = mysqli_query($conn, "SELECT nama, harga, stok FROM produk WHERE id=$prodId LIMIT 1");
            if (!$res || mysqli_num_rows($res) == 0) continue;
            $row = mysqli_fetch_assoc($res);

            if ($row['stok'] < $qty) {
                $errors = "Stok produk {$row['nama']} tidak mencukupi!";
                break;
            }

            $harga    = $row['harga'];
            $subtotal = $harga * $qty;
            $total   += $subtotal;

            $items[] = [
                'produk_id' => $prodId,
                'qty'       => $qty,
                'harga'     => $harga,
                'subtotal'  => $subtotal
            ];
        }

        if ($errors == '' && count($items) > 0) {
            $sql = "INSERT INTO transaksi (outlet_id, total, tanggal, status) 
                    VALUES ($outlet_id, $total, '$tanggal', '$status')";
            if (mysqli_query($conn, $sql)) {
                $trans_id = mysqli_insert_id($conn);

                foreach ($items as $it) {
                    $pid      = $it['produk_id'];
                    $qty      = $it['qty'];
                    $harga    = $it['harga'];
                    $subtotal = $it['subtotal'];

                    mysqli_query($conn, "INSERT INTO transaksi_detail (transaksi_id, produk_id, qty, harga, subtotal)
                                         VALUES ($trans_id, $pid, $qty, $harga, $subtotal)");

                    mysqli_query($conn, "UPDATE produk SET stok = stok - $qty WHERE id=$pid");
                }

                $nama_outlet = addslashes($outlet['nama_outlet']);
                echo "<script>alert('Transaksi berhasil disimpan untuk outlet $nama_outlet'); window.location='beranda.php?page=penjualan';</script>";
            } else {
                $errors = "Gagal menyimpan transaksi: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kasir - Penjualan</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
  :root { --green:#28a745; --green-dark:#218838; --bg:#f4f6f9; --card:#fff; }
  body { font-family:'Segoe UI',sans-serif; background:var(--bg); margin:0; padding:20px; }
  .card { background:var(--card); padding:24px; border-radius:14px; box-shadow:0 6px 20px rgba(0,0,0,0.08); max-width:1100px; margin:0 auto; }
  h2 { margin:0 0 20px; font-size:22px; color:#2c3e50; }
  .info-outlet { margin-bottom:16px; font-weight:600; color:#555; }
  table { width:100%; border-collapse:collapse; margin-top:10px; font-size:14px; }
  th,td { padding:12px; border-bottom:1px solid #eee; text-align:left; color: #333 }
  th { background:var(--green); color:#fff; }
  tr:hover { background:#f1fdf4; transition:.2s; }
  input[type=number] { width:80px; padding:6px; border:1px solid #ccc; border-radius:6px; text-align:center; }
  input[type=number]:focus { border-color:var(--green); outline:none; }
  .btn { background:var(--green); color:#fff; border:none; padding:10px 18px; border-radius:8px; font-weight:600; cursor:pointer; margin-top:14px; }
  .btn:hover { background:var(--green-dark); transform:translateY(-2px); }
  .error { color:#b91c1c; margin-bottom:10px; font-weight:600; }
  .summary { margin-top:20px; padding:14px; border-radius:10px; background:#e6f9ed; font-weight:700; color:#065f46; display:flex; align-items:center; justify-content:space-between; }
  .summary i { margin-right:8px; }
</style>
</head>
<body>

<div class="card">
  <h2><i class="fas fa-cash-register"></i> Penjualan Kasir</h2>
  <div class="info-outlet"><i class="fas fa-store"></i> Outlet: <?= htmlspecialchars($outlet['nama_outlet']) ?></div>

  <?php if($errors): ?>
    <div class="error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors) ?></div>
  <?php endif; ?>

  <form method="post" id="formKasir">
    <table>
      <tr>
        <th>Pilih</th>
        <th>Produk</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Qty</th>
        <th>Subtotal</th>
      </tr>
      <?php while($p = mysqli_fetch_assoc($produk_q)): ?>
      <tr data-id="<?= $p['id'] ?>">
        <td><input type="checkbox" name="produk[<?= $p['id'] ?>]" value="<?= $p['id'] ?>" class="chk" data-price="<?= $p['harga'] ?>"></td>
        <td><?= htmlspecialchars($p['nama']) ?></td>
        <td>Rp <?= number_format($p['harga'],0,',','.') ?></td>
        <td><?= $p['stok'] ?></td>
        <td><input type="number" name="qty[<?= $p['id'] ?>]" value="1" min="1" class="qty" data-id="<?= $p['id'] ?>"></td>
        <td class="subtotal">Rp 0</td>
      </tr>
      <?php endwhile; ?>
    </table>

    <div class="summary">
      <span><i class="fas fa-coins"></i> Total Belanja:</span>
      <span id="totalValue">Rp 0</span>
    </div>

    <button type="submit" name="simpan" class="btn"><i class="fas fa-save"></i> Simpan Transaksi</button>
  </form>
</div>

<script>
  const fmt = new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0});

  function recalcTotal(){
    let total = 0;
    document.querySelectorAll('tr[data-id]').forEach(row => {
      const cb = row.querySelector('.chk');
      const qtyInput = row.querySelector('.qty');
      const subtotalCell = row.querySelector('.subtotal');
      if (cb.checked) {
        const price = parseFloat(cb.dataset.price);
        const qty = Math.max(1, parseInt(qtyInput.value) || 1);
        const subtotal = price * qty;
        subtotalCell.textContent = fmt.format(subtotal);
        total += subtotal;
      } else {
        subtotalCell.textContent = "Rp 0";
      }
    });
    document.getElementById('totalValue').textContent = fmt.format(total);
  }

  document.querySelectorAll('.chk').forEach(cb => cb.addEventListener('change', recalcTotal));
  document.querySelectorAll('.qty').forEach(qty => qty.addEventListener('input', recalcTotal));

  recalcTotal();
</script>

</body>
</html>
