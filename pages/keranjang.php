<?php
session_start();
require_once '../connection.php'; // Path disesuaikan karena keranjang.php berada di dalam folder 'pages'

$cart_items = [];
$grand_total = 0;
$error_message = "";

// Ambil data keranjang dari session
$session_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (!empty($session_cart)) {
    // Ambil semua ID produk dari keranjang sesi
    $product_ids = array_keys($session_cart);
    
    // Buat placeholder untuk query IN clause
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    // Query untuk mengambil detail produk berdasarkan ID yang ada di keranjang
    $stmt = $con->prepare("SELECT id, kode, nama, harga, image, satuan FROM produk WHERE id IN ($placeholders)");
    
    // Bind parameter secara dinamis
    $types = str_repeat('i', count($product_ids)); // 'i' untuk integer
    $stmt->bind_param($types, ...$product_ids);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $products_data = [];
        while ($row = $result->fetch_assoc()) {
            $products_data[$row['id']] = $row; // Simpan produk dalam array asosiatif dengan ID sebagai kunci
        }
        $stmt->close();

        // Gabungkan data produk dengan kuantitas dari sesi
        foreach ($session_cart as $product_id => $quantity) {
            if (isset($products_data[$product_id])) {
                $product = $products_data[$product_id];
                $subtotal = $product['harga'] * $quantity;
                $grand_total += $subtotal;

                $cart_items[] = [
                    'id' => $product['id'],
                    'kode' => $product['kode'],
                    'nama' => $product['nama'],
                    'harga' => $product['harga'],
                    'image' => $product['image'],
                    'satuan' => $product['satuan'],
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
            } else {
                // Produk tidak ditemukan di database, mungkin sudah dihapus
                // Anda bisa menghapusnya dari sesi di sini jika mau
                // unset($_SESSION['cart'][$product_id]);
            }
        }
    } else {
        $error_message = "Gagal mengambil detail produk dari database: " . $con->error;
    }
} else {
    $error_message = "Keranjang belanja Anda kosong.";
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - SmartFarm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../src/style/style.css"> 
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .container-main { margin-top: 100px; padding-bottom: 50px; }
        h2 { color: #343a40; margin-bottom: 30px; text-align: center; font-weight: 600; }
        .cart-table th { background-color: #e9ecef; }
        .cart-item-image { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
        .cart-total-box {
            background-color: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 30px;
        }
        .cart-total-box h4 { font-weight: 600; margin-bottom: 20px; }
        .cart-total-box .total-row {
            font-size: 1.25rem;
            font-weight: 700;
            color: #28a745;
        }
        /* Style untuk tombol kuantitas (akan diimplementasikan nanti dengan JS) */
        .quantity-controls {
            display: flex;
            align-items: center;
        }
        .quantity-controls button {
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-size: 1.2rem;
            line-height: 1;
        }
        .quantity-controls input {
            width: 60px;
            text-align: center;
            margin: 0 5px;
            padding: 5px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include '../components/navbar.php'; // Path navbar disesuaikan ?>

    <div class="container container-main">
        <h2 class="mb-5">Keranjang Belanja Anda</h2>

        <?php if ($error_message && empty($cart_items)): ?>
            <div class="alert alert-info text-center" role="alert">
                <?php echo $error_message; ?>
                <br>
                <a href="produk.php" class="btn btn-primary mt-3"><i class="fas fa-shopping-basket me-1"></i> Lanjutkan Belanja</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 cart-table">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-center">Gambar</th>
                                            <th scope="col">Produk</th>
                                            <th scope="col" class="text-center">Harga</th>
                                            <th scope="col" class="text-center">Kuantitas</th>
                                            <th scope="col" class="text-end">Subtotal</th>
                                            <th scope="col" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($cart_items)): ?>
                                            <?php foreach ($cart_items as $item): ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <?php if (!empty($item['image'])): ?>
                                                            <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" class="cart-item-image" alt="<?php echo htmlspecialchars($item['nama']); ?>">
                                                        <?php else: ?>
                                                            <div class="text-muted text-center" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border: 1px dashed #ccc; border-radius: 8px;">
                                                                <i class="fas fa-image fa-2x"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($item['nama']); ?></h6>
                                                        <small class="text-muted">Kode: <?php echo htmlspecialchars($item['kode']); ?></small><br>
                                                        <small class="text-muted">Satuan: <?php echo htmlspecialchars($item['satuan']); ?></small>
                                                    </td>
                                                    <td class="text-center">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                                    <td class="text-center">
                                                        <div class="quantity-controls">
                                                            <button class="btn btn-sm btn-outline-secondary decrease-qty" data-product-id="<?php echo htmlspecialchars($item['id']); ?>">-</button>
                                                            <input type="text" class="form-control form-control-sm text-center qty-input" value="<?php echo htmlspecialchars($item['quantity']); ?>" data-product-id="<?php echo htmlspecialchars($item['id']); ?>" readonly>
                                                            <button class="btn btn-sm btn-outline-secondary increase-qty" data-product-id="<?php echo htmlspecialchars($item['id']); ?>">+</button>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                                    <td class="text-center">
                                                        <button class="btn btn-danger btn-sm remove-item-btn" data-product-id="<?php echo htmlspecialchars($item['id']); ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    Keranjang belanja Anda kosong.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="produk.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Lanjutkan Belanja</a>
                        <button class="btn btn-outline-danger" id="clear-cart-btn"><i class="fas fa-trash-alt me-1"></i> Kosongkan Keranjang</button>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card cart-total-box mt-4 mt-lg-0">
                        <h4 class="mb-4">Ringkasan Keranjang</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total Item
                                <span><?php echo count($cart_items); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center total-row">
                                Total Belanja
                                <span>Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></span>
                            </li>
                        </ul>
                        <button class="btn btn-success btn-lg mt-4 w-100"><i class="fas fa-money-check-alt me-2"></i> Lanjutkan ke Checkout</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../src/js/script.js"></script>

<script>
$(document).ready(function() {
    // Fungsi untuk memperbarui jumlah item di keranjang di navbar
    function updateCartCount(count) {
        $('.cart-count').text(count);
    }

    // Fungsi untuk memperbarui Grand Total di halaman
    function updateGrandTotal(total) {
        $('.total-row span').text('Rp ' + total);
    }

    // Fungsi untuk memperbarui total item di ringkasan
    function updateSummaryItemCount(count) {
        $('.list-group-item:first-child span').text(count);
    }

    // --- Ambil jumlah keranjang saat halaman keranjang.php dimuat ---
    $.ajax({
        url: '../add_to_cart.php', // Path disesuaikan: naik 1 folder dari 'pages'
        method: 'POST',
        data: { get_cart_count: true }, // Kirim parameter untuk meminta jumlah keranjang saja
        dataType: 'json',
        success: function(response) {
            if (response.success) { // Periksa properti success dari respons JSON
                updateCartCount(response.cart_count);
            } else {
                console.error("Failed to get initial cart count:", response.message || "Unknown error");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error getting initial cart count:", error);
        }
    });

    // --- Fungsionalitas Kosongkan Keranjang ---
    $('#clear-cart-btn').on('click', function() {
        if (confirm('Apakah Anda yakin ingin mengosongkan seluruh keranjang?')) {
            $.ajax({
                url: '../clear_cart.php', // Path disesuaikan: naik 1 folder dari 'pages'
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        updateCartCount(response.cart_count); // Perbarui count di navbar (akan menjadi 0)

                        // Kosongkan tabel keranjang di halaman
                        $('.cart-table tbody').html('<tr><td colspan="6" class="text-center py-4">Keranjang belanja Anda kosong.</td></tr>');
                        // Set total belanja menjadi 0
                        updateGrandTotal('0');
                        // Set total item menjadi 0
                        updateSummaryItemCount('0');

                        alert('Keranjang belanja berhasil dikosongkan!');
                    } else {
                        alert('Gagal mengosongkan keranjang.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error (clear cart):", status, error);
                    alert('Terjadi kesalahan saat mengosongkan keranjang.');
                }
            });
        }
    });

    // --- Fungsionalitas Update Kuantitas Item ---
    $('.decrease-qty, .increase-qty').on('click', function() {
        var productId = $(this).data('product-id');
        var qtyInput = $(this).siblings('.qty-input');
        var currentQty = parseInt(qtyInput.val());
        var newQty;

        if ($(this).hasClass('decrease-qty')) {
            newQty = currentQty - 1;
        } else { // increase-qty
            newQty = currentQty + 1;
        }

        // Batasi kuantitas minimum menjadi 1 jika tombol kurang ditekan, kecuali jika menjadi 0 untuk dihapus
        // Jika Anda ingin 0 berarti hapus, maka biarkan newQty bisa 0
        if (newQty < 0) newQty = 0; // Pastikan kuantitas tidak negatif

        // Kirim permintaan AJAX untuk memperbarui kuantitas
        $.ajax({
            url: '../update_cart_quantity.php', // Path disesuaikan
            method: 'POST',
            data: { 
                product_id: productId, 
                quantity: newQty 
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateCartCount(response.cart_count); // Update count di navbar
                    updateGrandTotal(response.grand_total); // Update grand total

                    if (response.removed) {
                        // Jika item dihapus (karena kuantitas menjadi 0)
                        qtyInput.closest('tr').remove(); // Hapus baris dari tabel
                        alert('Produk berhasil dihapus dari keranjang.');
                        // Periksa jika keranjang kosong setelah penghapusan
                        if (response.cart_count === 0) {
                            $('.cart-table tbody').html('<tr><td colspan="6" class="text-center py-4">Keranjang belanja Anda kosong.</td></tr>');
                        }
                    } else {
                        // Jika kuantitas berhasil diubah
                        qtyInput.val(newQty); // Perbarui nilai input kuantitas
                        // Perbarui subtotal untuk item ini
                        qtyInput.closest('tr').find('td:eq(4)').text('Rp ' + response.item_subtotal); 
                        alert('Kuantitas produk berhasil diperbarui.');
                    }
                    updateSummaryItemCount(response.cart_count); // Perbarui total item di ringkasan

                } else {
                    alert('Gagal memperbarui kuantitas: ' + (response.message || 'Terjadi kesalahan tidak diketahui.'));
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error (update quantity):", status, error);
                alert('Terjadi kesalahan saat memperbarui kuantitas.');
            }
        });
    });

    // --- Fungsionalitas Hapus Item Individu (akan diimplementasikan selanjutnya) ---
    // ... (biarkan placeholder ini untuk saat ini, atau implementasikan jika mau) ...
    // Contoh untuk tombol remove (membutuhkan endpoint PHP terpisah)
    $('.remove-item-btn').on('click', function() {
        var productId = $(this).data('product-id');
        if (confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) {
            // Ini akan memerlukan file PHP baru, misal: remove_from_cart.php
            // Untuk saat ini, fungsi update_cart_quantity.php dengan new_quantity = 0 sudah bisa menghapus
            // Jadi, Anda bisa memanggil fungsi yang sama di sini
            
            // Contoh penggunaan update_cart_quantity untuk hapus:
            $.ajax({
                url: '../update_cart_quantity.php',
                method: 'POST',
                data: { product_id: productId, quantity: 0 }, // Set quantity to 0 to remove
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.removed) {
                        updateCartCount(response.cart_count);
                        updateGrandTotal(response.grand_total);
                        updateSummaryItemCount(response.cart_count);
                        
                        $('#clear-cart-btn').siblings('a').show(); // Show continue shopping button if hidden
                        
                        $('.cart-table tbody').find('tr').filter(function(){
                            return $(this).find('.qty-input').data('product-id') == productId;
                        }).remove();

                        if (response.cart_count === 0) {
                            $('.cart-table tbody').html('<tr><td colspan="6" class="text-center py-4">Keranjang belanja Anda kosong.</td></tr>');
                        }
                        alert('Produk berhasil dihapus dari keranjang.');
                    } else {
                        alert('Gagal menghapus produk: ' + (response.message || 'Terjadi kesalahan tidak diketahui.'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error (remove item):", status, error);
                    alert('Terjadi kesalahan saat menghapus produk.');
                }
            });
        }
    });

});
</script>
</body>
</html>