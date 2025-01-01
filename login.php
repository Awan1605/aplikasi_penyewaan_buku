<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .card {
            box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.3),
                0 12px 40px 0 rgba(0, 0, 0, 0.25);
            transition: box-shadow 0.4s ease-in-out;
        }

        .gambar img:hover {
            border-radius: 46px;
            background: #e0e0e0;
            box-shadow: 12px 12px 24px #989898,
                -12px -12px 24px #ffffff;
            transition: box-shadow 0.6s ease-in-out;
        }
    </style>
</head>

<body>
    <?php
    // Memulai session untuk menyimpan data user yang login
    session_start();
    // Menghubungkan ke database
    include 'koneksi2.php';

    // Cek jika ada request POST dari form login
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Mengamankan input username dari SQL injection
        $username = mysqli_real_escape_string($koneksi, $_POST['username']);
        $password = $_POST['password'];

        // Query untuk mencari user berdasarkan username
        $query = "SELECT * FROM users WHERE username=?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Cek apakah user ditemukan
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Menyimpan data user ke session
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Mengarahkan user ke halaman yang sesuai berdasarkan role
                if ($user['role'] === "superadmin" || $user['role'] === "petugas") {
                    // Menampilkan pesan sukses untuk admin/petugas
                    echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Login Berhasil",
                    text: "Selamat datang! ' . $user['nama'] . '",
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "dashboard.php";
                });
                </script>';
                } else {
                    // Menampilkan pesan sukses untuk user biasa
                    echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Login Berhasil",
                    text: "Selamat datang! ' . $user['nama'] . '",
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "katalog_buku.php";
                });
                </script>';
                }
                exit();
            } else {
                // Menampilkan pesan error jika password salah
                echo '<script>
            Swal.fire({
                icon: "error",
                title: "Gagal Login",
                text: "Username atau password salah!",
                timer: 2000,
                showConfirmButton: false
            });
            </script>';
            }
        } else {
            // Menampilkan pesan error jika username tidak ditemukan
            echo '<script>
        Swal.fire({
            icon: "error",
            title: "Gagal Login",
            text: "Username tidak ditemukan!",
            timer: 2000,
            showConfirmButton: false
        });
        </script>';
        }
    }
    ?>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 93vh;">
        <div class="card mt-5" style="width: 60%;">
            <div class="row g-0">
                <div class="col-md-6 p-4">
                    <h2 class="text-center"><span style="color: blue;">De</span> <span style="color: black;">Book</span>
                    </h2>
                    <form action="login.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Masukan username"
                                required minlength="4" maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Masukan Password"
                                required minlength="8">
                        </div>
                        <button type="submit" class="btn btn-primary w-100" name="login">Login</button>
                    </form>
                    <span class="mt-5">Belum Punya Akun? <a href="register.php" class="text-primary">Klik
                            Disini</a></span>
                    <br>
                    <span class="mt-2">Lupa Password? <a href="#" class="text-primary" data-bs-toggle="modal"
                            data-bs-target="#forgotPasswordModal">Ganti Password</a></span>
                </div>
                <div class="col-md-6 mt-3 p-2 gambar">
                    <img src="gambar_login.PNG" width="300" height="700" class="img-fluid" alt="Gambar login">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="ganti_password.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="forgotPasswordModalLabel">Ganti Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="forgotPasswordForm">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="username" class="form-control" id="username" name="username"
                                    placeholder="Masukan username" required>
                                <div class="mb-3">
                                    <label for="old_password" class="form-label">Password Lama</label>
                                    <input type="password" class="form-control" id="old_password" name="old_password"
                                        placeholder="Masukan password lama" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Password baru</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password"
                                        placeholder="Masukan password baru" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Ganti Password</button>
                        </form>
                    </div>
                </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.4/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>