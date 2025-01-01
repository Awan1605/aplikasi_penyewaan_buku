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
$restricted_role = ['penyewa'];

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="style2.css">
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

        <!-- Stats Cards -->
        <?php
        include 'koneksi2.php';

        // Menghitung total buku
        $query_buku = "SELECT SUM(banyak_buku) AS banyak_buku FROM kelola_data_buku";
        $result_buku = mysqli_query($koneksi, $query_buku);
        $banyak_buku = 0;
        if ($result_buku) {
            $row_buku = mysqli_fetch_assoc($result_buku);
            $banyak_buku = $row_buku['banyak_buku'] ?? 0;
        }

        // Menghitung total buku yang disewa
        $query_dipinjam = "SELECT COUNT(*) as total_dipinjam FROM status_sewa WHERE status = 'disewa' OR status = 'Terlambat'";
        $result_dipinjam = mysqli_query($koneksi, $query_dipinjam);
        $total_dipinjam = mysqli_fetch_assoc($result_dipinjam)['total_dipinjam'] ?? 0;

        // Menghitung total anggota
        $query_anggota = "SELECT COUNT(*) as total FROM users WHERE role = 'penyewa'";
        $result_anggota = mysqli_query($koneksi, $query_anggota);
        $totalAnggota = mysqli_fetch_assoc($result_anggota)['total'] ?? 0;

        // Menghitung pendapatan dari sewa selesai dan terlambat
        // Menghitung pendapatan dari sewa selesai
        $query_pendapatan = "SELECT SUM(total_harga) as total FROM sewa_selesai WHERE status = 'Selesai Tepat Waktu' OR status = 'Terlambat'";
        $result_pendapatan = mysqli_query($koneksi, $query_pendapatan);
        $pendapatan_sewa = mysqli_fetch_assoc($result_pendapatan)['total'] ?? 0;

        // Menghitung denda dari status terlambat
        $query_denda = "SELECT SUM(denda) as total_denda FROM sewa_selesai WHERE status = 'Terlambat'";
        $result_denda = mysqli_query($koneksi, $query_denda);
        $pendapatan_denda = mysqli_fetch_assoc($result_denda)['total_denda'] ?? 0;

        // Total pendapatan termasuk denda
        $pendapatan_sewa += $pendapatan_denda;

        // // Menghitung denda dari status terlambat
        // $query_denda = "SELECT SUM(denda) as total_denda FROM sewa_selesai WHERE status = 'Terlambat'";
        // $result_denda = mysqli_query($koneksi, $query_denda);
        // $pendapatan_denda = mysqli_fetch_assoc($result_denda)['total_denda'] ?? 0;
        
        // Menghitung total pendapatan
        $total_pendapatan = $pendapatan_sewa;

        // Statistik peminjaman berdasarkan bulan
        $query = "SELECT MONTH(tanggal_pinjam) AS month, COUNT(*) AS total_peminjaman 
          FROM status_sewa 
          WHERE YEAR(tanggal_pinjam) = YEAR(CURDATE()) 
          AND (status = 'disewa' OR status = 'Terlambat')
          GROUP BY MONTH(tanggal_pinjam)";
        $result = mysqli_query($koneksi, $query);

        $months = [];
        $borrowing_data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $months[] = date('M', mktime(0, 0, 0, $row['month'], 10));
            $borrowing_data[] = $row['total_peminjaman'];
        }
        // Fungsi untuk mendapatkan total buku yang dipinjam untuk tahun tertentu
        function getTotalBorrowedBooks($koneksi, $year)
        {
            // Query untuk menghitung jumlah total peminjaman berdasarkan status dan tahun
            $query = "SELECT COUNT(*) AS total_dipinjam 
              FROM status_sewa 
              WHERE (status = 'disewa' OR status = 'Terlambat') 
              AND YEAR(tanggal_pinjam) = ?";

            // Mempersiapkan query dengan prepared statement
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "i", $year); // Mengikat parameter tahun ke query
            mysqli_stmt_execute($stmt); // Menjalankan query
            $result = mysqli_stmt_get_result($stmt); // Mendapatkan hasil query
        
            // Mengembalikan jumlah total peminjaman atau 0 jika tidak ada data
            return ($row = mysqli_fetch_assoc($result)) ? intval($row['total_dipinjam']) : 0;
        }

        // Mendapatkan tahun sekarang dan tahun sebelumnya
        $current_year = date('Y'); // Tahun saat ini
        $total_dipinjam_now = getTotalBorrowedBooks($koneksi, $current_year); // Total peminjaman tahun ini
        $total_dipinjam_previous = getTotalBorrowedBooks($koneksi, $current_year - 1); // Total peminjaman tahun lalu
        
        // Menghitung persentase perubahan peminjaman
        $percentage_change = ($total_dipinjam_previous > 0)
            ? (($total_dipinjam_now - $total_dipinjam_previous) / $total_dipinjam_previous) * 100
            : 0; // Jika tahun sebelumnya tidak ada data, maka perubahan 0%
        
        // Menentukan kelas tampilan berdasarkan perubahan persentase
        $percentage_class = ($percentage_change >= 0) ? 'text-success' : 'text-danger';
        $arrow_icon = ($percentage_change >= 0) ? 'fa-arrow-up' : 'fa-arrow-down';

        // Mengambil data peminjaman bulanan untuk tahun sebelumnya
        $query_prev = "SELECT MONTH(tanggal_pinjam) AS month, COUNT(*) AS total_peminjaman 
               FROM status_sewa 
               WHERE YEAR(tanggal_pinjam) = ? 
               AND (status = 'disewa' OR status = 'Terlambat')
               GROUP BY MONTH(tanggal_pinjam)";

        // Mempersiapkan query untuk data bulanan
        $stmt_prev = mysqli_prepare($koneksi, $query_prev);
        $prev_year = $current_year - 1; // Tahun sebelumnya
        mysqli_stmt_bind_param($stmt_prev, "i", $prev_year); // Mengikat parameter tahun sebelumnya ke query
        mysqli_stmt_execute($stmt_prev); // Menjalankan query
        $result_prev = mysqli_stmt_get_result($stmt_prev); // Mendapatkan hasil query
        
        // Inisialisasi array untuk data bulanan tahun sebelumnya (12 bulan)
        $borrowing_data_prev = array_fill(0, 12, 0); // Mengisi array dengan nilai 0 untuk 12 bulan
        while ($row = mysqli_fetch_assoc($result_prev)) {
            $borrowing_data_prev[$row['month'] - 1] = intval($row['total_peminjaman']); // Mengisi data sesuai bulan
        }
        ?>

        <div class="row mb-4">
            <!-- Total Buku -->
            <div class="col-xl-3 col-lg-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary text-white">
                        <i class="fas fa-book"></i>
                    </div>
                    <h6 class="mt-3">Total Buku</h6>
                    <h3><?= number_format($banyak_buku, 0, ',', '.'); ?></h3>
                    <p class="text-success mb-0"><i class="fas fa-arrow-up"></i> 12.5%</p>
                </div>
            </div>
            <!-- Dipinjam -->
            <div class="col-xl-3 col-lg-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-warning text-white">
                        <i class="fas fa-hand-holding"></i>
                    </div>
                    <h6 class="mt-3">Dipinjam</h6>
                    <h3><?= number_format($total_dipinjam, 0, ',', '.'); ?></h3>
                    <p class="<?= $percentage_class ?> mb-0"><i class="fas <?= $arrow_icon ?>"></i>
                        <?= number_format($percentage_change, 1) ?>%</p>
                </div>
            </div>
            <!-- Anggota Aktif -->
            <div class="col-xl-3 col-lg-6 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success text-white">
                        <i class="fas fa-users"></i>
                    </div>
                    <h6 class="mt-3">Anggota Aktif</h6>
                    <h3><?= number_format($totalAnggota, 0, ',', '.'); ?></h3>
                    <p class="text-success mb-0"><i class="fas fa-arrow-up"></i> 8.7%</p>
                </div>
            </div>
            <?php if ($role == 'superadmin'): ?>
                <!-- Pendapatan -->
                <div class="col-xl-3 col-lg-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-info text-white">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h6 class="mt-3">Pendapatan</h6>
                        <h3>Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                        <p class="text-success mb-0"><i class="fas fa-arrow-up"></i> 15.3%</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($role == 'superadmin'): ?>
            <!-- Charts -->
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-4">Statistik Peminjaman</h5>
                        <div id="borrowingChart"></div>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Scripts -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.getElementById('sidebarToggle').addEventListener('click', function () {
                    document.getElementById('sidebar').classList.toggle('sidebar-collapsed');
                    document.querySelector('.main-content').classList.toggle('main-content-expanded');
                });

                // Get current page URL
                const currentPage = window.location.pathname.split('/').pop();

                // Add active class to nav items
                const navItems = document.querySelectorAll('.nav-item');

                navItems.forEach(item => {
                    // Check if this nav item links to current page
                    const link = item.querySelector('a');
                    if (link && link.getAttribute('href') === currentPage) {
                        item.classList.add('active');
                    }

                    item.addEventListener('click', () => {
                        navItems.forEach(navItem => {
                            navItem.classList.remove('active');
                        });
                        item.classList.add('active');
                    });
                });

                // Get data for previous year

                // Borrowing Chart Configuration
                var borrowingOptions = {
                    series: [{
                        name: 'Peminjaman ' + new Date().getFullYear(),
                        data: <?php echo json_encode($borrowing_data); ?>
                    },
                    {
                        name: 'Peminjaman ' + (new Date().getFullYear() - 1),
                        data: <?php echo json_encode($borrowing_data_prev); ?>
                    }],
                    chart: {
                        height: 350,
                        width: '100%',
                        type: 'area',
                        toolbar: {
                            show: false
                        }
                    },
                    colors: ['#4361ee', '#f72585'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.3
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        title: {
                            text: 'Bulan'
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah Peminjaman'
                        }
                    },
                    tooltip: {
                        x: {
                            format: 'MMM'
                        }
                    }
                };

                // Initialize and render the chart
                var borrowingChart = new ApexCharts(
                    document.querySelector("#borrowingChart"),
                    borrowingOptions
                );
                borrowingChart.render();

                // // Category Chart
                // var categoryOptions = {
                //     series: [44, 55, 13, 43, 22],
                //     chart: {
                //         height: 350,
                //         type: 'donut',
                //     },
                //     labels: ['Teknologi', 'Novel', 'Sejarah', 'Sains', 'Lainnya'],
                //     colors: ['#4361ee', '#f72585', '#4cc9f0', '#3f37c9', '#4895ef'],
                //     responsive: [{
                //         breakpoint: 480,
                //         options: {
                //             chart: {
                //                 width: 200
                //             },
                //             legend: {
                //                 position: 'bottom'
                //             }
                //         }
                //     }]
                // };

                // var categoryChart = new ApexCharts(document.querySelector("#categoryChart"), categoryOptions);
                // categoryChart.render();

                document.addEventListener('DOMContentLoaded', function () {
                    const logoutButton = document.querySelector('#logout a');
                    if (logoutButton) {
                        logoutButton.addEventListener('click', function (event) {
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
                    }
                });

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
                document.getElementById('searchInput').addEventListener('keyup', function () {
                    const searchValue = this.value.toLowerCase();
                    if (searchValue.length >= 2) {
                        // Send AJAX request to search.php
                        fetch('search.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'search=' + encodeURIComponent(searchValue)
                        })
                            .then(response => response.json())
                            .then(results => {
                                // Create dropdown with search results
                                let dropdown = document.createElement('div');
                                dropdown.className = 'search-results-dropdown';
                                dropdown.style.position = 'absolute';
                                dropdown.style.top = '100%';
                                dropdown.style.left = '0';
                                dropdown.style.right = '0';
                                dropdown.style.backgroundColor = 'white';
                                dropdown.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
                                dropdown.style.zIndex = '1000';

                                results.forEach(result => {
                                    let item = document.createElement('div');
                                    item.className = 'search-result-item p-2';
                                    item.innerHTML = `<a href="${result.url}" class="text-decoration-none">
                            <div class="d-flex align-items-center">
                                <i class="fas ${result.icon} me-2"></i>
                                <span>${result.title}</span>
                            </div>
                        </a>`;
                                    dropdown.appendChild(item);
                                });

                                // Remove existing dropdown if any
                                const existingDropdown = document.querySelector('.search-results-dropdown');
                                if (existingDropdown) {
                                    existingDropdown.remove();
                                }

                                // Add new dropdown
                                document.querySelector('.search-box').appendChild(dropdown);
                            });
                    } else {
                        // Remove dropdown if search input is too short
                        const existingDropdown = document.querySelector('.search-results-dropdown');
                        if (existingDropdown) {
                            existingDropdown.remove();
                        }
                    }
                });
            </script>
</body>

</html>