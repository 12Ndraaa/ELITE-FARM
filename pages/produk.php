<?php
session_start(); // Mulai sesi
require_once '../connection.php'; 

$products = []; 
$error_message = "";

$result = $con->query("SELECT id, kode, nama, satuan, harga, image FROM produk ORDER BY id DESC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $result->free(); 
} else {
    $error_message = "Gagal mengambil data produk: " . $con->error;
}

$con->close(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk - SmartFarm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../src/style/style.css"> 
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .container-main {
            margin-top: 100px;
            padding-bottom: 50px;
        }
        h2 {
            color: #343a40;
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
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .product-card img {
            width: 100%;
            height: 200px; 
            object-fit: cover; 
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }
        .product-card .card-body {
            padding: 20px;
        }
        .product-card .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 10px;
        }
        .product-card .card-text {
            font-size: 1rem;
            color: #6c757d;
        }
        .product-card .price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #28a745;
            margin-top: 15px;
        }
        .btn-detail {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
            transition: background-color 0.3s ease;
        }
        .btn-detail:hover {
            background-color: #0056b3;
            border-color: #004b9e;
        }
        .btn-add-to-cart {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }
        .btn-add-to-cart:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
</head>
<body>
    <?php include '../components/navbar.php'; ?>

    <div class="container container-main">
        <h2 class="mb-5">Produk Kami</h2>

        <?php if ($error_message): ?>
            <div class="alert alert-danger text-center" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($products)): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card h-100 product-card">
                            <?php if (!empty($product['image'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['nama']); ?>">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;">
                                    <i class="fas fa-image fa-4x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['nama']); ?></h5>
                                <p class="card-text text-muted">Kode: <?php echo htmlspecialchars($product['kode']); ?></p>
                                <p class="card-text">Satuan: <?php echo htmlspecialchars($product['satuan']); ?></p>
                                <p class="price mt-auto">Rp <?php echo number_format(htmlspecialchars($product['harga']), 0, ',', '.'); ?></p>
                                <div class="d-flex justify-content-between mt-3">
                                    <a href="detail-produk.php?id=<?php echo htmlspecialchars($product['id']); ?>" class="btn btn-detail flex-grow-1 me-2">
                                        <i class="fas fa-info-circle me-1"></i> Detail
                                    </a>
                                    <button class="btn btn-success btn-add-to-cart flex-grow-1" data-product-id="<?php echo htmlspecialchars($product['id']); ?>">
                                        <i class="fas fa-cart-plus me-1"></i> Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Belum ada produk yang tersedia saat ini.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="../src/js/script.js"></script>

    <script>
    $(document).ready(function() {
        // Fungsi untuk memperbarui jumlah item di keranjang
        function updateCartCount(count) {
            $('.cart-count').text(count);
        }

        // Ambil jumlah keranjang saat halaman dimuat
        $.ajax({
            url: '../add_to_cart.php', // Path disesuaikan, karena produk.php ada di 'pages'
            method: 'POST',
            data: { get_cart_count: true }, // Kirim parameter untuk meminta jumlah keranjang saja
            dataType: 'json',
            success: function(response) {
                if (response.success !== undefined) { 
                    updateCartCount(response.cart_count);
                } else { 
                    updateCartCount(response.cart_count);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error getting initial cart count:", error);
            }
        });

        // Handle klik tombol 'Tambah ke Keranjang'
        $('.btn-add-to-cart').on('click', function() {
            var productId = $(this).data('product-id');
            // var quantity = 1; // Anda bisa tambahkan input quantity jika diperlukan

            $.ajax({
                url: '../add_to_cart.php', // Path disesuaikan
                method: 'POST',
                data: { 
                    product_id: productId, 
                    quantity: 1 
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        updateCartCount(response.cart_count);
                        alert('Produk berhasil ditambahkan ke keranjang!');
                    } else {
                        alert('Gagal menambahkan produk ke keranjang.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    alert('Terjadi kesalahan saat menambahkan produk ke keranjang.');
                }
            });
        });
    });
    </script>
</body>
</html>