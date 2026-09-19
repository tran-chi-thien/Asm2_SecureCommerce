<?php
require_once __DIR__ . '/config.php';

$amount = isset($_POST['amount']) ? number_format((float) $_POST['amount'], 2, '.', '') : '0.00';
$itemName = isset($_POST['item_name']) ? trim($_POST['item_name']) : 'Electric Bike Order';
if ((float) $amount <= 0 || $itemName === '') {
    header('Location: success.php?status=failed');
    exit;
}

$baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$returnUrl = $baseUrl . '/success.php';
$cancelUrl = $baseUrl . '/cancel.php';
$notifyUrl = $baseUrl . '/ipn.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PayPal Checkout</title>
</head>
<body>
    <form id="paypal-form" action="<?php echo htmlspecialchars(PAYPAL_URL, ENT_QUOTES, 'UTF-8'); ?>" method="post" style="display: none;">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="<?php echo htmlspecialchars(PAYPAL_ID, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="item_number" value="ALICE-BIKE-ORDER">
        <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="currency_code" value="<?php echo htmlspecialchars(PAYPAL_CURRENCY, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="return" value="<?php echo htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="cancel_return" value="<?php echo htmlspecialchars($cancelUrl, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="notify_url" value="<?php echo htmlspecialchars($notifyUrl, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="rm" value="2">
        <input type="hidden" name="no_shipping" value="1">
    </form>
    <noscript>
        <p>JavaScript is required to continue to PayPal.</p>
        <button type="submit" form="paypal-form">Continue to PayPal</button>
    </noscript>
    <script>
        document.getElementById('paypal-form').submit();
    </script>
</body>
</html>
