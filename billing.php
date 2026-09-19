<?php
session_start();
require_once __DIR__ . '/data.php';
require_once __DIR__ . '/config.php';

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$cartItems = array();
$total = 0;
foreach ($products as $product) {
    $quantity = isset($cart[$product['id']]) ? max(0, (int) $cart[$product['id']]) : 0;
    if ($quantity > 0) {
        $product['quantity'] = $quantity;
        $cartItems[] = $product;
        $total += $product['price'] * $quantity;
    }
}
if (!$cartItems) {
    header('Location: chart.php');
    exit;
}
$itemNames = array();
foreach ($cartItems as $cartItem) {
    $itemNames[] = $cartItem['name'] . ' x ' . $cartItem['quantity'];
}
$selectedPayment = isset($_GET['payment']) ? $_GET['payment'] : 'Visa';
$safe = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Information | Alice's Electronic Bike Shop</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px; font-family: Arial, sans-serif; color: #222; background: #f5f6f8; }
        .checkout-page { width: min(1100px, 100%); margin: 0 auto; }
        h1 { margin: 0 0 20px; font-size: 30px; }
        .checkout-grid { display: grid; grid-template-columns: 1.35fr 1fr; gap: 18px; }
        .panel { background: #fff; border: 1px solid #ddd; padding: 22px; }
        h2 { margin: 0 0 18px; font-size: 20px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 20px; }
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field.full { grid-column: 1 / -1; }
        label { font-size: 14px; }
        input, select { min-height: 38px; padding: 8px 10px; border: 1px solid #c8cdd3; font-size: 14px; background: #fff; }
        .payment-list { display: grid; gap: 8px; }
        .payment-choice { display: flex; align-items: center; gap: 12px; min-height: 54px; padding: 8px 12px; border: 1px solid #ddd; cursor: pointer; font-weight: bold; }
        .payment-choice:has(input:checked) { border-color: #1683d8; box-shadow: 0 0 0 1px #1683d8; }
        .payment-choice input { min-height: auto; }
        .brand { display: inline-block; min-width: 145px; font-size: 22px; }
        .visa { color: #16549c; } .mastercard { color: #d33a2c; } .paypal { color: #123b78; } .americanexpress { color: #0875a1; } .alipay { color: #087ec1; } .googlepay { color: #4285f4; } .applepay { color: #111; }
        .order-summary { margin: 0 0 18px; padding: 12px; background: #f5f6f8; }
        .order-summary p { margin: 6px 0; }
        .continue-btn { width: 100%; margin-top: 18px; padding: 12px; border: 0; background: #087fe5; color: #fff; font-size: 15px; cursor: pointer; }
        @media (max-width: 760px) { body { padding: 12px; } .checkout-grid, .form-grid { grid-template-columns: 1fr; } .field.full { grid-column: auto; } h1 { font-size: 25px; } }
    </style>
</head>
<body>
    <main class="checkout-page">
        <h1>Provide Billing Information</h1>
        <div class="checkout-grid">
            <section class="panel" aria-labelledby="billing-heading">
                <h2 id="billing-heading">Billing address</h2>
                <form id="billing-form" action="success.php" method="post">
                    <div class="form-grid">
                        <div class="field"><label for="first-name">First name</label><input id="first-name" name="firstName" required autocomplete="given-name"></div>
                        <div class="field"><label for="last-name">Last name</label><input id="last-name" name="lastName" required autocomplete="family-name"></div>
                        <div class="field full"><label for="username">Username</label><input id="username" name="username" required autocomplete="username"></div>
                        <div class="field full"><label for="email">Email (Optional)</label><input id="email" name="email" type="email" autocomplete="email"></div>
                        <div class="field full"><label for="address">Address</label><input id="address" name="address" required autocomplete="street-address"></div>
                        <div class="field full"><label for="address-2">Address 2 (Optional)</label><input id="address-2" name="address2"></div>
                        <div class="field"><label for="country">Country</label><select id="country" name="country" required><option value="">Choose...</option><option>Australia</option><option>New Zealand</option><option>United States</option><option>United Kingdom</option></select></div>
                        <div class="field"><label for="state">State</label><select id="state" name="state" required><option value="">Choose...</option><option>Victoria</option><option>New South Wales</option><option>Queensland</option><option>Western Australia</option></select></div>
                        <div class="field"><label for="zip">Zip</label><input id="zip" name="zip" required inputmode="numeric" autocomplete="postal-code"></div>
                    </div>
                    <input type="hidden" name="amount" value="<?php echo number_format($total, 2, '.', ''); ?>">
                    <input type="hidden" name="item_name" value="<?php echo $safe(implode(', ', $itemNames)); ?>">
                    <input type="hidden" name="status" value="success">
                    <input type="hidden" name="payment_method" id="payment-method" value="<?php echo $safe($selectedPayment); ?>">
                </form>
            </section>
            <section class="panel" aria-labelledby="payment-heading">
                <h2 id="payment-heading">Select A Payment Option</h2>
                <div class="order-summary">
                    <?php foreach ($cartItems as $item): ?><p><?php echo $safe($item['name']); ?> x <?php echo $item['quantity']; ?></p><?php endforeach; ?>
                    <p><strong>Total: AUD $<?php echo number_format($total, 2); ?></strong></p>
                </div>
                <div class="payment-list">
                    <?php $paymentOptions = array('Visa' => 'VISA', 'MasterCard' => 'MasterCard', 'PayPal' => 'PayPal', 'Google Pay' => 'G Pay'); ?>
                    <?php foreach ($paymentOptions as $value => $label): ?>
                        <label class="payment-choice"><input type="radio" name="payment" value="<?php echo $safe($value); ?>" <?php echo $selectedPayment === $value ? 'checked' : ''; ?>><span class="brand <?php echo strtolower(str_replace(' ', '', $value)); ?>"><?php echo $safe($label); ?></span></label>
                    <?php endforeach; ?>
                </div>
                <button class="continue-btn" id="continue-btn" type="submit" form="billing-form">Continue to checkout</button>
                <div id="google-pay-button-container" style="display: none; width: 100%; margin-top: 18px;"></div>
                <div id="paypal-card-button-container" style="display: none; width: 100%; margin-top: 18px;"></div>
            </section>
        </div>
    </main>
    <script>
        window.ALICE_CHECKOUT = {
            totalPrice: <?php echo json_encode(number_format($total, 2, '.', '')); ?>,
            currencyCode: 'AUD'
        };
        (function () {
            var form = document.getElementById('billing-form');
            var paymentMethod = document.getElementById('payment-method');
            var continueBtn = document.getElementById('continue-btn');
            var googlePayContainer = document.getElementById('google-pay-button-container');
            var cardContainer = document.getElementById('paypal-card-button-container');

            function updatePaymentUI(value) {
                form.action = value === 'PayPal' ? 'paypal.php' : 'success.php';
                var isGooglePay = value === 'Google Pay';
                var isCard = value === 'Visa' || value === 'MasterCard';
                continueBtn.style.display = (isGooglePay || isCard) ? 'none' : '';
                googlePayContainer.style.display = isGooglePay ? '' : 'none';
                cardContainer.style.display = isCard ? '' : 'none';
                if (isCard && window.renderPayPalCardButton) {
                    window.renderPayPalCardButton();
                }
            }

            document.querySelectorAll('input[name="payment"]').forEach(function (input) {
                input.addEventListener('change', function () {
                    paymentMethod.value = input.value;
                    updatePaymentUI(input.value);
                });
            });
            updatePaymentUI(paymentMethod.value);
        }());
    </script>
    <script src="index.js"></script>
    <script src="https://pay.google.com/gp/p/js/pay.js" onload="onGooglePayLoaded()" async></script>
    <script src="paypal-card.js"></script>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo urlencode(PAYPAL_CLIENT_ID); ?>&currency=<?php echo urlencode(PAYPAL_CURRENCY); ?>&components=buttons&enable-funding=card" onload="if (window.renderPayPalCardButton) { renderPayPalCardButton(); }"></script>
</body>
</html>
