<?php
session_start();
include 'koneksi2.php'; // Include the database connection file

// Redirect ke login jika belum login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$role = $_SESSION['role'];
$username = $_SESSION['username'];
$nama = $_SESSION['nama'];
$query = "SELECT * FROM kelola_data_buku";
$result = mysqli_query($koneksi, $query);
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
    <style>
        .book-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
            padding: 10px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .book-card img {
            max-height: 200px;
            margin-bottom: 10px;
            border-radius: 10px;
        }

        .book-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .book-price {
            color: #ff7f00;
            font-size: 18px;
            margin: 10px 0;
        }

        .banner {
            margin-top: -20px;
            background-color: #007bff;
            color: white;
            padding: 40px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .banner h2 {
            white-space: nowrap;
            display: inline-block;
            animation: marquee 10s linear infinite;
        }

        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .banner button {
            background-color: #ff7f00;
            border: none;
            padding: 10px 20px;
            color: white;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .banner button:hover {
            background-color: #e66b00;
        }

        avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6c63ff, #2196f3);
            /* Gradient background */
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 50px;
            /* Icon size */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Shadow */
            margin: 20px auto;
        }

        .avatar:hover {
            transform: scale(1.1);
            /* Zoom effect */
            transition: transform 0.3s ease;
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
                    <div class="avatar">
                        <span class="badge bg-info text-dark pt-2 pb-2 px-3 rounded-pill">
                            <i class="fas fa-user me-1"></i>
                            Selamat Datang, <?= ucwords(strtolower($username)); ?>!
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mt-5">
            <div class="banner">
                <h2>
                    "Baca Lebih Banyak, Bayar Lebih Sedikit"
                </h2>
                <p
                    style="font-size: 1.2em; font-weight: bold; color:rgb(224, 234, 237); text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
                    <span style="color:black">De</span>
                    <span style="color: white">Book</span>
                    - Aplikasi Penyewaan Buku Terbaik di Dunia
                </p>
                <button>
                    Sewa Buku Segera
                </button>
            </div>
            <div class="row">
                <?php while ($buku = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="book-card animate__animated animate__fadeInUp animate__hover">
                            <img src="<?= htmlspecialchars($buku['sampul']); ?>" class="img-fluid" alt="Sampul Buku">
                            <div class="book-title">
                                <?= htmlspecialchars($buku['judul']); ?>
                            </div>
                            <div class="book-price mb-2">
                                <strong>Rp. <?= number_format($buku['harga'], 0, ',', '.'); ?></strong>
                            </div>
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#detailModal<?= $buku['id']; ?>">Detail Buku</button>
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#pinjamModal"
                                onclick="setPinjamData(<?= $buku['id']; ?>, '<?= htmlspecialchars($buku['judul']); ?>', <?= $buku['harga']; ?>)">
                                Sewa Buku
                            </button>
                        </div>
                    </div>

                    <!-- Modal Detail Buku -->
                    <div class="modal fade" id="detailModal<?= $buku['id']; ?>" tabindex="-1">
                        <div class="modal-dialog animate__animated animate__zoomIn">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detail Buku</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <img src="<?= htmlspecialchars($buku['sampul']); ?>"
                                        class="img-fluid mb-3 mx-auto d-block" style="max-height: 350px;" alt="Sampul Buku">
                                    <h5>Judul: <?= htmlspecialchars($buku['judul']); ?></h5>
                                    <p><strong>Penerbit:</strong> <?= htmlspecialchars($buku['penerbit']); ?></p>
                                    <p><strong>Harga:</strong> Rp. <?= number_format($buku['harga'], 0, ',', '.'); ?></p>
                                    <p><strong>Deskripsi:</strong> <?= htmlspecialchars($buku['deskripsi']); ?></p>
                                    <p><strong>Tersedia:</strong> <?= $buku['banyak_buku']; ?> Buku</p>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#pinjamModal"
                                        onclick="setPinjamData(<?= $buku['id']; ?>, '<?= htmlspecialchars($buku['judul']); ?>', <?= $buku['harga']; ?>)">
                                        Sewa Buku
                                    </button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>


            <!-- Modal Form Peminjaman -->
            <div class="modal fade" id="pinjamModal" tabindex="-1" aria-labelledby="pinjamModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="formPinjam" action="proses_pinjam.php" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="pinjamModalLabel">Detail Sewa Buku</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- ID Buku -->
                                <input type="hidden" id="idBuku" name="id_buku">
                                <!-- Sampul Buku -->
                                <div class="mb-3">
                                    <!-- <label for="sampulBuku" class="form-label"></label> -->
                                    <img id="sampulBuku" class="img-fluid mx-auto d-block" style="max-height: 300px;"
                                        alt="Sampul Buku">
                                </div>
                                <!-- Nama Buku -->
                                <div class="mb-3">
                                    <label for="namaBuku" class="form-label">Nama Buku</label>
                                    <input type="text" class="form-control" id="namaBuku" name="nama_buku" readonly>
                                </div>
                                <!-- Nama Peminjam -->
                                <div class="mb-3">
                                    <label for="namaPeminjam" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="namaPeminjam" name="nama_peminjam"
                                        readonly>
                                </div>

                                <!-- Harga Buku -->
                                <div class="mb-3">
                                    <label for="hargaBuku" class="form-label">Harga Sewa</label>
                                    <input type="text" class="form-control" id="hargasewa" name="harga_sewa" readonly>
                                </div>

                                <!-- Total Harga -->
                                <div class="mb-3">
                                    <label for="totalHarga" class="form-label">Total Harga Sewa</label>
                                    <input type="text" class="form-control" id="totalHarga" name="total_harga" readonly>
                                </div>

                                <!-- Tanggal Pinjam -->
                                <div class="mb-3">
                                    <label for="tanggalPinjam" class="form-label">Tanggal Pinjam</label>
                                    <input type="date" class="form-control" id="tanggalPinjam" name="tanggal_pinjam"
                                        readonly>
                                </div>

                                <!-- Tanggal Kembali -->
                                <div class="mb-3">
                                    <label for="tanggalKembali" class="form-label">Tanggal Kembali</label>
                                    <input type="date" class="form-control" id="tanggalKembali" name="tanggal_kembali"
                                        readonly>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-success">Sewa Buku</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- JavaScript untuk Pinjam Buku -->
        <script>
            const pinjamButtons = document.querySelectorAll('.btn-success');
            pinjamButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const bookCard = this.closest('.book-card');
                    const sampulUrl = bookCard.querySelector('img').src;

                    // Set the book cover image in modal
                    const modalImg = document.querySelector('#pinjamModal img#sampulBuku');
                    if (modalImg) {
                        modalImg.src = sampulUrl;
                    }
                });
            });

            function setPinjamData(idBuku, namaBuku, hargasewa) {
                document.getElementById("idBuku").value = idBuku;
                document.getElementById("namaBuku").value = namaBuku;
                document.getElementById("hargasewa").value = hargasewa;

                // Hitung Total Harga  1 hari x harga buku)
                const totalHarga = 1 * hargasewa;
                document.getElementById("totalHarga").value = totalHarga;

                // Tanggal Pinjam (hari ini)
                const today = new Date();
                const tanggalPinjam = today.toISOString().split("T")[0];
                document.getElementById("tanggalPinjam").value = tanggalPinjam;

                // Tanggal Kembali (8 hari dari sekarang)
                const kembali = new Date();
                kembali.setDate(today.getDate() + 8);
                const tanggalKembali = kembali.toISOString().split("T")[0];
                document.getElementById("tanggalKembali").value = tanggalKembali;

                // Isi otomatis nama peminjam
                document.getElementById("namaPeminjam").value = "<?= $nama; ?>";
            }

            document.addEventListener("DOMContentLoaded", function () {
                const pinjamModal = document.getElementById("pinjamModal");

                pinjamModal.addEventListener("show.bs.modal", function (event) {
                    // Tombol yang memicu modal
                    const button = event.relatedTarget;
                    // Ambil data dari atribut data-*
                    const idBuku = button.getAttribute("data-id");
                    const namaBuku = button.getAttribute("data-nama");
                    const hargasewa = parseInt(button.getAttribute("data-harga"));
                    // Panggil fungsi setPinjamData
                    setPinjamData(idBuku, namaBuku, hargasewa);
                });
            });

            // Fungsi untuk meminjam buku dengan batasan 3 buku
            function pinjamBuku(idBuku, judul) {
                // Konfirmasi peminjaman
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda ingin meminjam buku '" + judul + "'?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, sewa buku!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Kirim data melalui AJAX ke proses_pinjam.php
                        fetch('proses_pinjam.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'id_buku=' + idBuku
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: data.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        location.reload(); // Reload halaman
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: data.message,
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Terjadi kesalahan!',
                                });
                            });
                    }
                });
            }
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

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>