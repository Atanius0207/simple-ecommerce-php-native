<?php
// Tandai menu aktif berdasarkan parameter ?page=
$currentPage = $_GET['page'] ?? '';
?>
<div class="sidebar" id="sidebar">
    <ul class="sidebar-menu">
      <li><a href="beranda.php?page=keranjang" class="<?= ($currentPage=='keranjang')?'active':'' ?>"><i class="fas fa-shopping-cart"></i> Keranjang</a></li>
      <li><a href="beranda.php?page=checkout" class="<?= ($currentPage=='checkout')?'active':'' ?>"><i class="fas fa-credit-card"></i> Checkout</a></li>
      <li><a href="beranda.php?page=riwayat" class="<?= ($currentPage=='riwayat')?'active':'' ?>"><i class="fas fa-history"></i> Riwayat Transaksi</a></li>
      <li><a href="index.php" class="logout"><i class="fas fa-redo"></i> Kembali</a></li>
    </ul>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
/* CSS sidebar (dari yang Anda kirim) */
*{margin:0;padding:0;box-sizing:border-box}
body{display:flex;font-family:'Poppins',sans-serif;background:#121212;color:#fff;line-height:1.6;}
.sidebar{width:250px;background:linear-gradient(180deg,#191414,#0d0d0d);height:100vh;position:fixed;top:0;left:0;display:flex;flex-direction:column;padding-top:20px;box-shadow:2px 0 10px rgba(0,0,0,0.5);transition:.3s;}
.sidebar-logo{text-align:center;margin-bottom:40px;}
.sidebar-logo h2{color:#1db954;font-size:26px;letter-spacing:1px;font-weight:600;}
.sidebar-menu{list-style:none;padding:0;flex:1;}
.sidebar-menu li{margin:10px 0;}
.sidebar-menu li a{display:flex;align-items:center;text-decoration:none;color:#ccc;padding:12px 20px;transition:.3s;border-radius:8px;font-weight:500;}
.sidebar-menu li a i{margin-right:12px;font-size:18px;width:20px;text-align:center;}
.sidebar-menu li a:hover,.sidebar-menu li a.active{background:rgba(29,185,84,.2);color:#1db954;transform:translateX(5px);}
.logout{!important;}
.logout:hover{background:rgba(255,77,77,.1); !important;}
.main-content{margin-left:250px;padding:20px;flex:1;transition:margin-left .3s;}
@media(max-width:900px){.sidebar{width:200px}.main-content{margin-left:200px}}
@media(max-width:768px){.sidebar{left:-250px}.sidebar.active{left:0}.main-content{margin-left:0;width:100%}}
</style>
