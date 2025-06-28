<?php
session_start();
require_once '../connection.php'; 

$product_details = null;

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']); 

    $stmt = $con->prepare("SELECT id, kode, nama, satuan, harga, image FROM produk WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $product_details = $result->fetch_assoc();
    } else {
        header('Location: ../produk.php');
        exit();
    }
    $stmt->close();
} else {
    header('Location: ../produk.php');
    exit();
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk: <?php echo htmlspecialchars($product_details['nama']); ?></title>
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

        .product-image { 
            max-width: 100%; 
            height: auto; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); 
        }

        .card-detail { 
            border-radius: 0.75rem; 
            box-shadow: 0 0 20px rgba(0,0,0,0.05); 
        }

        .product-info h3 { 
            color: #343a40; 
            font-weight: 600; 
            margin-bottom: 15px; 
        }

        .product-info p { 
            font-size: 1.1rem; 
            color: #555; 
            line-height: 1.6; 
        }

        .product-info .price { 
            font-size: 1.8rem; 
            font-weight: 700; 
            color: #28a745; 
            margin-bottom: 20px; 
        }

        .product-info .badge { 
            font-size: 0.9rem; 
            padding: 8px 12px; 
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .button-group .btn {
            flex: 1;
            margin: 0 5px; /* Add margin between buttons */
        }
    </style>
</head>
<body>
    <?php include '../components/navbar.php'; ?>

    <div class="container container-main">
        <?php if ($product_details): ?>
            <h2 class="mb-4 text-center">Detail Produk</h2>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card card-detail p-4">
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <?php if (!empty($product_details['image'])): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($product_details['image']); ?>" class="product-image" alt="<?php echo htmlspecialchars($product_details['nama']); ?>">
                                <?php else: ?>
                                    <div class="text-muted border p-4 rounded d-flex align-items-center justify-content-center" style="height: 250px;">
                                        <i class="fas fa-image fa-3x"></i><br>No Image
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 product-info">
                                <h3><?php echo htmlspecialchars($product_details['nama']); ?></h3>
                                <p class="text-muted">Kode Produk: <?php echo htmlspecialchars($product_details['kode']); ?></p>
                                <p>Satuan: <span class="badge bg-primary"><?php echo htmlspecialchars($product_details['satuan']); ?></span></p>
                                <p>Stok: <span class="badge bg-info"><?php echo htmlspecialchars($product_details['stock'] ?? 'N/A'); ?></span></p>
                                <p class="price">Harga: Rp <?php echo number_format(htmlspecialchars($product_details['harga']), 0, ',', '.'); ?></p>
                                <hr>
                                <div class="button-group">
                                    <button class="btn btn-success add-to-cart-btn" data-product-id="<?php echo htmlspecialchars($product_details['id']); ?>">
                                        <i class="fas fa-cart-plus me-1"></i> Tambahkan ke Keranjang
                                    </button>
                                    <a href="produk.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Produk</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">Produk tidak ditemukan.</div>
            <div class="text-center">
                <a href="produk.php" class="btn btn-primary">Kembali ke Daftar Produk</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../src/js/script.js"></script>

    <script>
    $(document).ready(function() {
        // Fungsi untuk memperbarui jumlah item di keranjang
        function updateCartCount(count) {
            $('.cart-count').text(count);
        }

        // Ambil jumlah keranjang saat halaman dimuat
        $.ajax({
            url: '../add_to_cart.php', 
            method: 'POST',
            data: { get_cart_count: true }, 
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
        $('.add-to-cart-btn').on('click', function() {
            var productId = $(this).data('product-id');

            $.ajax({
                url: '../add_to_cart.php', 
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
