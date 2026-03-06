<?php
include '../db.php';

// Ambil data produk
$produk_q = mysqli_query($conn, "SELECT id, nama, harga, stok FROM produk ORDER BY nama ASC");

// Ambil total penjualan
$penjualan_q = mysqli_query($conn, "SELECT SUM(total) as total_penjualan FROM transaksi");
$penjualan_data = mysqli_fetch_assoc($penjualan_q);
$total_penjualan = $penjualan_data['total_penjualan'] ?? 0;

// Ambil total pembelian
$pembelian_q = mysqli_query($conn, "SELECT SUM(total) as total_pembelian FROM pembelian");
$pembelian_data = mysqli_fetch_assoc($pembelian_q);
$total_pembelian = $pembelian_data['total_pembelian'] ?? 0;

// Hitung laba bersih
$laba_bersih = $total_penjualan - $total_pembelian;

// Proses pembelian
if (isset($_POST['simpan'])) {
    $supplier = mysqli_real_escape_string($conn, $_POST['supplier']);
    $total = 0;

    // Simpan pembelian utama
    mysqli_query($conn, "INSERT INTO pembelian (tanggal, supplier, total) VALUES (NOW(), '$supplier', 0)");
    $pembelian_id = mysqli_insert_id($conn);

    foreach ($_POST['produk'] as $id_produk => $val) {
        $qty = intval($_POST['qty'][$id_produk]);
        $harga = intval($_POST['harga'][$id_produk]);
        if ($qty > 0) {
            $subtotal = $harga * $qty;
            $total += $subtotal;

            // Simpan detail
            mysqli_query($conn, "INSERT INTO pembelian_detail (pembelian_id, produk_id, qty, harga, subtotal) 
                                 VALUES ($pembelian_id, $id_produk, $qty, $harga, $subtotal)");

            // Update stok produk
            mysqli_query($conn, "UPDATE produk SET stok = stok + $qty WHERE id=$id_produk");
        }
    }

    // Update total pembelian
    mysqli_query($conn, "UPDATE pembelian SET total=$total WHERE id=$pembelian_id");

    echo "<script>alert('Pembelian berhasil disimpan'); window.location='beranda.php?page=pembelian';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembelian Barang</title>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
  body { background:#f4f6f9; color:#333; font-family:'Segoe UI', sans-serif; }
  h2 { margin-bottom:20px; display:flex; align-items:center; gap:10px; color:#28a745; }
  .card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.08); margin-bottom:20px; }
  .form-group { margin-bottom:15px; }
  label { display:block; font-weight:600; margin-bottom:6px; }
  input[type=text], input[type=number] { width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; transition:.2s; }
  input:focus { border-color:#28a745; box-shadow:0 0 8px rgba(40,167,69,0.3); outline:none; }
  table { width:100%; border-collapse:collapse; margin-top:10px; }
  th,td { padding:12px; border-bottom:1px solid #eee; text-align:left; }
  th { background:#28a745; color:#fff; }
  tr:hover { background:#f1fdf4; transition:.3s; }
  .btn { background:#28a745; border:none; padding:10px 18px; border-radius:8px; color:#fff; font-weight:600; cursor:pointer; transition:.2s; display:inline-flex; align-items:center; gap:8px; margin-top:10px; }
  .btn:disabled { background:#ccc; cursor:not-allowed; }
  .btn:hover:enabled { background:#218838; transform:translateY(-2px); box-shadow:0 6px 15px rgba(0,0,0,0.15); }
  .summary { margin-top:20px; text-align:right; font-size:20px; font-weight:700; color:#28a745; }
  .warning { color:#dc3545; font-weight:600; margin-top:10px; display:none; }
  /* Animasi angka */
  .animate-up { animation: bounceUp 0.3s ease; }
  @keyframes bounceUp {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); color: #f1c40f; }
    100% { transform: scale(1); color:#28a745; }
  }
  .animate-down { animation: fadeDown 0.3s ease; }
  @keyframes fadeDown {
    0% { transform: translateY(-5px); opacity: 0.5; }
    100% { transform: translateY(0); opacity: 1; }
  }
</style>
</head>
<body>

<div class="card">
  <h2><i class="fas fa-truck-loading"></i> Tambah Pembelian Barang</h2>
  <div class="summary success">
    <i class="fas fa-wallet"></i> Laba Bersih: Rp <span id="uang-tersedia"><?= number_format($laba_bersih,0,',','.') ?></span>
  </div>
  <form method="post">
    <div class="form-group">
      <label><i class="fas fa-user-tag"></i> Supplier</label>
      <input type="text" name="supplier" required>
    </div>

    <table>
      <tr>
        <th><i class="fas fa-check-square"></i> Pilih</th>
        <th><i class="fas fa-box"></i> Produk</th>
        <th><i class="fas fa-tags"></i> Harga Beli</th>
        <th><i class="fas fa-sort-numeric-up"></i> Qty</th>
        <th><i class="fas fa-coins"></i> Subtotal</th>
      </tr>
      <?php while($p = mysqli_fetch_assoc($produk_q)) { ?>
      <tr>
        <td><input type="checkbox" class="pilih-produk" name="produk[<?= $p['id'] ?>]" value="<?= $p['id'] ?>"></td>
        <td><i class="fas fa-box-open"></i> <?= htmlspecialchars($p['nama']) ?> (Stok: <?= $p['stok'] ?>)</td>
        <td><input type="number" class="harga" name="harga[<?= $p['id'] ?>]" value="<?= $p['harga'] ?>" min="0" disabled></td>
        <td><input type="number" class="qty" name="qty[<?= $p['id'] ?>]" value="0" min="0"></td>
        <td class="subtotal">0</td>
      </tr>
      <?php } ?>
    </table>

    <div class="summary">
      Total: Rp <span id="total">0</span>
    </div>
    <div id="warning-msg" class="warning"><i class="fas fa-exclamation-triangle"></i> Total melebihi laba bersih!</div>

    <button type="submit" id="btnSimpan" name="simpan" class="btn"><i class="fas fa-save"></i> Simpan Pembelian</button>
  </form>
</div>

<script>
let lastTotal = 0;
const labaBersih = <?= $laba_bersih ?>;
const totalEl = document.getElementById("total");
const btnSimpan = document.getElementById("btnSimpan");
const warningMsg = document.getElementById("warning-msg");

// Fungsi animasi angka bertambah
function animateValue(element, start, end, duration) {
    let startTime = null;
    function animation(currentTime) {
        if (!startTime) startTime = currentTime;
        const progress = Math.min((currentTime - startTime) / duration, 1);
        const value = Math.floor(progress * (end - start) + start);
        element.innerText = value.toLocaleString();
        if (progress < 1) requestAnimationFrame(animation);
    }
    requestAnimationFrame(animation);
}

function hitungTotal() {
    let total = 0;
    document.querySelectorAll("tr").forEach(function(row) {
        let harga = row.querySelector(".harga") ? parseInt(row.querySelector(".harga").value || 0) : 0;
        let qty = row.querySelector(".qty") ? parseInt(row.querySelector(".qty").value || 0) : 0;
        let checkbox = row.querySelector(".pilih-produk");
        let subtotalCell = row.querySelector(".subtotal");

        if (checkbox && checkbox.checked && subtotalCell) {
            let subtotal = harga * qty;
            subtotalCell.innerText = subtotal.toLocaleString();
            total += subtotal;
        } else if (subtotalCell) {
            subtotalCell.innerText = "0";
        }
    });

    if (total > lastTotal) {
        totalEl.classList.add("animate-up");
        setTimeout(() => totalEl.classList.remove("animate-up"), 300);
        animateValue(totalEl, lastTotal, total, 400);
    } else if (total < lastTotal) {
        totalEl.classList.add("animate-down");
        setTimeout(() => totalEl.classList.remove("animate-down"), 300);
        totalEl.innerText = total.toLocaleString();
    } else {
        totalEl.innerText = total.toLocaleString();
    }

    if (total > labaBersih) {
        btnSimpan.disabled = true;
        warningMsg.style.display = "block";
    } else {
        btnSimpan.disabled = false;
        warningMsg.style.display = "none";
    }

    lastTotal = total;
}

document.querySelectorAll(".harga, .qty, .pilih-produk").forEach(function(input) {
    input.addEventListener("input", hitungTotal);
    input.addEventListener("change", hitungTotal);
});
</script>
</body>
</html>