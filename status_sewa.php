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
$restricted_role = [];

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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Bootstrap CSS (optional) -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

        /* Styling untuk tombol */
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
        .me-1 {
            margin-right: 5px;
        }

        /* tombol download pdf dan excel */
        .download-button {
            transition: all 0.3s ease;
        }

        .download-button:hover {
            transform: scale(1.1);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .download-button:active {
            transform: scale(0.9);
        }
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
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari buku...">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <div class="d-flex align-items-center">
                    <div class="dropdown me-3">
                        <button class="btn btn-light dropdown-toggle" type="button" id="categoryDropdown"
                            data-bs-toggle="dropdown">
                            Kategori
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Cerpen</a></li>
                            <li><a class="dropdown-item" href="#">Teknologi</a></li>
                            <li><a class="dropdown-item" href="#">Hukum</a></li>
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

        <div class="container-fluid mt-0 px-2">
            <div class="justify-content-between align-items-center mb-4">
                <h2 class="text-center text-drak">STATUS PENYEWAAN</h2>
                <hr class="mb-4" style="border-top: 2px solid #3a5afa;">
                <div class="mb-3 text-start">
                    <a href="export_pdf.php" class="btn btn-danger download-button">
                        <i class="fas fa-file-pdf"></i> Unduh Untuk Mengambil Buku
                    </a>
                </div>

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
                            <?php if ($role == 'superadmin' || $role == 'petugas'): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
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
                        if ($role === 'superadmin' || $role === 'petugas') {
                            $stmt = $koneksi->prepare("SELECT * FROM status_sewa");
                        } else {
                            $stmt = $koneksi->prepare("SELECT * FROM status_sewa WHERE user_id = ?");
                            $stmt->bind_param('i', $user_id);
                        }

                        // Eksekusi query
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Tampilkan data dengan loop
                        $no = 1;
                        if (!$result) {
                            echo "<tr><td colspan='10' class='text-center'>Query error: " . mysqli_error($koneksi) . "</td></tr>";
                        } else {
                            if ($result->num_rows > 0) {
                                while ($data = $result->fetch_assoc()) {
                                    $id_buku = $data['id_buku'] ?? 0;
                                    $stmt_sampul = mysqli_prepare($koneksi, "SELECT sampul FROM kelola_data_buku WHERE id = ?");
                                    mysqli_stmt_bind_param($stmt_sampul, "i", $id_buku);
                                    mysqli_stmt_execute($stmt_sampul);
                                    $result_sampul = mysqli_stmt_get_result($stmt_sampul);
                                    $sampul_data = mysqli_fetch_assoc($result_sampul);
                                    $sampul = $sampul_data['sampul'] ?? '';
                                    mysqli_stmt_close($stmt_sampul);
                                    ?>
                                    <tr>
                                        <td class="text-center align-middle"><?= $no++; ?></td>
                                        <td class="text-center align-middle">
                                            <?php if (file_exists("$sampul")): ?>
                                                <img src="<?= htmlspecialchars($sampul) ?>" class="img-thumbnail"
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
                                        <td class="align-middle text-center">
                                            <?php
                                            $current_date = date('Y-m-d');
                                            $return_date = $data['tanggal_kembali'] ?? null;
                                            $status = $data['status'] ?? 'Disewa';
                                            $denda = 0;

                                            // Calculate late days and denda if applicable
                                            if ($return_date && $return_date < $current_date && $status != 'Selesai') {
                                                $late_days = ceil((strtotime($current_date) - strtotime($return_date)) / (60 * 60 * 24));
                                                $denda = $late_days * 500;

                                                // Update status and denda in database only if necessary
                                                if ($status != 'Terlambat') {
                                                    $query = "UPDATE status_sewa SET status = ?, denda = ? WHERE id = ?";
                                                    $stmt = mysqli_prepare($koneksi, $query);
                                                    mysqli_stmt_bind_param($stmt, 'sii', $new_status, $denda, $id);
                                                    $new_status = 'Terlambat';
                                                    $id = $data['id'];
                                                    mysqli_stmt_execute($stmt);
                                                }

                                                // Display status badge
                                                echo '<span class="badge bg-danger">Terlambat</span>';
                                            } elseif ($status == 'Selesai') {
                                                // Reset denda and update status to 'Selesai'
                                                $query = "UPDATE status_sewa SET status = ?, denda = ? WHERE id = ?";
                                                $stmt = mysqli_prepare($koneksi, $query);
                                                mysqli_stmt_bind_param($stmt, 'sii', $new_status, $denda, $id);
                                                $new_status = 'Selesai';
                                                $id = $data['id'];
                                                mysqli_stmt_execute($stmt);

                                                // Display status badge
                                                echo '<span class="badge bg-success">Selesai</span>';
                                            } else {
                                                // Display current status badge
                                                echo '<span class="badge bg-warning text-dark">' . htmlspecialchars($status) . '</span>';

                                                // Ensure denda is updated if necessary
                                                $query = "UPDATE status_sewa SET denda = ? WHERE id = ?";
                                                $stmt = mysqli_prepare($koneksi, $query);
                                                mysqli_stmt_bind_param($stmt, 'ii', $denda, $id);
                                                $id = $data['id'];
                                                mysqli_stmt_execute($stmt);
                                            }

                                            ?>
                                        </td>

                                        <td class="align-middle text-end">Rp <?= number_format($denda, 0, ',', '.'); ?></td>
                                        <?php if ($role == 'superadmin' || $role == 'petugas'): ?>
                                            <td class="text-center">
                                                <button class="btn btn-success btn-sm me-1"
                                                    onclick="updateStatus(<?php echo $data['id']; ?>, 'kembali')">
                                                    <i class="fas fa-check"></i> Kembali
                                                </button>
                                                <button class="btn btn-warning btn-sm me-1"
                                                    onclick="updateStatus(<?php echo $data['id']; ?>, 'Diperpanjang')">
                                                    <i class="fas fa-clock"></i> Perpanjang
                                                </button>
                                                <button class="btn btn-danger btn-sm"
                                                    onclick="updateStatus(<?php echo $data['id']; ?>, 'Dibatalkan')">
                                                    <i class="fas fa-times"></i> Batalkan
                                                </button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        $stmt->close();
                        ?>
                </table>
            </div>

        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            //serach buku
            document.getElementById('searchInput').addEventListener('keyup', function () {
                const searchValue = this.value.toLowerCase();
                const rows = document.querySelectorAll('#example tbody tr');

                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    const rowText = Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ');
                    if (rowText.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            $(document).ready(function () {
                $('#example').DataTable({
                    "columnDefs": [
                        {
                            "targets": [0],  // Kolom pertama (NO)
                            "orderable": false // Nonaktifkan urutan untuk kolom ini
                        }
                    ]
                });
            });

            function updateStatus(id, action) {
                // First show confirmation dialog
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: `Anda akan mengubah status menjadi ${action}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url = 'proses_sewa.php';
                        const formData = new FormData();
                        formData.append('id', id);
                        formData.append('action', action);

                        fetch(url, {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.text())
                            .then(result => {
                                Swal.fire({
                                    title: 'Informasi',
                                    text: result,
                                    icon: result.includes('berhasil') ? 'success' : 'error',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
                                });
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Terjadi kesalahan saat memproses data.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#d33'
                                });
                            });
                    }
                });
            }

            $(document).ready(function () {
                $('#example').DataTable();
            });

            // Remove ApexCharts initialization if not needed
            $(document).ready(function () {
                $('#logout').on('click', function (event) {
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
                            window.location.href = 'logout.php';
                        }
                    });
                });
            });
            //search penyewa
            document.getElementById('searchInput').addEventListener('keyup', function () {
                const searchValue = this.value.toLowerCase();
                const rows = document.querySelectorAll('#example tbody tr');

                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    const rowText = Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ');
                    if (rowText.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

        </script>
</body>

</html>