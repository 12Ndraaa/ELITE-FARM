<?php
// Lokasi: ELITE-FARM/update_cart_quantity.php
session_start();
require_once 'connection.php'; // Sesuaikan path ini jika connection.php tidak langsung di root

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Invalid request.',
    'cart_count' => 0,
    'grand_total' => 0,
    'item_subtotal' => 0,
    'removed' => false // Menandakan apakah item dihapus (jika kuantitas menjadi 0)
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $new_quantity = intval($_POST['quantity']);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if ($product_id <= 0) {
        $response['message'] = 'Invalid product ID.';
        echo json_encode($response);
        exit();
    }

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
        $_SESSION['cart'][$product_id] = $new_quantity;
        $response['success'] = true;
        $response['message'] = 'Quantity updated successfully.';
    }

    // Hitung ulang total keranjang dan subtotal item yang diubah
    $grand_total = 0;
    $total_items_in_cart = 0;
    $item_subtotal = 0;

    if (!empty($_SESSION['cart'])) {
        $product_ids_in_cart = array_keys($_SESSION['cart']);
        
        // Hanya query database jika ada produk di keranjang
        if (!empty($product_ids_in_cart)) {
            $placeholders = implode(',', array_fill(0, count($product_ids_in_cart), '?'));
            $stmt = $con->prepare("SELECT id, harga FROM produk WHERE id IN ($placeholders)");
            
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
        }

        foreach ($_SESSION['cart'] as $id => $qty) {
            if (isset($product_prices[$id])) {
                $price = $product_prices[$id];
                $sub = $price * $qty;
                $grand_total += $sub;
                $total_items_in_cart += $qty;

                if ($id == $product_id) { // Untuk item yang baru saja diubah kuantitasnya
                    $item_subtotal = $sub;
                }
            }
        }
    }

    $response['cart_count'] = $total_items_in_cart;
    $response['grand_total'] = number_format($grand_total, 0, ',', '.');
    $response['item_subtotal'] = number_format($item_subtotal, 0, ',', '.');
} else {
    $response['message'] = 'Parameter tidak lengkap.';
}

echo json_encode($response);
$con->close(); // Tutup koneksi database
exit();
?>