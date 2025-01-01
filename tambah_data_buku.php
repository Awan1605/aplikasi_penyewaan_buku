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
    <title>Kelola data buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="tambah_buku.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Bootstrap CSS (optional) -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

    <!-- Navbar -->
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
        <section>

            <div class="container-fluid px-4 py-3"></div>
            <div class=" justify-content-between align-items-center mb-4">
                <h2 class="text-center text-drak">Data Buku</h2>
                <hr class="mb-4" style="border-top: 2px solid #3a5afa;">
                <button class="btn btn-info btn-add shadow" data-bs-toggle="modal" data-bs-target="#tambahDataModal">
                    <i class="fas fa-plus me-2"></i>
                    Tambah Data Buku
                </button>
            </div>
            <table id="example" class="display" style="width:100%">

                <thead class=" text-center" style="background-color:rgb(58, 90, 250); color: white;">
                    <tr>
                        <th>NO</th>
                        <th>Sampul</th>
                        <th>Judul Buku</th>
                        <th>Deskripsi Buku</th>
                        <th>Harga</th>
                        <th>Banyak Buku</th>
                        <th>Penerbit</th>
                        <th>Aksi</th>
                    </tr>
                    </style=>
                    <!-- untuk mengambil data -->
                <tbody>
                    <?php
                    error_reporting(E_ALL);
                    ini_set('display_errors', 1);
                    include 'koneksi2.php';
                    $query = mysqli_query($koneksi, "SELECT * FROM kelola_data_buku");
                    $no = 1;
                    if (!$query) {
                        echo "Query error: " . mysqli_error($koneksi);
                    } else {
                        if (mysqli_num_rows($query) > 0) {
                            while ($data = mysqli_fetch_assoc($query)) {
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <?php

                                        $sampul = $data['sampul'];
                                        if (!empty($sampul)) {
                                            echo '<img src="' . htmlspecialchars($sampul) . '" alt="Sampul Buku" class="rounded shadow" style="width: 100px; height: auto;">';
                                        } else {
                                            echo '<img src="https://via.placeholder.com/100" alt="Sampul Buku" class="rounded shadow">';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($data['judul']); ?></td>
                                    <td><?php echo htmlspecialchars($data['deskripsi']); ?></td>
                                    <td>Rp <?php echo number_format($data['harga'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($data['banyak_buku']); ?></td>
                                    <td><?php echo htmlspecialchars($data['penerbit']); ?></td>

                                    <td>
                                        <button class="btn btn-success btn-sm me-1 edit-button" data-bs-toggle="modal"
                                            data-bs-target="#editDataModal" data-sampul="<?php echo $data['sampul']; ?>"
                                            data-id="<?php echo $data['id']; ?>" data-judul="<?php echo $data['judul']; ?>"
                                            data-deskripsi="<?php echo $data['deskripsi']; ?>"
                                            data-harga="<?php echo $data['harga']; ?>"
                                            data-banyak_buku="<?php echo $data['banyak_buku']; ?>"
                                            data-penerbit="<?php echo $data['penerbit']; ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="hapus_buku.php?sampul=<?php echo $data['sampul']; ?>"
                                            class="btn btn-danger btn-sm btn-hapus">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </a>

                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='7'>Tidak ada data tersedia</td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
    </div>
    </section>
    <!-- Modal Tambah Data Buku -->
    <div class="modal fade" id="tambahDataModal" tabindex="-1" aria-labelledby="tambahDataLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="tambahDataLabel">Tambah Data Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="tambah_buku.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="judul" class="form-label">Sampul</label>
                            <input type="file" class="form-control" id="sampul" name="sampul" required>
                        </div>
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Buku</label>
                            <input type="text" class="form-control" id="judul" name="judul" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Buku</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="harga" name="harga" required>
                        </div>
                        <div class="mb-3">
                            <label for="banyak_buku" class="form-label">Banyak Buku</label>
                            <input type="number" class="form-control" id="banyak_buku" name="banyak_buku" required>
                        </div>
                        <div class="mb-3">
                            <label for="penerbit" class="form-label">Penerbit</label>
                            <input type="text" class="form-control" id="penerbit" name="penerbit" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 shadow">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Data -->
    <div class="modal fade" id="editDataModal" tabindex="-1" aria-labelledby="editDataLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editDataLabel">Edit Data Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="update_buku.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="mb-3">
                            <label for="edit-sampul" class="form-label">Sampul</label>
                            <input type="file" class="form-control" id="edit-sampul" name="sampul" required>
                            <img id="edit-sampul-preview" src="" alt="Preview Sampul"
                                style="display: none; width: 150px; height: auto; margin-top: 10px;">
                        </div>
                        <div class="mb-3">
                            <label for="edit-judul" class="form-label">Judul Buku</label>
                            <input type="text" class="form-control" id="edit-judul" name="judul" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-deskripsi" class="form-label">Deskripsi Buku</label>
                            <textarea class="form-control" id="edit-deskripsi" name="deskripsi" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit-harga" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="edit-harga" name="harga" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-banyak_buku" class="form-label">Banyak Buku</label>
                            <input type="number" class="form-control" id="edit-banyak_buku" name="banyak_buku" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-penerbit" class="form-label">Penerbit</label>
                            <input type="text" class="form-control" id="edit-penerbit" name="penerbit" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 shadow">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#example').DataTable();
        });

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

        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.edit-button');
            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const judul = this.getAttribute('data-judul');
                    const deskripsi = this.getAttribute('data-deskripsi');
                    const harga = this.getAttribute('data-harga');
                    const banyak_buku = this.getAttribute('data-banyak_buku');
                    const penerbit = this.getAttribute('data-penerbit');
                    const sampul = this.getAttribute('data-sampul'); // Atribut data untuk sampul

                    // Set nilai form
                    document.getElementById('edit-judul').value = judul;
                    document.getElementById('edit-deskripsi').value = deskripsi;
                    document.getElementById('edit-harga').value = harga;
                    document.getElementById('edit-banyak_buku').value = banyak_buku;
                    document.getElementById('edit-penerbit').value = penerbit;

                    // Set preview gambar sampul
                    document.getElementById('edit-id').value = id;
                    const sampulPreview = document.getElementById('edit-sampul-preview');
                    if (sampul) {
                        sampulPreview.src = sampul;
                        sampulPreview.style.display = 'block';
                    } else {
                        sampulPreview.style.display = 'none';
                    }

                    // Make image upload optional during edit
                    document.getElementById('edit-sampul').removeAttribute('required');
                });
            });
        });
        // button Hapus
        document.querySelectorAll('.btn-hapus').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault(); // Mencegah tautan langsung
                const url = this.href; // Ambil URL tujuan

                // Tampilkan SweetAlert
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });

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
                    // Panggil logout.php untuk menghapus session
                    window.location.href = 'logout.php';
                }
            });
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

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>