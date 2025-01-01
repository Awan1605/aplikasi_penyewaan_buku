<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "penyewaan_buku"; // Nama Database

// Melakukan koneksi ke db
$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    die("Gagal konek: " . mysqli_connect_error()); // mysqli_connect_error()
}
?>