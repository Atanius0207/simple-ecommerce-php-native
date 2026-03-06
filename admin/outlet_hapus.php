<?php
include '../db.php';
session_start();

if (!isset($_GET['id'])) {
    echo "<script>alert('ID Outlet tidak ditemukan'); window.location='beranda.php?page=outlet';</script>";
    exit;
}

$id = intval($_GET['id']);
mysqli_query($conn, "DELETE FROM outlet WHERE id=$id");

echo "<script>alert('Outlet berhasil dihapus'); window.location='beranda.php?page=outlet';</script>";
exit;
