<?php
// Lokasi: ELITE-FARM/clear_cart.php
session_start();
// Tidak perlu require_once 'connection.php' karena tidak berinteraksi dengan DB

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Gagal mengosongkan keranjang.', 'cart_count' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']); // Menghapus seluruh array keranjang dari session
        $response['success'] = true;
        $response['message'] = 'Keranjang berhasil dikosongkan.';
        $response['cart_count'] = 0; // Pastikan hitungan menjadi 0
    } else {
        // Keranjang sudah kosong, anggap saja berhasil
        $response['success'] = true; 
        $response['message'] = 'Keranjang sudah kosong.';
        $response['cart_count'] = 0;
    }
} else {
    $response['message'] = 'Metode request tidak diizinkan.';
}

echo json_encode($response);
exit();
?>