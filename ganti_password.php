<?php
include 'koneksi2.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $old_password = mysqli_real_escape_string($koneksi, $_POST['old_password']);
    $new_password = mysqli_real_escape_string($koneksi, $_POST['new_password']);

    // Periksa apakah username ada
    $check_query = "SELECT password FROM users WHERE username = ?";
    $stmt_check = mysqli_prepare($koneksi, $check_query);
    if ($stmt_check) {
        mysqli_stmt_bind_param($stmt_check, "s", $username);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            mysqli_stmt_bind_result($stmt_check, $hashed_password);
            mysqli_stmt_fetch($stmt_check);

            // Verifikasi password lama
            if (password_verify($old_password, $hashed_password)) {
                // Hash password baru
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password di database
                $update_query = "UPDATE users SET password = ? WHERE username = ?";
                $stmt_update = mysqli_prepare($koneksi, $update_query);

                if ($stmt_update) {
                    mysqli_stmt_bind_param($stmt_update, "ss", $new_hashed_password, $username);
                    $execute_update = mysqli_stmt_execute($stmt_update);

                    if ($execute_update) {
                        echo "<script>alert('Password berhasil diubah.'); window.location.href = 'login.php';</script>";
                    } else {
                        echo "<script>alert('Gagal memperbarui password.'); window.location.href = 'login.php';</script>";
                    }

                    mysqli_stmt_close($stmt_update);
                } else {
                    echo "<script>alert('Query untuk memperbarui password gagal.'); window.location.href = 'login.php';</script>";
                }
            } else {
                echo "<script>alert('Password lama salah.'); window.location.href = 'login.php';</script>";
            }

            mysqli_stmt_close($stmt_check);
        } else {
            echo "<script>alert('Username tidak ditemukan.'); window.location.href = 'login.php';</script>";
        }
    } else {
        echo "<script>alert('Query untuk memeriksa username gagal.'); window.location.href = 'login.php';</script>";
    }
}
?>