<?php
// Include database connection file
include 'koneksi2.php';

// Ambil data dari formulir (dianggap menggunakan metode POST)
$nama = $_POST['nama'];
$alamat = $_POST['alamat'];
$nohp = $_POST['nohp'];
$role = $_POST['role'];

// Query untuk memperbarui data mahasiswa
$result = mysqli_query($koneksi, "UPDATE users SET role='$role', alamat='$alamat', nohp='$nohp' WHERE nama='$nama'");

if ($result) {
    // Redirect ke halaman utama setelah pembaruan dengan pesan sukses
    echo "<script>
            alert('Data berhasil diperbarui');
            window.location.href='data_anggota.php';
          </script>";
} else {
    // Tampilkan pesan kesalahan jika pembaruan gagal
    echo "<script>
            alert('Data gagal diperbarui');
            window.location.href='data_anggota.php';
          </script>";
}
?>