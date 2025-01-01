<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
// Cek apakah session login tersedia
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
// Daftar role yang tidak diizinkan mengakses halaman ini
$role = strtolower($_SESSION['role']);
$restricted_role = ['superadmin'];

if (in_array($role, $restricted_role)) {
    echo "Redirecting to katalog_buku.php...";
    header("Location: katalog_buku.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeBook Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="tambah_buku.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Bootstrap CSS (optional) -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.min.css">

    <style>
        #example {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        #example th,
        #example td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            vertical-align: middle;
        }

        #example th {
            background-color: rgb(51, 151, 198);
            color: white;
        }

        #example tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Styling untuk gambar */
        #example img {
            width: 100px;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Styling untuk tombol
        .btn {
            display: inline-block;
            font-size: 12px;
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-sm {
            font-size: 11px;
            padding: 5px 10px;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-warning {
            background-color: #ffc107;
            color: black;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        /* Tambahkan margin untuk tombol */
        /* .me-1 {
            margin-right: 5px;
        } */
        */
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="brand-title">
            <span style="color:black">De</span>
            <span style="color: var(--info-emphasis-color);">Book</span>
        </div>
        <?php if ($role == 'superadmin' || $role == 'petugas'): ?>
            <div class="nav-item">
                <i class="fas fa-home"></i>
                <span class="nav-text"><a href="dashboard.php">Dashboard</a></span>
            </div>
        <?php endif; ?>
        <?php if ($role == 'petugas' || $role == 'superadmin' || $role == 'penyewa'): ?>
            <div class="nav-item">
                <i class="fas fa-book"></i>
                <span class="nav-text"><a href="katalog_buku.php">Katalog Buku</a></span>
            </div>
        <?php endif; ?>
        <?php if ($role == 'petugas' || $role == 'superadmin' || $role == 'penyewa'): ?>
            <div class="nav-item">
                <i class="fas fa-clock"></i>
                <span class="nav-text"><a href="status_sewa.php">Status Sewa</a></span>
            </div>
        <?php endif; ?>
        <?php if ($role == 'superadmin'): ?>
            <div class="nav-item">
                <i class="fas fa-book-open"></i>
                <span class="nav-text"><a href="tambah_data_buku.php">Kelola Data Buku</a></span>
            </div>
        <?php endif; ?>
        <?php if ($role == 'superadmin'): ?>
            <div class="nav-item">
                <i class="fas fa-users"></i>
                <span class="nav-text"><a href="data_anggota.php">Data Pengguna</a></span>
            </div>
        <?php endif; ?>
        <?php if ($role == 'superadmin'): ?>
            <div class="nav-item">
                <i class="fas fa-money-bill-wave"></i>
                <span class="nav-text"><a href="sewa_selesai.php">Sewa Selesai</a></span>
            </div>
        <?php endif; ?>
        <?php if ($role == 'petugas' || $role == 'penyewa'): ?>
            <div class="nav-item">
                <i class="fas fa-history"></i>
                <span class="nav-text"><a href="riwayat.php">Riwayat</a></span>
            </div>
        <?php endif; ?>
        <?php if ($role == 'petugas' || $role == 'superadmin' || $role == 'penyewa'): ?>
            <div class="nav-item" id="logout">
                <i class="fas fa-sign-out-alt"></i>
                <span class="nav-text"><a href="logout.php">Logout</a></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-light" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="search-box">
                    <input type="text" class="form-control" placeholder="Cari buku...">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <div class="d-flex align-items-center">
                    <div class="dropdown me-3">
                        <button class="btn btn-light dropdown-toggle" type="button" id="categoryDropdown"
                            data-bs-toggle="dropdown">
                            Kategori
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                            <li><a class="dropdown-item" href="#" data-category="Cerpen">Cerpen</a></li>
                            <li><a class="dropdown-item" href="#" data-category="Teknologi">Teknologi</a></li>
                            <li><a class="dropdown-item" href="#" data-category="Hukum">Hukum</a></li>
                        </ul>
                    </div>
                    <div class="position-relative me-3">
                        <i class="fas fa-bell fs-5"></i>
                        <span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                    </div>
                    <!-- <img src="https://picsum.photos/40/40?random=1" class="rounded-circle me-2" alt="Profile"> -->
                    <div>
                        <a class="navbar-brand" href="#">
                            <span class="badge bg-info text-dark pt-2 pb-2 px-3 rounded-pill">
                                <i class="fas fa-user me-1"></i>
                                Selamat Datang, <?= ucwords(strtolower($username)); ?>!
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-0 px-4 py-3">
            <div class="justify-content-between align-items-center mb-4">
                <h2 class="text-center text-drak">Riwayat Penyewaan</h2>
                <hr class="mb-4" style="border-top: 2px solid #3a5afa;">
            </div>
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th width="5%">NO</th>
                            <th width="12%">Sampul Buku</th>
                            <th width="15%">Judul Buku</th>
                            <th width="12%">Nama Peminjam</th>
                            <th width="10%">Harga Sewa</th>
                            <th width="10%">Total Harga</th>
                            <th width="10%">Tanggal Pinjam</th>
                            <th width="10%">Tanggal Kembali</th>
                            <th width="8%">Status</th>
                            <th width="8%">Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // error_reporting(E_ALL);
                        // ini_set('display_errors', 1);
                        include 'koneksi2.php';

                        // Periksa apakah user telah login
                        if (!isset($_SESSION['username'])) {
                            echo "<script>alert('Anda harus login terlebih dahulu.'); window.location.href='login.php';</script>";
                            exit;
                        }

                        // Ambil username dari sesi
                        $username = $_SESSION['username'];

                        // Ambil role dan user_id berdasarkan username
                        $query_user = "SELECT user_id, role FROM users WHERE username = ?";
                        $stmt_user = $koneksi->prepare($query_user);
                        $stmt_user->bind_param('s', $username);
                        $stmt_user->execute();
                        $result_user = $stmt_user->get_result();

                        if ($result_user && $result_user->num_rows > 0) {
                            $user_data = $result_user->fetch_assoc();
                            $user_id = $user_data['user_id'];
                            $role = $user_data['role'];
                        } else {
                            echo "<script>alert('Terjadi kesalahan. Silakan login kembali.'); window.location.href='login.php';</script>";
                            exit;
                        }

                        // Query data berdasarkan role
                        if ($role === 'superadmin') {
                            $stmt = $koneksi->prepare("SELECT * FROM sewa_selesai");
                        } else {
                            $stmt = $koneksi->prepare("SELECT * FROM sewa_selesai WHERE user_id = ?");
                            $stmt->bind_param('i', $user_id);
                        }

                        // Eksekusi query
                        $stmt->execute();
                        $query = $stmt->get_result();

                        $no = 1;

                        if (!$query) {
                            echo "<tr><td colspan='10' class='text-center'>Query error: " . mysqli_error($koneksi) . "</td></tr>";
                        } else {
                            if (mysqli_num_rows($query) > 0) {
                                while ($data = mysqli_fetch_assoc($query)) {
                                    $id_buku = $data['id_buku'] ?? 0;
                                    $stmt = mysqli_prepare($koneksi, "SELECT sampul FROM kelola_data_buku WHERE id = ?");
                                    mysqli_stmt_bind_param($stmt, "i", $id_buku);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    $sampul_data = mysqli_fetch_assoc($result);
                                    $sampul = $sampul_data['sampul'];
                                    ?>
                                    <tr>
                                        <td class="text-center align-middle"><?= $no++; ?></td>
                                        <td class="text-center align-middle">
                                            <?php if (file_exists("$sampul")): ?>
                                                <img src="<?= htmlspecialchars($sampul) ?>" alt="Sampul Buku" class="img-thumbnail"
                                                    style="max-width: 80px;">
                                            <?php else: ?>
                                                <span class="text-muted">Tidak tersedia</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle"><?= htmlspecialchars($data['nama_buku'] ?? ''); ?></td>
                                        <td class="align-middle"><?= htmlspecialchars($data['nama_peminjam'] ?? ''); ?></td>
                                        <td class="align-middle text-end">Rp
                                            <?= number_format($data['harga_sewa'] ?? 0, 0, ',', '.'); ?>
                                        </td>
                                        <td class="align-middle text-end">Rp
                                            <?= number_format($data['total_harga'] ?? 0, 0, ',', '.'); ?>
                                        </td>
                                        <td class="align-middle text-center"><?= htmlspecialchars($data['tanggal_pinjam'] ?? ''); ?>
                                        </td>
                                        <td class="align-middle text-center">
                                            <?= htmlspecialchars($data['tanggal_kembali'] ?? ''); ?>
                                        </td>
                                        <td
                                            class="align-middle <?= (strtolower($data['status']) == 'terlambat') ? 'text-danger' : 'text-primary' ?>">
                                            <?= htmlspecialchars($data['status'] ?? ''); ?>
                                        </td>
                                        <td class="align-middle"><?= htmlspecialchars($data['denda'] ?? ''); ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.all.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#example').DataTable();
            });

            // var categoryChart = new ApexCharts(document.querySelector("#categoryChart"), categoryOptions);
            // categoryChart.render();

            document.querySelector('.nav-item#logout a').addEventListener('click', function (event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Yakin ingin logout?',
                    text: 'Anda akan keluar dari sesi ini.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Logout!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Panggil logout.php untuk menghapus session
                        window.location.href = 'logout.php';
                    }
                });
            });
        </script>
</body>

</html>