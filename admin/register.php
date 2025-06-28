<?php
session_start();

// Bagian koneksi database
$con = new mysqli("localhost", "root", "", "toko_elite");

// Check connection
if ($con->connect_errno) {
    echo "Failed to connect to MySQL: " . $con->connect_error;
    exit();
}

$success_message = "";
$error_message = "";

if (isset($_POST['registerbtn'])) {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $confirm_password = htmlspecialchars($_POST['confirm_password']);

    // Validasi input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = "Semua kolom harus diisi.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Konfirmasi password tidak cocok.";
    } elseif (strlen($password) < 6) { // Contoh: minimal 6 karakter
        $error_message = "Password minimal 6 karakter.";
    } else {
        // Cek apakah username sudah ada
        $stmt = $con->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Username sudah terdaftar. Silakan pilih username lain.";
        } else {
            // Hash password sebelum menyimpan ke database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Masukkan data pengguna baru
            $stmt = $con->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                $success_message = "Registrasi berhasil! Silakan <a href='login.php'>Login</a>.";
                // Opsional: Redirect langsung ke halaman login setelah berhasil
                // header('Location: login.php');
                // exit();
            } else {
                $error_message = "Terjadi kesalahan saat registrasi. Silakan coba lagi.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .register-container {
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
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid register-container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-md-8 col-lg-5 col-xl-4">
                <div class="card shadow-lg p-4">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Daftar Akun Baru</h3>
                        <form action="" method="POST">
                            <?php if (isset($error_message) && $error_message != ""): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error_message; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($success_message) && $success_message != ""): ?>
                                <div class="alert alert-success" role="alert">
                                    <?php echo $success_message; ?>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Masukkan username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan password" required>
                            </div>
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Konfirmasi password" required>
                            </div>
                            <button type="submit" name="registerbtn" class="btn btn-primary w-100">Daftar</button>
                            <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login di sini</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 