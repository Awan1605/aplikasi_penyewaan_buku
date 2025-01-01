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
$restricted_role = ['penyewa', 'petugas'];

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.4/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style4.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <h3 class="animated-title"><span class="text-dark">DAFTAR PENGGUNA</span></h3>
            <hr>
            <button type="button" class="btn btn-info mb-2" data-bs-toggle="modal" data-bs-target="#tambahDataModal">
                <i class="fas fa-plus-circle me-2"></i>TAMBAH PENGGUNA
            </button>
            <table id="example" class="display" style="width:100%">
                <thead class="table-dark" style="background-color:rgba(45, 193, 219, 0.8); color: black;">
                    <tr>
                        <th scope="col">NO</th>
                        <th scope="col">Nama Pengguna</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">No HP</th>
                        <th scope="col">username</th>
                        <th scope="col">Role</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include 'koneksi2.php';
                    $query = mysqli_query($koneksi, "SELECT * FROM users");
                    $no = 1;
                    while ($data = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $data['nama']; ?></td>
                            <td><?php echo $data['alamat']; ?></td>
                            <td><?php echo $data['nohp']; ?></td>
                            <td><?php echo $data['username']; ?></td>
                            <td><?php echo $data['role']; ?></td>
                            <td>
                                <button class="btn btn-success btn-sm me-1 edit-button" data-bs-toggle="modal"
                                    data-bs-target="#editDataModal" data-nama="<?php echo $data['nama']; ?>"
                                    data-nama="<?php echo $data['nama']; ?>" data-nohp="<?php echo $data['nohp']; ?>"
                                    data-alamat="<?php echo $data['alamat']; ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="hapus_anggota.php?nama=<?php echo $data['nama']; ?>"
                                    class="btn btn-danger btn-sm delete-button">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    include 'koneksi2.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
        $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
        $nohp = mysqli_real_escape_string($koneksi, $_POST['nohp']);
        $username = mysqli_real_escape_string($koneksi, $_POST['username']);
        $password = mysqli_real_escape_string($koneksi, $_POST['password']);
        $role = mysqli_real_escape_string($koneksi, $_POST['role']);

        // Role default sebagai penyewa
        // $role = 'penyewa';
    
        // Periksa apakah username sudah digunakan
        $check_query = "SELECT username FROM users WHERE username = ?";
        $stmt_check = mysqli_prepare($koneksi, $check_query);
        if ($stmt_check) {
            mysqli_stmt_bind_param($stmt_check, "s", $username);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);

            if (mysqli_stmt_num_rows($stmt_check) > 0) {
                echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Penambahan anggota Gagal!',
                    text: 'Username sudah digunakan, silakan pilih username lain.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'data_anggota.php';
                });
            </script>";
                mysqli_stmt_close($stmt_check);
                exit;
            }
            mysqli_stmt_close($stmt_check);
        }

        // Hash password sebelum disimpan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Query menggunakan prepared statement
        $query = "INSERT INTO users (nama, alamat, nohp, username, password, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);

        if ($stmt) {
            // Bind parameter
            mysqli_stmt_bind_param($stmt, "ssssss", $nama, $alamat, $nohp, $username, $hashed_password, $role);

            // Eksekusi query
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Data Pengguna Berhasil di Tambahkan!',
                    text: 'Data berhasil dibuat. Klik OK.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'data_anggota.php';
                });
            </script>";
            } else {
                echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Pengguna Gagal di Tambhkan!',
                    text: 'Terjadi kesalahan pada sistem, silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
            </script>";
            }

            // Tutup statement
            mysqli_stmt_close($stmt);
        } else {
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Penambahan Data Gagal!',
                text: 'Tidak dapat memproses permintaan.',
                confirmButtonText: 'OK'
            });
        </script>";
        }
    }
    ?>
    <!-- Modal Tambah Data -->
    <div class="modal fade" id="tambahDataModal" tabindex="-1" aria-labelledby="tambahDataLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahDataLabel">Tambah PENGGUNA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="nama" class="form-label">NAMA</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">ALAMAT</label>
                            <input type="text" class="form-control" id="alamat" name="alamat" required>
                        </div>
                        <div class="mb-3">
                            <label for="jurusan" class="form-label">NOHP</label>
                            <input type="text" class="form-control" id="nohp" name="nohp" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="Superadmin">superadmin</option>
                                <option value="petugas">petugas</option>
                                <option value="penyewa">penyewa</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Edit Data -->
    <div class="modal fade" id="editDataModal" tabindex="-1" aria-labelledby="editDataLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDataLabel">Edit Data Anggota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="edit_anggota.php" method="POST">
                        <input type="hidden" id="edit-nim" name="nim">
                        <div class="mb-3">
                            <label for="edit-nama" class="form-label">NAMA</label>
                            <input type="text" class="form-control" id="edit-nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-alamat" class="form-label">ALAMAT</label>
                            <input type="text" class="form-control" id="edit-alamat" name="alamat" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-nohp" class="form-label">NOHP</label>
                            <input type="text" class="form-control" id="edit-nohp" name="nohp" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="Superadmin">superadmin</option>
                                <option value="petugas">petugas</option>
                                <option value="penyewa">penyewa</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.4/dist/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#example').DataTable();
        });
        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.edit-button');
            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const nama = this.getAttribute('data-nama');
                    const alamat = this.getAttribute('data-alamat');
                    const nohp = this.getAttribute('data-nohp');

                    document.getElementById('edit-nama').value = nama;
                    document.getElementById('edit-alamat').value = alamat;
                    document.getElementById('edit-nohp').value = nohp;
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            document.body.addEventListener('click', function (event) {
                if (event.target && event.target.classList.contains('delete-button')) {
                    event.preventDefault(); // Mencegah link untuk langsung melakukan aksi
                    const deleteUrl = event.target.href;

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: 'Data ini akan dihapus secara permanen!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = deleteUrl;
                        }
                    });
                }
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

    </script>
</body>

</html>