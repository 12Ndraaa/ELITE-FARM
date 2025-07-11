<?php
// ELITE-FARM/components/navbar.php

// Pastikan session_start() sudah ada di file utama yang meng-include navbar ini (misal: produk.php, keranjang.php)
// Jika tidak, tambahkan di sini:
// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }

// Hitung total kuantitas dari session cart untuk ditampilkan di navbar
$cart_total_quantity = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_total_quantity += $qty;
    }
}
?>
<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand fadeInDown" href="/ELITE-FARM/index.php">
            <i class="fas fa-leaf me-2"></i>Smart<span>Farm</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto">
                <li class="nav-item fadeInDown" style="animation-delay: 0.2s;">
                    <a class="nav-link" href="/ELITE-FARM/index.php"><i class="fas fa-home me-1"></i>Beranda</a>
                </li>
                <li class="nav-item fadeInDown" style="animation-delay: 0.3s;">
                    <a class="nav-link" href="/ELITE-FARM/pages/fitur.php"><i class="fas fa-lightbulb me-1"></i>Fitur</a>
                </li>
                <li class="nav-item fadeInDown" style="animation-delay: 0.4s;">
                    <a class="nav-link" href="/ELITE-FARM/pages/produk.php"><i class="fas fa-box-open me-1"></i>Produk</a>
                </li>
                <li class="nav-item fadeInDown" style="animation-delay: 0.5s;">
                    <?php
                    // Tampilkan link "Manajemen Produk" hanya jika pengguna sudah login DAN memiliki role 'admin'
                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                        echo '<a class="nav-link" href="/ELITE-FARM/admin/manajemen-produk.php"><i class="fas fa-cogs me-1"></i>Manajemen Produk</a>';
                    }
                    ?>
                </li>
                <li class="nav-item fadeInDown" style="animation-delay: 0.6s;">
                    <a class="nav-link" href="/ELITE-FARM/pages/kontak.php"><i class="fas fa-envelope me-1"></i>Kontak</a>
                </li>
            </ul>

            <form class="d-flex search-form fadeInDown" style="animation-delay: 0.7s;" action="/ELITE-FARM/pages/produk.php" method="GET">
                <input class="form-control me-2" type="search" name="search" placeholder="Cari produk..." aria-label="Search">
                <button class="btn search-btn" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <div class="fadeInDown" style="animation-delay: 0.8s;">
                <a href="/ELITE-FARM/pages/keranjang.php" class="cart-icon me-4"> 
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count"><?php echo $cart_total_quantity; ?></span>
                </a>
            </div>

            <div class="d-flex align-items-center fadeInDown" style="animation-delay: 0.9s;">
                <?php
                // Logika PHP untuk menampilkan tombol Login/Register atau tombol Logout
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                    // Jika pengguna sudah login, tampilkan nama pengguna dan tombol Logout
                    echo '<span class="navbar-text me-2">Halo, ' . htmlspecialchars($_SESSION['username']) . '!</span>';
                    echo '<a href="/ELITE-FARM/admin/logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>';
                } else {
                    // Jika pengguna belum login, tampilkan tombol Login dan Register
                    echo '<a href="/ELITE-FARM/admin/login.php" class="btn btn-outline-success btn-sm me-1"><i class="fas fa-sign-in-alt me-1"></i> Login</a>';
                    echo '<a href="/ELITE-FARM/admin/register.php" class="btn btn-success btn-sm"><i class="fas fa-user-plus me-1"></i> Register</a>';
                }
                ?>
            </div>
        </div>
    </div>
</nav>

<script>
    // Fungsi untuk memperbarui jumlah item di keranjang di navbar
    function updateNavbarCartCount(count) {
        // console.log('DEBUG: updateNavbarCartCount called with count:', count); // Debugging
        // Pastikan ini menargetkan SPAN dengan class 'cart-count' yang berisi angka
        $('.cart-count').text(count);
    }

    $(document).ready(function() {
        // --- 1. Ambil jumlah keranjang saat halaman dimuat ---
        $.ajax({
            url: '../add_to_cart.php', // Path relatif dari pages/
            method: 'POST',
            data: { get_cart_count: true },
            dataType: 'json',
            success: function(response) {
                // console.log('DEBUG: Initial AJAX response:', response);
                if (response.success) {
                    updateNavbarCartCount(response.cart_count);
                } else {
                    console.error("Failed to get initial cart count:", response.message || "Unknown error");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error getting initial cart count:", status, error);
            }
        });

        // --- 2. Fungsionalitas "Tambah ke Keranjang" (Hanya di produk.php) ---
        // Jika kode ini ada di keranjang.php, hapus saja.
        $('.add-to-cart-btn').on('click', function(e) {
            e.preventDefault();

            var productId = $(this).data('product-id');
            var quantity = 1;

            // console.log(`DEBUG: Adding product ID: ${productId} with quantity ${quantity}`);

            $.ajax({
                url: '../add_to_cart.php',
                method: 'POST',
                data: { product_id: productId, quantity: quantity },
                dataType: 'json',
                success: function(response) {
                    // console.log('DEBUG: Add to cart AJAX response:', response);
                    if (response.success) {
                        updateNavbarCartCount(response.cart_count);
                        alert(response.message);
                    } else {
                        alert('Gagal menambahkan produk ke keranjang: ' + (response.message || 'Terjadi kesalahan tidak diketahui.'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error (add to cart):", status, error);
                    alert('Terjadi kesalahan saat menambahkan produk ke keranjang. Mohon coba lagi.');
                }
            });
        });

        // --- Fungsionalitas lainnya dari keranjang.php (jika ini file keranjang.php) ---
        // Contoh: Clear Cart Button, Increase/Decrease Quantity
        // Pastikan kode ini hanya ada di keranjang.php
        // ... (kode dari keranjang.php sebelumnya untuk update quantity dan clear cart) ...
    });
    </script>