<?php
// Lokasi: ELITE-FARM/add_to_cart.php
session_start();
require_once 'connection.php'; // Sesuaikan path ini jika connection.php tidak langsung di root

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Invalid request.',
    'cart_count' => 0
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- SKENARIO 1: Menambahkan produk ke keranjang ---
    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity']; // Kuantitas yang ingin ditambahkan (biasanya 1 dari produk.php)

        if ($product_id > 0 && $quantity > 0) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            // Jika produk sudah ada di keranjang, tambahkan kuantitasnya. Jika belum, set kuantitas.
            $_SESSION['cart'][$product_id] = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] + $quantity : $quantity;
            
            $response['success'] = true;
            $response['message'] = 'Produk berhasil ditambahkan ke keranjang!';
        } else {
            $response['message'] = 'ID produk atau kuantitas tidak valid untuk ditambahkan.';
        }
    } 
    // --- SKENARIO 2: Hanya meminta jumlah keranjang (untuk inisialisasi navbar) ---
    elseif (isset($_POST['get_cart_count'])) {
        $response['success'] = true; // Anggap sukses jika hanya meminta hitungan
        $response['message'] = 'Cart count retrieved.';
    } else {
        $response['message'] = 'Parameter tidak lengkap atau tidak dikenal.';
    }

    // --- SELALU HITUNG ULANG TOTAL ITEM DI KERANJANG UNTUK RESPON ---
    $cart_count = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $qty) {
            $cart_count += $qty;
        }
    }
    $response['cart_count'] = $cart_count; // Kirim jumlah total item kembali
} else {
    $response['message'] = 'Metode request tidak diizinkan.';
}

echo json_encode($response);
exit();
?>