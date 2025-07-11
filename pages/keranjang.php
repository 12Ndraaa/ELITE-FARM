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
        /* Gaya spesifik untuk halaman keranjang.php */
        .cart-table th { 
            background-color: #e9ecef; 
            vertical-align: middle; /* Memastikan teks di tengah secara vertikal */
        }
        .cart-table td {
            vertical-align: middle; /* Memastikan konten sel di tengah secara vertikal */
        }
        .cart-item-image { 
            width: 80px; 
            height: 80px; 
            object-fit: cover; 
            border-radius: 8px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.1); /* Tambah sedikit bayangan */
        }
        .cart-total-box {
            background-color: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 30px;
        }
        .cart-total-box h4 { 
            font-weight: 600; 
            margin-bottom: 20px; 
            color: #343a40; /* Warna teks gelap */
        }
        .cart-total-box .total-row {
            font-size: 1.25rem;
            font-weight: 700;
            color: #28a745;
            border-top: 1px solid #dee2e6; /* Garis pemisah untuk total */
            padding-top: 15px;
            margin-top: 15px;
        }
        /* Style untuk tombol kuantitas (akan diimplementasikan nanti dengan JS) */
        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center; /* Pusatkan kontrol kuantitas */
        }
        .quantity-controls button {
            width: 35px; /* Sedikit lebih lebar */
            height: 35px; /* Sedikit lebih tinggi */
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-size: 1.2rem;
            line-height: 1;
            transition: all 0.2s ease;
        }
        .quantity-controls button:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .quantity-controls input {
            width: 70px; /* Lebih lebar untuk menampung angka */
            text-align: center;
            margin: 0 8px; /* Margin lebih besar */
            padding: 8px 5px; /* Padding lebih banyak */
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-weight: 500;
        }
        /* Styling untuk tombol di bagian bawah tabel */
        .btn-outline-primary {
            border-color: #007bff;
            color: #007bff;
        }
        .btn-outline-primary:hover {
            background-color: #007bff;
            color: white;
        }
        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            transition: all 0.3s ease;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include '../components/navbar.php'; // Path navbar disesuaikan ?>

    <div class="container container-main">
        <h2 class="mb-5 text-center">Keranjang Belanja Anda</h2>

        <?php if ($error_message && empty($cart_items)): ?>
            <div class="alert alert-info text-center py-4 rounded-3 shadow-sm" role="alert">
                <i class="fas fa-info-circle fa-2x mb-3 text-primary"></i>
                <h4 class="alert-heading mb-3">Keranjang Belanja Anda Kosong!</h4>
                <p><?php echo $error_message; ?></p>
                <hr>
                <p class="mb-0">Yuk, jelajahi produk kami dan isi keranjang Anda.</p>
                <a href="produk.php" class="btn btn-primary mt-3 px-4 py-2"><i class="fas fa-shopping-basket me-2"></i> Lanjutkan Belanja</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0 cart-table">
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
                                                        <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($item['nama']); ?></h6>
                                                        <small class="text-muted">Kode: <?php echo htmlspecialchars($item['kode']); ?></small><br>
                                                        <small class="text-muted">Satuan: <?php echo htmlspecialchars($item['satuan']); ?></small>
                                                    </td>
                                                    <td class="text-center text-nowrap">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                                    <td class="text-center">
                                                        <div class="quantity-controls">
                                                            <button class="btn btn-sm btn-outline-secondary decrease-qty" data-product-id="<?php echo htmlspecialchars($item['id']); ?>">-</button>
                                                            <input type="text" class="form-control form-control-sm text-center qty-input" value="<?php echo htmlspecialchars($item['quantity']); ?>" data-product-id="<?php echo htmlspecialchars($item['id']); ?>" readonly>
                                                            <button class="btn btn-sm btn-outline-secondary increase-qty" data-product-id="<?php echo htmlspecialchars($item['id']); ?>">+</button>
                                                        </div>
                                                    </td>
                                                    <td class="text-end fw-bold text-nowrap">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                                    <td class="text-center">
                                                        <button class="btn btn-danger btn-sm remove-item-btn" data-product-id="<?php echo htmlspecialchars($item['id']); ?>">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    Keranjang belanja Anda kosong.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <a href="produk.php" class="btn btn-outline-primary px-4 py-2"><i class="fas fa-arrow-left me-2"></i> Lanjutkan Belanja</a>
                        <button class="btn btn-outline-danger px-4 py-2" id="clear-cart-btn"><i class="fas fa-trash-alt me-2"></i> Kosongkan Keranjang</button>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card cart-total-box mt-4 mt-lg-0">
                        <h4 class="mb-4">Ringkasan Keranjang</h4>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                                Total Item
                                <span><?php echo count($cart_items); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center total-row bg-transparent">
                                Total Belanja
                                <span>Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></span>
                            </li>
                        </ul>
                        <button class="btn btn-success btn-lg mt-3 w-100"><i class="fas fa-money-check-alt me-2"></i> Lanjutkan ke Checkout</button>
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
        $('.total-row span').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));
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
        if (confirm('Apakah Anda yakin ingin mengosongkan seluruh keranjang? Tindakan ini tidak dapat dibatalkan.')) {
            $.ajax({
                url: '../clear_cart.php', // Path disesuaikan: naik 1 folder dari 'pages'
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        updateCartCount(response.cart_count); // Perbarui count di navbar (akan menjadi 0)

                        // Kosongkan tabel keranjang di halaman
                        $('.cart-table tbody').html('<tr><td colspan="6" class="text-center py-4 text-muted">Keranjang belanja Anda kosong.</td></tr>');
                        // Set total belanja menjadi 0
                        updateGrandTotal(0); // Kirim angka 0, biar diformat di fungsi
                        // Set total item menjadi 0
                        updateSummaryItemCount(0);

                        // Sembunyikan bagian tabel dan ringkasan jika keranjang kosong
                        $('.row').hide(); 
                        $('.container-main').append('<div class="alert alert-info text-center py-4 rounded-3 shadow-sm" role="alert"><i class="fas fa-info-circle fa-2x mb-3 text-primary"></i><h4 class="alert-heading mb-3">Keranjang Belanja Anda Kosong!</h4><p>Keranjang belanja Anda berhasil dikosongkan. Yuk, jelajahi produk kami dan isi keranjang Anda.</p><hr><p class="mb-0"><a href="produk.php" class="btn btn-primary mt-3 px-4 py-2"><i class="fas fa-shopping-basket me-2"></i> Lanjutkan Belanja</a></p></div>');

                    } else {
                        alert('Gagal mengosongkan keranjang: ' + (response.message || 'Terjadi kesalahan tidak diketahui.'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error (clear cart):", status, error);
                    alert('Terjadi kesalahan saat mengosongkan keranjang. Mohon coba lagi.');
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
            if (newQty < 0) newQty = 0; // Pastikan kuantitas tidak negatif
        } else { // increase-qty
            newQty = currentQty + 1;
        }

        // Jika kuantitas tidak berubah (misal sudah 0 dan dikurangi lagi)
        if (newQty === currentQty && $(this).hasClass('decrease-qty')) {
            return; // Jangan lakukan AJAX call jika tidak ada perubahan
        }

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
                    updateSummaryItemCount(response.cart_count); // Perbarui total item di ringkasan

                    if (response.removed) {
                        // Jika item dihapus (karena kuantitas menjadi 0)
                        qtyInput.closest('tr').remove(); // Hapus baris dari tabel
                        
                        // Periksa jika keranjang kosong setelah penghapusan
                        if (response.cart_count === 0) {
                            $('.cart-table tbody').html('<tr><td colspan="6" class="text-center py-4 text-muted">Keranjang belanja Anda kosong.</td></tr>');
                            // Sembunyikan bagian tabel dan ringkasan jika keranjang kosong
                            $('.row').hide();
                            $('.container-main').append('<div class="alert alert-info text-center py-4 rounded-3 shadow-sm" role="alert"><i class="fas fa-info-circle fa-2x mb-3 text-primary"></i><h4 class="alert-heading mb-3">Keranjang Belanja Anda Kosong!</h4><p>Produk berhasil dihapus dan keranjang Anda sekarang kosong. Yuk, jelajahi produk kami dan isi keranjang Anda.</p><hr><p class="mb-0"><a href="produk.php" class="btn btn-primary mt-3 px-4 py-2"><i class="fas fa-shopping-basket me-2"></i> Lanjutkan Belanja</a></p></div>');
                        }
                    } else {
                        // Jika kuantitas berhasil diubah
                        qtyInput.val(newQty); // Perbarui nilai input kuantitas
                        // Perbarui subtotal untuk item ini
                        qtyInput.closest('tr').find('td:eq(4)').text('Rp ' + new Intl.NumberFormat('id-ID').format(response.item_subtotal)); 
                    }
                } else {
                    alert('Gagal memperbarui kuantitas: ' + (response.message || 'Terjadi kesalahan tidak diketahui.'));
                    // Kembalikan kuantitas ke nilai sebelumnya jika gagal
                    qtyInput.val(currentQty); 
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error (update quantity):", status, error);
                alert('Terjadi kesalahan saat memperbarui kuantitas. Mohon coba lagi.');
                // Kembalikan kuantitas ke nilai sebelumnya jika error
                qtyInput.val(currentQty);
            }
        });
    });

    // --- Fungsionalitas Hapus Item Individu (menggunakan endpoint update_cart_quantity dengan qty 0) ---
    $('.remove-item-btn').on('click', function() {
        var productId = $(this).data('product-id');
        if (confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) {
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
                        
                        // Hapus baris produk dari tabel
                        $('.cart-table tbody').find('tr').filter(function(){
                            return $(this).find('.qty-input').data('product-id') == productId;
                        }).remove();

                        if (response.cart_count === 0) {
                            $('.cart-table tbody').html('<tr><td colspan="6" class="text-center py-4 text-muted">Keranjang belanja Anda kosong.</td></tr>');
                            // Sembunyikan bagian tabel dan ringkasan jika keranjang kosong
                            $('.row').hide();
                            $('.container-main').append('<div class="alert alert-info text-center py-4 rounded-3 shadow-sm" role="alert"><i class="fas fa-info-circle fa-2x mb-3 text-primary"></i><h4 class="alert-heading mb-3">Keranjang Belanja Anda Kosong!</h4><p>Produk berhasil dihapus dan keranjang Anda sekarang kosong. Yuk, jelajahi produk kami dan isi keranjang Anda.</p><hr><p class="mb-0"><a href="produk.php" class="btn btn-primary mt-3 px-4 py-2"><i class="fas fa-shopping-basket me-2"></i> Lanjutkan Belanja</a></p></div>');
                        }
                    } else {
                        alert('Gagal menghapus produk: ' + (response.message || 'Terjadi kesalahan tidak diketahui.'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error (remove item):", status, error);
                    alert('Terjadi kesalahan saat menghapus produk. Mohon coba lagi.');
                }
            });
        }
    });

    // Pada saat halaman dimuat atau setelah AJAX update, pastikan tampilan keranjang kosong sudah benar
    if (<?php echo json_encode(empty($cart_items)); ?>) {
        $('.row').hide();
    }
});
</script>
<!-- BOOTSTRAP SCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- JS -->
     <script src="./src/js/script.js"></script>
</body>
</html>