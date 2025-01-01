<?php
// include database connection file
include 'koneksi2.php';

$sampul = $_GET['sampul'];

// Query untuk menghapus data berdasarkan kolom sampul
$query = "DELETE FROM kelola_data_buku WHERE sampul='$sampul'";
$result = mysqli_query($koneksi, $query);

// Cek apakah query berhasil dijalankan
if ($result) {
    echo "<script>
        alert('Data Berhasil Dihapus');
        window.location.href = 'tambah_data_buku.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal Menghapus Data: " . mysqli_error($koneksi) . "');
        window.location.href = 'tambah_data_buku.php';
    </script>";
}

// Tambahkan exit untuk memastikan pengalihan berhenti eksekusi script
exit;

?>