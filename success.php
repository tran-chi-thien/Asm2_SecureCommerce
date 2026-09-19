<?php
$status = isset($_REQUEST['status']) ? strtolower($_REQUEST['status']) : '';
$transactionId = isset($_REQUEST['tx']) ? $_REQUEST['tx'] : (isset($_REQUEST['txn_id']) ? $_REQUEST['txn_id'] : '');
$amount = isset($_REQUEST['amt']) ? $_REQUEST['amt'] : (isset($_REQUEST['mc_gross']) ? $_REQUEST['mc_gross'] : (isset($_REQUEST['amount']) ? $_REQUEST['amount'] : ''));
$currency = isset($_REQUEST['cc']) ? $_REQUEST['cc'] : (isset($_REQUEST['mc_currency']) ? $_REQUEST['mc_currency'] : 'AUD');
$product = isset($_REQUEST['item_name']) ? $_REQUEST['item_name'] : 'Electric Bike Model 1, Electric Bike Model 2';
$paymentMethod = isset($_REQUEST['payment_method']) ? $_REQUEST['payment_method'] : (isset($_REQUEST['payment']) ? $_REQUEST['payment'] : 'PayPal');
$isSuccess = $status === 'success' || $transactionId !== '';
$safe = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
if ($isSuccess && $transactionId === '') {
    $transactionId = 'LOCAL-' . strtoupper(substr(hash('sha256', $product . $amount . microtime(true)), 0, 12));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Result | Alice's Electronic Bike Shop</title>
</head>
<body>
    <main class="result">
        <?php if ($isSuccess): ?>
            <h1 class="success">Payment Successful</h1>
            <p>Thank you for your order.</p>
            <div class="details">
                <p><span>Transaction ID</span><strong><?php echo $safe($transactionId); ?></strong></p>
                <p><span>Paid amount</span><strong><?php echo $safe($currency . ' ' . $amount); ?></strong></p>
                <p><span>Payment method</span><strong><?php echo $safe($paymentMethod); ?></strong></p>
                <p><span>Product</span><strong><?php echo $safe($product); ?></strong></p>
            </div>
        <?php else: ?>
            <h1 class="failed">Payment Failed</h1>
            <p>Your payment was not completed.</p>
        <?php endif; ?>
        <a href="index.php">Return to shop</a>
    </main>
</body>
</html>
