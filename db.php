<?php
 $servername = "localhost";
 $username = "root";
 $password = "";
 $dbname = "shopneo";

 $conn = mysqli_connect($servername, $username, $password, $dbname);

 if(!$conn) {
    die("Koneksi Dengan Databse Gagal: ". mysqli_connect_error());
 }
?>