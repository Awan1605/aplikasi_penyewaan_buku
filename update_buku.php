<?php
// Include database connection file
include 'koneksi2.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi dan sanitasi input
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $judul = isset($_POST['judul']) ? mysqli_real_escape_string($koneksi, $_POST['judul']) : null;
    $deskripsi = isset($_POST['deskripsi']) ? mysqli_real_escape_string($koneksi, $_POST['deskripsi']) : null;
    $harga = isset($_POST['harga']) && is_numeric($_POST['harga']) ? intval($_POST['harga']) : null;
    $banyak_buku = isset($_POST['banyak_buku']) && is_numeric($_POST['banyak_buku']) ? intval($_POST['banyak_buku']) : null;
    $penerbit = isset($_POST['penerbit']) ? mysqli_real_escape_string($koneksi, $_POST['penerbit']) : null;

    $targetDir = "uploads/";
    $fileName = basename($_FILES["sampul"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ["jpg", "jpeg", "png", "gif"];

    // Cek apakah direktori tujuan ada
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Ambil file sampul saat ini
    $query_get_current_file = "SELECT sampul FROM kelola_data_buku WHERE id = $id";
    $result = mysqli_query($koneksi, $query_get_current_file);
    $currentFile = mysqli_fetch_assoc($result)['sampul'];

    // Cek apakah file baru diunggah
    if (!empty($fileName)) {
        // Validasi tipe file
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["sampul"]["tmp_name"], $targetFilePath)) {
                $newFilePath = $targetFilePath;
            } else {
                echo "<script>
                    alert('Terjadi kesalahan saat mengunggah file.');
                    window.history.back();
                </script>";
                exit;
            }
        } else {
            echo "<script>
                alert('Tipe file tidak diizinkan. Harap unggah file dengan tipe: " . implode(", ", $allowedTypes) . "');
                window.history.back();
            </script>";
            exit;
        }
    } else {
        // Gunakan file sampul yang ada jika tidak ada file baru
        $newFilePath = $currentFile;
    }

    // Validasi apakah semua data diisi
    if ($judul && $deskripsi && $harga !== null && $banyak_buku !== null && $penerbit) {
        // Query untuk update data
        $query = "UPDATE kelola_data_buku SET 
                  sampul = '$newFilePath',
                  judul = '$judul',
                  deskripsi = '$deskripsi',
                  harga = $harga,
                  banyak_buku = $banyak_buku,
                  penerbit = '$penerbit'
                  WHERE id = $id";

        // Eksekusi query
        if (mysqli_query($koneksi, $query)) {
            echo "<script>
                alert('Data Berhasil Diperbarui');
                window.location.href = 'tambah_data_buku.php';
            </script>";
        } else {
            echo "<script>
                alert('Gagal Memperbarui Data: " . mysqli_error($koneksi) . "');
                window.location.href = 'tambah_data_buku.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('Pastikan semua data telah diisi dengan benar!');
            window.history.back();
        </script>";
    }
}
?>