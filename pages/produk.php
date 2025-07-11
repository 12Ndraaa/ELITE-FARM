<?php
// Lokasi: ELITE-FARM/pages/produk.php

// PENTING: session_start() HARUS dipanggil di awal setiap file PHP
// yang membutuhkan fitur session (seperti untuk login/logout atau keranjang).
// Pastikan tidak ada output HTML sebelum ini.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan path ke connection.php sudah benar, relatif dari lokasi produk.php
require_once '../connection.php';

$products = [];
$error_message = "";

// Periksa koneksi sebelum melakukan query
if ($con->connect_error) {
    $error_message = "Koneksi database gagal: " . $con->connect_error;
} else {
    // Query untuk mengambil data produk
    // Menggunakan prepared statement adalah praktik terbaik untuk keamanan
    $stmt = $con->prepare("SELECT id, kode, nama, satuan, harga, image FROM produk ORDER BY id DESC");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            $result->free(); // Bebaskan hasil query
        } else {
            $error_message = "Gagal mengambil data produk: " . $stmt->error;
        }
        $stmt->close(); // Tutup statement
    } else {
        $error_message = "Gagal menyiapkan statement: " . $con->error;
    }
}

$con->close(); // Tutup koneksi database setelah selesai
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk - SmartFarm</title>

    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="../src/style/style.css">

    <style>
        h2 {
            color: #343a40; /* Dark text color */
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
        }
        .product-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            height: 100%; /* Important for equal height cards in grid */
            display: flex;
            flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .product-image-container { /* Container for image or placeholder */
            width: 100%;
            height: 200px; /* Fixed height for image area */
            overflow: hidden;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e9ecef; /* Background color if no image */
        }
        .product-image { /* Style for the actual image */
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ensures image fills area without distortion */
        }
        .no-image-placeholder { /* Style for placeholder if no image */
            text-align: center;
            color: #6c757d;
            padding: 20px; /* Add padding so the icon isn't too cramped */
        }
        .product-card .card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1; /* Allows card-body to fill remaining space */
        }
        .product-card .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 10px;
        }
        .product-card .card-text {
            font-size: 0.95rem; /* Slightly smaller */
            color: #6c757d;
            margin-bottom: 5px; /* Little spacing between text details */
        }
        .product-card .price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #28a745; /* SmartFarm green */
            margin-top: auto; /* Pushes price to the bottom of card-body */
            margin-bottom: 15px; /* Little spacing above buttons */
        }
        .btn-detail {
            background-color: #17a2b8; /* Light blue */
            border-color: #17a2b8;
            color: white;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-weight: 500;
            border-radius: 0.5rem;
        }
        .btn-detail:hover {
            background-color: #138496;
            border-color: #117a8b;
            transform: translateY(-2px);
        }
        .btn-add-to-cart {
            background-color: #28a745; /* SmartFarm green */
            border-color: #28a745;
            color: white;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-weight: 500;
            border-radius: 0.5rem;
        }
        .btn-add-to-cart:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <?php include '../components/navbar.php'; // Memuat navbar ?>

    <div class="container container-main">
        <h2 class="mb-5">Produk Kami</h2>

        <?php if ($error_message): ?>
            <div class="alert alert-danger text-center" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($products) && !$error_message): ?>
            <div class="alert alert-info text-center" role="alert">
                Belum ada produk yang tersedia saat ini.
            </div>
        <?php elseif (!empty($products)): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card product-card">
                            <div class="product-image-container">
                                <?php
                                $image_path = '../uploads/' . $product['image'];
                                if (!empty($product['image']) && file_exists($image_path)):
                                ?>
                                    <img src="<?php echo htmlspecialchars($image_path); ?>" class="product-image" alt="<?php echo htmlspecialchars($product['nama']); ?>">
                                <?php else: ?>
                                    <div class="no-image-placeholder">
                                        <i class="fas fa-box-open fa-4x text-muted"></i>
                                        <p class="text-muted mt-2">No Image</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['nama']); ?></h5>
                                <p class="card-text text-muted small">Kode: <?php echo htmlspecialchars($product['kode']); ?></p>
                                <p class="card-text small">Satuan: <?php echo htmlspecialchars($product['satuan']); ?></p>
                                <p class="price">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
                                <div class="d-flex justify-content-between mt-3">
                                    <a href="detail-produk.php?id=<?php echo htmlspecialchars($product['id']); ?>" class="btn btn-detail flex-grow-1 me-2">
                                        <i class="fas fa-info-circle me-1"></i> Detail
                                    </a>
                                    <button
                                        class="btn btn-add-to-cart flex-grow-1"
                                        data-product-id="<?php echo htmlspecialchars($product['id']); ?>"
                                        data-product-name="<?php echo htmlspecialchars($product['nama']); ?>"
                                    >
                                        <i class="fas fa-cart-plus me-1"></i> Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../components/footer.php'; // Memuat footer ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <script>
    $(document).ready(function() {
        function updateNavbarCartCount(count) {
            $('.cart-count').text(count);
        }

        $.ajax({
            url: '../add_to_cart.php',
            method: 'POST',
            data: { get_cart_count: true },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateNavbarCartCount(response.cart_count);
                } else {
                    console.error("Failed to get initial cart count for navbar:", response.message || "Unknown error");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error getting initial cart count for navbar:", status, error);
            }
        });

        $('.btn-add-to-cart').on('click', function(e) {
            e.preventDefault();

            var productId = $(this).data('product-id');
            var productName = $(this).data('product-name');
            var quantity = 1;

            $.ajax({
                url: '../add_to_cart.php',
                method: 'POST',
                data: {
                    product_id: productId,
                    quantity: quantity
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        updateNavbarCartCount(response.cart_count);
                        alert('Produk "' + productName + '" berhasil ditambahkan ke keranjang!');
                    } else {
                        alert('Gagal menambahkan produk: ' + (response.message || 'Terjadi kesalahan tidak diketahui.'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error (add to cart):", status, error);
                    alert('Terjadi kesalahan saat menambahkan produk ke keranjang. Mohon coba lagi.');
                }
            });
        });
    });
    </script>
    
    <!-- BOOTSTRAP SCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- JS -->
     <script src="../src/js/script.js"></script>
</body>
</html>