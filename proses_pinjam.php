<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'koneksi2.php';

// Periksa apakah session sudah ada
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Anda harus login untuk mengakses halaman ini.'); window.location.href='login.php';</script>";
    exit;
}

// Ambil username dari sesi
$username = $_SESSION['username'];

// Ambil user_id berdasarkan username
$query = "SELECT user_id, nama FROM users WHERE username = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_id = $user['user_id']; // Ambil user_id dari hasil query
    $nama_peminjam = $user['nama']; // Ambil nama peminjam dari hasil query
} else {
    echo "<script>alert('Terjadi kesalahan. Silakan login kembali.'); window.location.href='login.php';</script>";
    exit;
}

// Cek apakah user_id valid
if (empty($user_id)) {
    echo "<script>alert('User ID tidak ditemukan. Silakan login kembali.'); window.location.href='login.php';</script>";
    exit;
}

// Validasi jumlah peminjaman
$query = "SELECT COUNT(*) AS jumlah_pinjam FROM status_sewa WHERE user_id = ? AND status = 'disewa'";
$stmt = $koneksi->prepare($query);
$stmt->bind_param('i', $user_id); // Gunakan user_id untuk validasi
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['jumlah_pinjam'] >= 3) {
    echo "<script>alert('Anda sudah mencapai batas maksimal 3 buku.'); window.location.href='status_sewa.php';</script>";
    exit;
}

// Cek ketersediaan buku
$query_buku = "SELECT banyak_buku FROM kelola_data_buku WHERE id = ?";
$stmt_buku = $koneksi->prepare($query_buku);
$stmt_buku->bind_param('i', $_POST['id_buku']);
$stmt_buku->execute();
$result_buku = $stmt_buku->get_result();
$buku = $result_buku->fetch_assoc();

if ($buku['banyak_buku'] <= 0) {
    echo "<script>alert('Maaf, buku ini sedang tidak tersedia (stok habis).'); window.location.href='status_sewa.php';</script>";
    exit;
}

// Proses peminjaman buku
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_buku = filter_input(INPUT_POST, 'id_buku', FILTER_VALIDATE_INT);
    $nama_buku = htmlspecialchars($_POST['nama_buku']);
    $harga_sewa = filter_input(INPUT_POST, 'harga_sewa', FILTER_VALIDATE_FLOAT);
    $total_harga = filter_input(INPUT_POST, 'total_harga', FILTER_VALIDATE_FLOAT);
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];

    // Periksa apakah buku sudah dipinjam oleh user yang sama
    $check_query = "SELECT COUNT(*) AS count FROM status_sewa WHERE id_buku = ? AND user_id = ? AND status = 'disewa'";
    $stmt = $koneksi->prepare($check_query);
    $stmt->bind_param('ii', $id_buku, $user_id); // Menggunakan user_id dan id_buku
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        echo "<script>alert('Anda sudah menyewa buku ini dan tidak bisa memnyewa lagi'); window.location.href='status_sewa.php';</script>";
    } else {
        // Simpan data peminjaman ke tabel status_sewa
        $query = "INSERT INTO status_sewa (id_buku, user_id, nama_buku, nama_peminjam, harga_sewa, total_harga, tanggal_pinjam, tanggal_kembali, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'disewa')";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param('iissdsss', $id_buku, $user_id, $nama_buku, $nama_peminjam, $harga_sewa, $total_harga, $tanggal_pinjam, $tanggal_kembali);

        if ($stmt->execute()) {
            // Jika peminjaman berhasil, update jumlah buku yang tersedia
            $update_query = "UPDATE kelola_data_buku SET banyak_buku = banyak_buku - 1 WHERE id = ?";
            $update_stmt = $koneksi->prepare($update_query);
            $update_stmt->bind_param('i', $id_buku);
            $update_stmt->execute();

            echo "<script>alert('Data penyewaan berhasil disimpan.'); window.location.href='status_sewa.php';</script>";
        } else {
            error_log("Error: " . $stmt->error);
            echo "<script>alert('Gagal menyimpan data penyewaan.'); window.location.href='status_sewa.php';</script>";
        }
    }
}
?>