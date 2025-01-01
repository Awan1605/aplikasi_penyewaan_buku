<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi2.php';

// Validasi input POST
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Gantilah ke FILTER_SANITIZE_FULL_SPECIAL_CHARS

if (!$id || !$action) {
    echo 'Data tidak lengkap.';
    exit;  // Memastikan tidak ada output lainnya setelah ini
}

try {
    if ($action === 'kembali') {
        // Dapatkan ID buku sebelum memindahkan data
        $getBookIdQuery = "SELECT id_buku FROM status_sewa WHERE id = ?";
        $bookStmt = mysqli_prepare($koneksi, $getBookIdQuery);
        mysqli_stmt_bind_param($bookStmt, 'i', $id);
        mysqli_stmt_execute($bookStmt);
        $result = mysqli_stmt_get_result($bookStmt);
        $bookData = mysqli_fetch_assoc($result);

        // Update book quantity in kelola_buku
        $updateBookQuery = "UPDATE kelola_data_buku SET banyak_buku = banyak_buku + 1 WHERE id = ?";
        $updateStmt = mysqli_prepare($koneksi, $updateBookQuery);
        mysqli_stmt_bind_param($updateStmt, 'i', $bookData['id_buku']);
        mysqli_stmt_execute($updateStmt);

        // Pindahkan data ke tabel sewa_selesai
        $query = "INSERT INTO sewa_selesai (id_buku, user_id, nama_buku, nama_peminjam, harga_sewa, total_harga, tanggal_pinjam, tanggal_kembali, denda, status)
                  SELECT id_buku, user_id, nama_buku, nama_peminjam, harga_sewa, total_harga, tanggal_pinjam, tanggal_kembali, 0, 'Selesai Tepat Waktu'
                  FROM status_sewa
                  WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);


        if (mysqli_stmt_execute($stmt)) {
            // Hapus dari status_sewa
            $deleteQuery = "DELETE FROM status_sewa WHERE id = ?";
            $deleteStmt = mysqli_prepare($koneksi, $deleteQuery);
            mysqli_stmt_bind_param($deleteStmt, 'i', $id);
            mysqli_stmt_execute($deleteStmt);

            echo 'Data berhasil dipindahkan ke sewa selesai.';
        } else {
            throw new Exception('Gagal memindahkan data.');
        }

    } elseif ($action === 'Diperpanjang') {
        // Perbarui tanggal kembali menjadi 7 hari dari hari ini
        $newDate = date('Y-m-d', strtotime('+7 days'));

        // Update tanggal, total harga (dikali 2), dan status
        $query = "UPDATE status_sewa SET tanggal_kembali = ?, total_harga = total_harga * 2, status = 'Perpanjang' WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'si', $newDate, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo 'Tanggal kembali berhasil diperpanjang, harga diperbarui, dan status diubah.';
        } else {
            throw new Exception('Gagal memperbarui tanggal kembali, harga, dan status.');
        }

    } elseif ($action === 'Dibatalkan') {
        // Get the book ID before deleting
        $getBookIdQuery = "SELECT id_buku FROM status_sewa WHERE id = ?";
        $bookStmt = mysqli_prepare($koneksi, $getBookIdQuery);
        mysqli_stmt_bind_param($bookStmt, 'i', $id);
        mysqli_stmt_execute($bookStmt);
        $result = mysqli_stmt_get_result($bookStmt);
        $bookData = mysqli_fetch_assoc($result);

        // Update book quantity in kelola_buku
        $updateBookQuery = "UPDATE kelola_data_buku SET banyak_buku = banyak_buku + 1 WHERE id = ?";
        $updateStmt = mysqli_prepare($koneksi, $updateBookQuery);
        mysqli_stmt_bind_param($updateStmt, 'i', $bookData['id_buku']);
        mysqli_stmt_execute($updateStmt);

        // Hapus data dari status_sewa
        $query = "DELETE FROM status_sewa WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            echo 'Data berhasil dihapus dan stok buku dikembalikan.';
        } else {
            throw new Exception('Gagal menghapus data.');
        }

    } else {
        throw new Exception('Aksi tidak valid.');
    }
} catch (Exception $e) {
    echo 'Terjadi kesalahan: ' . $e->getMessage();
}
?>