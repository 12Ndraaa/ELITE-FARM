<?php
session_start(); // Mulai sesi
require_once 'connection.php'; // Sertakan file koneksi database

header('Content-Type: application/json'); // Beri tahu browser bahwa responsnya adalah JSON

$response = [
    'success' => false,
    'message' => 'Invalid request.',
    'cart_count' => 0,
    'grand_total' => 0,
    'item_subtotal' => 0,
    'removed' => false // Menandakan apakah item dihapus (jika kuantitas menjadi 0)
];

if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $new_quantity = intval($_POST['quantity']);

    // Pastikan keranjang ada di sesi
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Pastikan product_id valid
    if ($product_id <= 0) {
        $response['message'] = 'Invalid product ID.';
        echo json_encode($response);
        exit();
    }

    // Jika kuantitas baru adalah 0, hapus item dari keranjang
    if ($new_quantity <= 0) {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $response['removed'] = true;
            $response['success'] = true;
            $response['message'] = 'Product removed from cart.';
        } else {
            $response['message'] = 'Product not found in cart to remove.';
        }
    } else {
        // Update kuantitas produk di keranjang
        $_SESSION['cart'][$product_id] = $new_quantity;
        $response['success'] = true;
        $response['message'] = 'Quantity updated successfully.';
    }

    // Sekarang, hitung ulang total keranjang dan subtotal item yang diubah
    $grand_total = 0;
    $total_items_in_cart = 0;
    $item_subtotal = 0;

    if (!empty($_SESSION['cart'])) {
        // Ambil semua ID produk dari keranjang sesi
        $product_ids_in_cart = array_keys($_SESSION['cart']);
        
        // Buat placeholder untuk query IN clause
        $placeholders = implode(',', array_fill(0, count($product_ids_in_cart), '?'));
        
        // Query untuk mengambil harga produk dari database
        $stmt = $con->prepare("SELECT id, harga FROM produk WHERE id IN ($placeholders)");
        
        // Bind parameter secara dinamis
        $types = str_repeat('i', count($product_ids_in_cart));
        $stmt->bind_param($types, ...$product_ids_in_cart);
        $stmt->execute();
        $result = $stmt->get_result();

        $product_prices = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $product_prices[$row['id']] = $row['harga'];
            }
            $stmt->close();
        }

        // Hitung ulang grand total dan subtotal item yang diubah
        foreach ($_SESSION['cart'] as $id => $qty) {
            if (isset($product_prices[$id])) {
                $price = $product_prices[$id];
                $sub = $price * $qty;
                $grand_total += $sub;
                $total_items_in_cart += $qty;

                // Jika ini item yang baru saja diubah kuantitasnya
                if ($id == $product_id) {
                    $item_subtotal = $sub;
                }
            }
        }
    }

    $response['cart_count'] = $total_items_in_cart;
    $response['grand_total'] = number_format($grand_total, 0, ',', '.'); // Format untuk tampilan
    $response['item_subtotal'] = number_format($item_subtotal, 0, ',', '.'); // Format untuk tampilan
}

echo json_encode($response);
$con->close();
exit();
?>