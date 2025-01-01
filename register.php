<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .card {
            box-shadow:
                0.1px 0.5px 1.9px rgba(0, 0, 0, 0.024),
                0.3px 1.1px 4.3px rgba(0, 0, 0, 0.05),
                0.6px 1.9px 7.7px rgba(0, 0, 0, 0.079),
                1px 3.2px 12.8px rgba(0, 0, 0, 0.115),
                1.6px 5.3px 21.2px rgba(0, 0, 0, 0.168),
                2.8px 9.2px 37px rgba(0, 0, 0, 0.218),
                6px 20px 80px rgba(0, 0, 0, 0.26);
            transition: box-shadow 0.6s ease-in-out;
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
    include 'koneksi2.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
        $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
        $nohp = mysqli_real_escape_string($koneksi, $_POST['nohp']);
        $username = mysqli_real_escape_string($koneksi, $_POST['username']);
        $password = mysqli_real_escape_string($koneksi, $_POST['password']);

        // Role default sebagai penyewa
        $role = 'penyewa';

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
                    title: 'Registrasi Gagal!',
                    text: 'Username sudah digunakan, silakan pilih username lain.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'register.php';
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
                    title: 'Registrasi Berhasil!',
                    text: 'Akun Anda telah dibuat. Klik OK untuk login.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'login.php';
                });
            </script>";
            } else {
                echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Registrasi Gagal!',
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
                title: 'Registrasi Gagal!',
                text: 'Tidak dapat memproses permintaan.',
                confirmButtonText: 'OK'
            });
        </script>";
        }
    }
    ?>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 93vh;">
        <div class="card mt-5" style="width: 80%;">
            <div class="row g-0">
                <div class="col-md-6 p-4">
                    <h2>Silahkan Masukan Data anda!</h2>
                    <form action="" method="post">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" placeholder="Masukan Nama Lengkap"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <input type="text" class="form-control" name="alamat" placeholder="Masukan Alamat" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">No.Hp</label>
                            <input type="number" class="form-control" name="nohp" placeholder="Masukan No Hp anda"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="text" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Masukan username"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="Password1" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Masukan Password"
                                required>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </form>
                    <span class="mt-4">Sudah punya akun. login <a href="login.php"
                            class="text-primary">Disini</a></span>
                </div>
                <div class="gambar col-md-6 mt-5 p-5">
                    <img src="gambar_login.PNG" width="400" height="900" class="img-fluid" alt="Gambar login">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>