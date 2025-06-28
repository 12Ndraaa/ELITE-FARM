<?php
// PENTING: session_start() HARUS dipanggil di awal setiap file PHP
// yang meng-include navbar ini (misalnya di index.php, produk.php, dll.).
// Contoh: Di awal file index.php Anda, tambahkan:
// <?php session_start();
// Jika tidak, Anda bisa mengaktifkan baris di bawah ini,
// tapi praktik terbaik adalah di file utama yang memanggil navbar ini.
// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartFarm Navbar</title>
    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="../src/style/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fadeInDown" href="../index.php">
                <i class="fas fa-leaf me-2"></i>Smart<span>Farm</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item fadeInDown" style="animation-delay: 0.2s;">
                        <a class="nav-link" href="../index.php"><i class="fas fa-home me-1"></i>Beranda</a>
                    </li>
                    <li class="nav-item fadeInDown" style="animation-delay: 0.3s;">
                        <a class="nav-link" href="../index.php#features"><i class="fas fa-lightbulb me-1"></i>Fitur</a>
                    </li>
                    <li class="nav-item fadeInDown" style="animation-delay: 0.4s;">
                        <a class="nav-link" href="../pages/produk.php"><i class="fas fa-box-open me-1"></i>Produk</a>
                    </li>
                    <li class="nav-item fadeInDown" style="animation-delay: 0.5s;">
                        <?php
                        // Tampilkan link "Manajemen Produk" hanya jika pengguna sudah login DAN memiliki role 'admin'
                        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                            echo '<a class="nav-link" href="./admin/manajemen-produk.php"><i class="fas fa-cogs me-1"></i>Manajemen Produk</a>';
                        }
                        ?>
                    </li>
                    <li class="nav-item fadeInDown" style="animation-delay: 0.6s;">
                        <a class="nav-link" href="#contact"><i class="fas fa-envelope me-1"></i>Kontak</a>
                    </li>
                </ul>

                <form class="d-flex search-form fadeInDown" style="animation-delay: 0.7s;">
                    <input class="form-control me-2" type="search" placeholder="Cari produk..." aria-label="Search">
                    <button class="btn search-btn" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <div class="fadeInDown" style="animation-delay: 0.8s;">
                    <a href="../pages/keranjang.php" class="cart-icon me-4"> <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
                    </a>
                </div>

                <div class="d-flex align-items-center fadeInDown" style="animation-delay: 0.9s;">
                    <?php
                    // Logika PHP untuk menampilkan tombol Login/Register atau tombol Logout
                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                        // Jika pengguna sudah login, tampilkan nama pengguna dan tombol Logout
                        echo '<span class="navbar-text me-2">Halo, ' . htmlspecialchars($_SESSION['username']) . '!</span>';
                        echo '<a href="../admin/logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>';
                    } else {
                        // Jika pengguna belum login, tampilkan tombol Login dan Register
                        echo '<a href="./admin/login.php" class="btn btn-outline-success btn-sm me-1"><i class="fas fa-sign-in-alt me-1"></i> Login</a>';
                        echo '<a href="./admin/register.php" class="btn btn-success btn-sm"><i class="fas fa-user-plus me-1"></i> Register</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>
    <!-- BOOTSTRAP SCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- JS -->
    <script src="../src/js/script.js"></script>
</body>
</html>
