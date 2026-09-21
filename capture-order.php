<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/data.php';
require_once __DIR__ . '/paypal-api.php';

$itemNames = array();
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
foreach ($products as $product) {
    $quantity = isset($cart[$product['id']]) ? max(0, (int) $cart[$product['id']]) : 0;
    if ($quantity > 0) {
        $itemNames[] = $product['name'] . ' x ' . $quantity;
    }
}

$orderId = isset($_POST['orderID']) ? trim($_POST['orderID']) : '';
if ($orderId === '') {
    http_response_code(400);
    echo json_encode(array('error' => 'Missing order ID.'));
    exit;
}

try {
    $capture = paypal_capture_order($orderId);
    $status = isset($capture['status']) ? $capture['status'] : '';
    if ($status !== 'COMPLETED') {
        http_response_code(402);
        echo json_encode(array('error' => 'Payment was not completed.'));
        exit;
    }

    $captureDetails = isset($capture['purchase_units'][0]['payments']['captures'][0])
        ? $capture['purchase_units'][0]['payments']['captures'][0]
        : array();
    $transactionId = isset($captureDetails['id']) ? $captureDetails['id'] : $orderId;
    $amount = isset($captureDetails['amount']['value']) ? $captureDetails['amount']['value'] : '';
    $currency = isset($captureDetails['amount']['currency_code']) ? $captureDetails['amount']['currency_code'] : PAYPAL_CURRENCY;

    $logEntry = date('c') . ' CARD_CAPTURED ' . $transactionId . ' ' . $amount . ' ' . $currency . PHP_EOL;
    file_put_contents(__DIR__ . '/payments.log', $logEntry, FILE_APPEND | LOCK_EX);

    unset($_SESSION['cart']);

    echo json_encode(array(
        'transactionId' => $transactionId,
        'amount' => $amount,
        'currency' => $currency,
        'itemName' => implode(', ', $itemNames),
    ));
} catch (RuntimeException $e) {
    http_response_code(502);
    echo json_encode(array('error' => $e->getMessage()));
}
?>
