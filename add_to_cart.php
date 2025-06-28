<?php
session_start(); // Mulai sesi untuk menyimpan data keranjang

// Sertakan file koneksi database jika diperlukan (misalnya untuk validasi produk_id)
// require_once 'connection.php'; 

header('Content-Type: application/json'); // Beri tahu browser bahwa responsnya adalah JSON

$response = ['success' => false, 'cart_count' => 0];

// --- Hitung total item di keranjang terlebih dahulu ---
// Logika ini dibutuhkan untuk semua jenis respons (baik add_to_cart atau get_cart_count)
$total_items_in_cart = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $total_items_in_cart += $qty;
    }
}
$response['cart_count'] = $total_items_in_cart;

// --- Logika untuk hanya mendapatkan jumlah keranjang (saat halaman dimuat) ---
// Jika parameter 'get_cart_count' ada, kita hanya perlu mengembalikan jumlah keranjang
if (isset($_POST['get_cart_count']) && $_POST['get_cart_count'] === 'true') {
    $response['success'] = true; // Beri tanda sukses karena ini hanya permintaan untuk mendapatkan data
    echo json_encode($response);
    exit(); // Hentikan eksekusi skrip di sini
}

// --- Logika untuk menambahkan produk ke keranjang (jika ada product_id) ---
// Bagian ini hanya akan dieksekusi jika bukan hanya permintaan 'get_cart_count'
if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1; // Default quantity 1

    if ($product_id > 0) { // Pastikan product_id valid
        // Inisialisasi keranjang jika belum ada di session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Tambahkan produk ke keranjang atau perbarui jumlahnya
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }

        $response['success'] = true; // Beri tanda sukses karena produk berhasil ditambahkan
        
        // --- Hitung ulang total item di keranjang setelah penambahan ---
        // Penting: Hitung ulang setelah $_SESSION['cart'] diperbarui
        $total_items_in_cart = 0;
        foreach ($_SESSION['cart'] as $qty) {
            $total_items_in_cart += $qty;
        }
        $response['cart_count'] = $total_items_in_cart;

    } else {
        // Product ID tidak valid
        $response['message'] = "ID produk tidak valid.";
    }
} else {
    // Jika tidak ada product_id yang dikirim dan bukan get_cart_count
    $response['message'] = "Tidak ada ID produk yang diberikan.";
}


echo json_encode($response); // Kirim respons JSON
exit();
?>