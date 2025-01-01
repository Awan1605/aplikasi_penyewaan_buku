<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Metadata dan Title -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeBook Dashboard</title>

    <!-- Link CSS dan Library -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="style2.css">
</head>

<body>
    <?php
    session_start();

    // Validasi apakah pengguna sudah login atau belum
    if ($_SESSION['level'] == "") {
        header("location:index.php?pesan=gagal");
    }
    ?>
    <p>Halo <b><?php echo $_SESSION['username']; ?></b>, Anda telah login sebagai
        <b><?php echo $_SESSION['level']; ?></b>.
    </p>
    <!-- Konten Halaman -->

    <div class="sidebar" id="sidebar">
        <div class="brand-title">
            <span style="color:black">De</span>
            <span style="color: var(--info-emphasis-color);">Book</span>
        </div>
        <div class="nav-item active">
            <i class="fas fa-home"></i>
            <span class="nav-text">Dashboard</span>
        </div>
        <div class="nav-item">
            <i class="fas fa-book"></i>
            <span class="nav-text"><a href="katalog.php">Katalog Buku</a></span>
        </div>
        <div class="nav-item">
            <i class="fas fa-clock"></i>
            <span class="nav-text">Peminjaman</span>
        </div>
        <div class="nav-item">
            <i class="fas fa-book-open"></i>
            <span class="nav-text"><a href="tambah_data_buku.php">Kelola Data Buku</a></span>
        </div>
        <div class="nav-item">
            <i class="fas fa-users"></i>
            <span class="nav-text">Data Pengguna</span>
        </div>
        <div class="nav-item">
            <i class="fas fa-money-bill-wave"></i>
            <span class="nav-text">Pembayaran</span>
        </div>
        <div class="nav-item">
            <i class="fas fa-heart"></i>
            <span class="nav-text">Favorit</span>
        </div>
        <div class="nav-item">
            <i class="fas fa-history"></i>
            <span class="nav-text">Riwayat</span>
        </div>
        <div class="nav-item" id="logout">
            <i class="fas fa-sign-out-alt"></i>
            <span class="nav-text"><a href="logout.php">Logout</a></span>
        </div>

        <!-- Informasi Pengguna -->
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
                        <img src="https://picsum.photos/40/40?random=1" class="rounded-circle me-2" alt="Profile">
                        <div>
                            <div class="fw-bold">Kelompok 4</div>
                            <small class="text-muted">Admin</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-lg-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-primary text-white">
                            <i class="fas fa-book"></i>
                        </div>
                        <h6 class="mt-3">Total Buku</h6>
                        <h3>1,528</h3>
                        <p class="text-success mb-0"><i class="fas fa-arrow-up"></i> 12.5%</p>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning text-white">
                            <i class="fas fa-hand-holding"></i>
                        </div>
                        <h6 class="mt-3">Dipinjam</h6>
                        <h3>245</h3>
                        <p class="text-danger mb-0"><i class="fas fa-arrow-down"></i> 3.2%</p>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-success text-white">
                            <i class="fas fa-users"></i>
                        </div>
                        <h6 class="mt-3">Anggota Aktif</h6>
                        <h3>847</h3>
                        <p class="text-success mb-0"><i class="fas fa-arrow-up"></i> 8.7%</p>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-info text-white">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h6 class="mt-3">Pendapatan</h6>
                        <h3>Rp 5.2M</h3>
                        <p class="text-success mb-0"><i class="fas fa-arrow-up"></i> 15.3%</p>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-4">Statistik Peminjaman</h5>
                        <div id="borrowingChart"></div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-4">Kategori Populer</h5>
                        <div id="categoryChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Sidebar Toggle
            document.getElementById('sidebarToggle').addEventListener('click', function () {
                document.getElementById('sidebar').classList.toggle('sidebar-collapsed');
                document.querySelector('.main-content').classList.toggle('main-content-expanded');
            });

            const navItems = document.querySelectorAll('.nav-item');

            navItems.forEach(item => {
                item.addEventListener('click', () => {
                    navItems.forEach(navItem => {
                        navItem.classList.remove('active');
                    });
                    item.classList.add('active');
                });
            });
            // Borrowing Chart
            var borrowingOptions = {
                series: [{
                    name: 'Peminjaman 2024',
                    data: [65, 59, 80, 81, 56, 55, 40, 88, 96, 67, 85, 73]

                }],
                chart: {
                    height: 350,
                    type: 'area',
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#4361ee'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
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
                        format: 'dd/MM/yy HH:mm'
                    },
                },
            };

            var borrowingChart = new ApexCharts(document.querySelector("#borrowingChart"), borrowingOptions);
            borrowingChart.render();

            // Category Chart
            var categoryOptions = {
                series: [44, 55, 13, 43, 22],
                chart: {
                    height: 350,
                    type: 'donut',
                },
                labels: ['Teknologi', 'Novel', 'Sejarah', 'Sains', 'Lainnya'],
                colors: ['#4361ee', '#f72585', '#4cc9f0', '#3f37c9', '#4895ef'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var categoryChart = new ApexCharts(document.querySelector("#categoryChart"), categoryOptions);
            categoryChart.render();

            document.getElementById('logout').addEventListener('click', function (event) {
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
                        window.location.href = 'login.php';
                    }
                });
            });
        </script>
    </div>
</body>

</html>