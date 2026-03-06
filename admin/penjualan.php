<?php
include '../db.php';

$default_outlet_id = 1;

// Ambil daftar produk
$produk_q = mysqli_query($conn, "SELECT id, nama, harga, stok, gambar FROM produk ORDER BY nama ASC");

// Proses transaksi
$errors = '';
if (isset($_POST['simpan'])) {
    $selected = $_POST['produk'] ?? [];
    $qtys = $_POST['qty'] ?? [];

    if (empty($selected)) {
        $errors = "Pilih minimal satu produk untuk disimpan.";
    } else {
        $outlet_id = isset($_POST['outlet_id']) ? intval($_POST['outlet_id']) : $default_outlet_id;
        $tanggal = date('Y-m-d');
        $status = 'lunas';
        $total = 0;
        $items = [];

        foreach ($selected as $prodId => $val) {
            $prodId = intval($prodId);
            $qty = isset($qtys[$prodId]) ? intval($qtys[$prodId]) : 1;
            if ($qty < 1) $qty = 1;

            $res = mysqli_query($conn, "SELECT nama, harga, stok FROM produk WHERE id=$prodId LIMIT 1");
            if (!$res || mysqli_num_rows($res) == 0) continue;
            $row = mysqli_fetch_assoc($res);

            if ($row['stok'] < $qty) {
                $errors = "Stok produk {$row['nama']} tidak mencukupi!";
                break;
            }

            $subtotal = $row['harga'] * $qty;
            $total += $subtotal;

            $items[] = [
                'produk_id'=>$prodId,'qty'=>$qty,
                'harga'=>$row['harga'],'subtotal'=>$subtotal
            ];
        }

        if ($errors=='' && count($items)>0) {
            $sql = "INSERT INTO transaksi (outlet_id,total,tanggal,status) 
                    VALUES ($outlet_id,$total,'$tanggal','$status')";
            if (mysqli_query($conn,$sql)) {
                $trans_id = mysqli_insert_id($conn);
                foreach ($items as $it) {
                    mysqli_query($conn,"INSERT INTO transaksi_detail 
                        (transaksi_id,produk_id,qty,harga,subtotal)
                        VALUES ($trans_id,{$it['produk_id']},{$it['qty']},{$it['harga']},{$it['subtotal']})");
                    mysqli_query($conn,"UPDATE produk SET stok=stok-{$it['qty']} WHERE id={$it['produk_id']}");
                }
                echo "<script>alert('✅ Transaksi berhasil disimpan!');window.location='beranda.php?page=penjualan';</script>";
                exit;
            } else {
                $errors = "Gagal menyimpan transaksi: ".mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kasir - Penjualan</title>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
  :root{--green:#28a745;--green-dark:#218838;--bg:#f4f6f9;--card:#ffffff;--muted:#6b7280;}
  body{font-family:'Segoe UI',Roboto,Arial,sans-serif;background:var(--bg);margin:0;padding:28px;color:#1f2937;}
  .container{max-width:1100px;margin:0 auto;}
  .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;}
  h1{margin:0;font-size:26px;font-weight:700;display:flex;align-items:center;gap:10px;}
  .card{background:var(--card);border-radius:12px;padding:18px;box-shadow:0 8px 20px rgba(15,23,42,0.06);}
  .summary{display:flex;gap:16px;align-items:center;margin-bottom:16px;}
  .total-card{flex:1;padding:18px;border-radius:10px;background:linear-gradient(90deg,rgba(40,167,69,0.08),rgba(40,167,69,0.03));border:1px solid rgba(40,167,69,0.09);transition:transform .18s,box-shadow .18s;}
  .total-card:hover{transform:translateY(-4px);box-shadow:0 10px 25px rgba(16,185,129,0.06);}
  .total-label{color:var(--muted);font-weight:600;}
  .total-value{font-size:22px;font-weight:800;color:var(--green);margin-top:6px;}
  table.products{width:100%;border-collapse:collapse;margin-top:8px;font-size:14px;}
  table.products thead th{text-align:left;padding:12px 14px;background:var(--green);color:white;font-weight:700;}
  table.products tbody tr{background:#fff;transition:background .18s,transform .12s;}
  table.products tbody tr:hover{background:#f6fff6;transform:translateY(-3px);box-shadow:0 8px 18px rgba(16,185,129,0.04);}
  table.products td{padding:12px 14px;border-bottom:1px solid #f1f5f9;vertical-align:middle;}
  .product-name{font-weight:600;color:#0f172a;}
  .muted{color:var(--muted);font-size:13px;}
  input[type="number"]{width:84px;padding:8px 10px;border-radius:8px;border:1px solid #e6e9ee;}
  input[type="number"]:focus{outline:none;border-color:var(--green);box-shadow:0 4px 14px rgba(40,167,69,0.12);}
  .btn{background:var(--green);color:#fff;border:none;padding:12px 18px;border-radius:10px;cursor:pointer;font-weight:700;margin-top:14px;transition:transform .12s,box-shadow .12s;display:flex;align-items:center;gap:8px;}
  .btn:hover{transform:translateY(-3px);box-shadow:0 10px 22px rgba(16,185,129,0.12);}
  .subtotal-cell{font-weight:700;color:#0b6b2d;text-align:right;}
  .error{color:#b91c1c;margin-bottom:10px;font-weight:600;}
  select {font-family: "Font Awesome 6 Free", Arial; font-weight: 600;}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1><i class="fas fa-cash-register"></i> Kasir — Penjualan</h1>
    <div class="muted"><i class="fas fa-user-shield"></i> Admin Panel • Halaman Penjualan</div>
  </div>

  <div class="card">
    <?php if($errors): ?><div class="error"><i class="fas fa-triangle-exclamation"></i> <?= htmlspecialchars($errors) ?></div><?php endif; ?>

    <div class="summary">
      <div class="total-card">
        <div class="total-label"><i class="fas fa-receipt"></i> Total Belanja</div>
        <div id="totalValue" class="total-value">Rp 0</div>
      </div>
      <div style="width:220px;text-align:right;">
        <label class="muted" style="font-size:13px;"><i class="fas fa-store"></i> Outlet</label>
        <div>
          <select name="outlet_id" form="formKasir" style="padding:8px 10px;border-radius:8px;border:1px solid #e6e9ee;">
            <?php
              $outlet_q = mysqli_query($conn,"SELECT id,nama_outlet FROM outlet ORDER BY nama_outlet ASC");
              while($o=mysqli_fetch_assoc($outlet_q)){
                $sel=($o['id']==$default_outlet_id)?'selected':'';
                echo "<option value=\"{$o['id']}\" $sel>&#xf54e; ".htmlspecialchars($o['nama_outlet'])."</option>";
              }
            ?>
          </select>
        </div>
      </div>
    </div>

    <form id="formKasir" method="post">
      <input type="hidden" name="outlet_id" value="<?= $default_outlet_id ?>">
      <table class="products">
        <thead>
          <tr>
            <th><i class="fas fa-check-square"></i> Pilih</th>
            <th><i class="fas fa-box-open"></i> Produk</th>
            <th style="text-align:right;"><i class="fas fa-tags"></i> Harga</th>
            <th><i class="fas fa-sort-numeric-up"></i> Qty</th>
            <th style="text-align:right;"><i class="fas fa-calculator"></i> Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php while($p=mysqli_fetch_assoc($produk_q)): ?>
          <tr data-id="<?= $p['id'] ?>">
            <td><input type="checkbox" name="produk[<?= $p['id'] ?>]" value="<?= $p['id'] ?>" class="chk" data-price="<?= $p['harga'] ?>"></td>
            <td>
              <div class="product-name"><i class="fas fa-box"></i> <?= htmlspecialchars($p['nama']) ?></div>
              <div class="muted"><i class="fas fa-warehouse"></i> Stok: <?= $p['stok'] ?></div>
            </td>
            <td style="text-align:right;">Rp <?= number_format($p['harga'],0,',','.') ?></td>
            <td style="text-align:center;"><input type="number" name="qty[<?= $p['id'] ?>]" value="1" min="1" class="qty" data-id="<?= $p['id'] ?>"></td>
            <td class="subtotal-cell" id="st-<?= $p['id'] ?>">Rp 0</td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <button type="submit" name="simpan" class="btn"><i class="fas fa-shopping-basket"></i> Simpan Transaksi</button>
    </form>
  </div>
</div>

<script>
  const fmt=new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0});
  function recalcTotal(){
    let total=0; document.querySelectorAll('td[id^="st-"]').forEach(td=>td.textContent='Rp 0');
    document.querySelectorAll('.chk:checked').forEach(cb=>{
      const id=cb.value,price=parseFloat(cb.dataset.price)||0;
      const qty=document.querySelector('.qty[data-id="'+id+'"]');const q=qty?Math.max(1,parseInt(qty.value)||1):1;
      const st=price*q; total+=st;
      document.getElementById('st-'+id).textContent=fmt.format(Math.round(st));
    });
    document.getElementById('totalValue').textContent=fmt.format(Math.round(total));
  }
  document.querySelectorAll('.chk').forEach(cb=>cb.addEventListener('change',recalcTotal));
  document.querySelectorAll('.qty').forEach(q=>q.addEventListener('input',()=>{const id=q.dataset.id,cb=document.querySelector('.chk[value="'+id+'"]');if(cb&&cb.checked)recalcTotal();}));
  recalcTotal();
  document.getElementById('formKasir').addEventListener('submit',e=>{if(document.querySelectorAll('.chk:checked').length===0){e.preventDefault();alert('Pilih minimal satu produk.');}});
</script>
</body>
</html>
