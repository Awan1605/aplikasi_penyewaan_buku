<?php
//file koneksi database
include 'koneksi2.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Validasi data dari $_POST sebelum digunakan
  $penerbit = ($_POST['penerbit']) ? mysqli_real_escape_string($koneksi, $_POST['penerbit']) : null;

  $targetDir = "uploads/";
  $fileName = basename($_FILES["sampul"]["name"]);
  $targetFilePath = $targetDir . $fileName;

  // Validasi tipe file
  $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
  $allowedTypes = ["jpg", "jpeg", "png", "gif"];
  $judul = ($_POST['judul']) ? mysqli_real_escape_string($koneksi, $_POST['judul']) : null;
  $deskripsi = ($_POST['deskripsi']) ? mysqli_real_escape_string($koneksi, $_POST['deskripsi']) : null;
  $harga = ($_POST['harga']) && is_numeric($_POST['harga']) ? $_POST['harga'] : null;
  $banyak_buku = ($_POST['banyak_buku']) && is_numeric($_POST['banyak_buku']) ? $_POST['banyak_buku'] : null;

  // Cek apakah direktori tujuan sudah ada, jika belum buat
  if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
  }

  if (in_array($fileType, $allowedTypes)) {
    // Pindahkan file yang diunggah ke direktori tujuan
    if (move_uploaded_file($_FILES["sampul"]["tmp_name"], $targetFilePath)) {
      // Simpan informasi file ke dalam database
      if ($fileName && $judul && $deskripsi && $harga !== null && $banyak_buku !== null && $penerbit) {
        // Status buku setel menjadi 'Tersedia' saat pertama kali ditambahkan
        $status = 'Tersedia';

        // Query untuk memasukkan data ke dalam database
        $query = "INSERT INTO kelola_data_buku (sampul, judul, deskripsi, harga, banyak_buku, penerbit, status) 
                  VALUES ('$targetFilePath', '$judul', '$deskripsi', $harga, $banyak_buku, '$penerbit', '$status')";

        // Eksekusi query
        if (mysqli_query($koneksi, $query)) {
          echo "<script>
                alert('Data Berhasil Disimpan');
                window.location.href = 'tambah_data_buku.php';
            </script>";
        } else {
          // Tampilkan error jika query gagal
          echo "<script>
                alert('Gagal Menyimpan Data: " . mysqli_error($koneksi) . "' );
                window.location.href = 'tambah_data_buku.php';
            </script>";
        }
      } else {
        // Jika ada data yang kosong, akan menampilkan pesan kesalahan
        echo "<script>
            alert('Pastikan semua data telah diisi dengan benar!');
            window.location.href = 'tambah_data_buku.php';
        </script>";
      }
    } else {
      echo "Terjadi kesalahan saat mengunggah file.";
    }
  } else {
    echo "Tipe file tidak diizinkan. Harap unggah file dengan tipe: " . implode(", ", $allowedTypes);
  }
}
?>