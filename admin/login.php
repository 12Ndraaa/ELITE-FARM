<?php
session_start();
// Memanggil file koneksi database terpisah
require_once '../connection.php'; // Pastikan path ini benar relatif terhadap login.php

$error_message = ""; // Variabel tetap ada untuk menampung pesan error jika ingin digunakan di tempat lain

if (isset($_POST['loginbtn'])) {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // Tambahkan 'role' ke dalam query SELECT
    $stmt = $con->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Penting: Pastikan kolom 'password' di database Anda menyimpan password yang di-hash
        if (password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role']; // Simpan role pengguna ke sesi

            header('Location: ../index.php'); // Sesuaikan path redirect jika diperlukan
            exit();
        } else {
            $error_message = "Username atau password salah."; // Pesan error masih disetel, tapi tidak ditampilkan
        }
    } else {
        $error_message = "Username atau password salah."; // Pesan error masih disetel, tapi tidak ditampilkan
    }
    $stmt->close();
}
// Tidak perlu menutup koneksi $con di sini jika akan digunakan lagi di halaman lain
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .login-container {
            background: linear-gradient(to right, #28a745, #ffc107);
            height: 100vh;
        }
        .card {
            border-radius: 1rem;
            border: none;
        }
        .card-title {
            font-weight: 700;
            color: #343a40;
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            font-weight: 600;
            padding: .75rem 1.25rem;
            border-radius: .5rem;
        }
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        a {
            color: #28a745;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        /* .alert { margin-bottom: 20px; } /* Anda bisa menghapus ini juga jika tidak ada alert yang ditampilkan */
    </style>
</head>
<body>
    <div class="container-fluid login-container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-md-8 col-lg-5 col-xl-4">
                <div class="card shadow-lg p-4">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Login Akun Anda</h3>
                        <form action="" method="POST">
                            <!-- BAGIAN INI TELAH DIHILANGKAN -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Masukkan username" required>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan password" required>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                                <label class="form-check-label" for="rememberMe">Ingat saya</label>
                            </div>
                            <button type="submit" name="loginbtn" class="btn btn-primary w-100">Login</button>
                            <p class="text-center mt-3">Belum punya akun? <a href="./register.php">Daftar sekarang</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
