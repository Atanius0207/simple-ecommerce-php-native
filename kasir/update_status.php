<?php
include '../db.php';

if(isset($_POST['id']) && isset($_POST['status'])){
    $id = intval($_POST['id']);
    $status = $_POST['status'];

    $sql = "UPDATE transaksi_pelanggan SET status='$status' WHERE id=$id";
    if(mysqli_query($conn, $sql)){
        header("Location: beranda.php?page=transaksi");
    } else {
        echo "Gagal update status!";
    }
}
?>
