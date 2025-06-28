<?php
session_start(); // Mulai sesi

header('Content-Type: application/json'); // Beri tahu browser bahwa responsnya adalah JSON

$response = ['success' => false, 'cart_count' => 0];

// Kosongkan keranjang di sesi
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']); // Menghapus seluruh variabel sesi 'cart'
    // Atau bisa juga: $_SESSION['cart'] = []; // Mengatur keranjang menjadi array kosong
}

// Setelah dikosongkan, jumlah item di keranjang pasti 0
$response['success'] = true;
$response['cart_count'] = 0; // Karena sudah dikosongkan

echo json_encode($response); // Kirim respons JSON
exit();
?>