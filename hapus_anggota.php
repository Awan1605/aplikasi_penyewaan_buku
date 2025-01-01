<?php
// Include file koneksi ke database
include 'koneksi2.php';

// Ambil nilai NIM dari URL (menggunakan metode GET)
$nama = $_GET['nama'];

// Query untuk menghapus data mahasiswa berdasarkan NIM
$result = mysqli_query($koneksi, "DELETE FROM users WHERE nama='$nama'");

// Redirect ke halaman mahasiswa setelah penghapusan
header("Location: data_anggota.php");
?>