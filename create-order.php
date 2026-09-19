<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/data.php';
require_once __DIR__ . '/paypal-api.php';

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$total = 0;
foreach ($products as $product) {
    $quantity = isset($cart[$product['id']]) ? max(0, (int) $cart[$product['id']]) : 0;
    $total += $product['price'] * $quantity;
}

if ($total <= 0) {
    http_response_code(400);
    echo json_encode(array('error' => 'Your cart is empty.'));
    exit;
}

try {
    $order = paypal_create_order(number_format($total, 2, '.', ''), PAYPAL_CURRENCY);
    echo json_encode(array('id' => $order['id']));
} catch (RuntimeException $e) {
    http_response_code(502);
    echo json_encode(array('error' => $e->getMessage()));
}
?>
