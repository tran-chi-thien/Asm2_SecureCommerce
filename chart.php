<?php
session_start();
require_once __DIR__ . '/data.php';
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    if ($action === 'add') {
        $productId = isset($_POST['product_id']) ? $_POST['product_id'] : '';
        if (find_product($products, $productId)) {
            $_SESSION['cart'][$productId] = isset($_SESSION['cart'][$productId]) ? $_SESSION['cart'][$productId] + 1 : 1;
        }
    } elseif ($action === 'update') {
        foreach ($products as $product) {
            $productId = $product['id'];
            if (isset($_POST['quantity'][$productId])) {
                $quantity = max(0, (int) $_POST['quantity'][$productId]);
                if ($quantity === 0) {
                    unset($_SESSION['cart'][$productId]);
                } else {
                    $_SESSION['cart'][$productId] = $quantity;
                }
            }
        }
    } elseif ($action === 'remove') {
        foreach ($products as $product) {
            $productId = $product['id'];
            if (isset($_POST['remove'][$productId])) {
                unset($_SESSION['cart'][$productId]);
            }
        }
    }
    header('Location: chart.php');
    exit;
}

$cartItems = array();
$total = 0;
foreach ($products as $product) {
    $quantity = isset($_SESSION['cart'][$product['id']]) ? (int) $_SESSION['cart'][$product['id']] : 0;
    if ($quantity > 0) {
        $product['quantity'] = $quantity;
        $product['line_total'] = $product['price'] * $quantity;
        $cartItems[] = $product;
        $total += $product['line_total'];
    }
}
$itemNames = array();
foreach ($cartItems as $cartItem) {
    $itemNames[] = $cartItem['name'] . ' x ' . $cartItem['quantity'];
}
$safe = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | Alice's Electronic Bike Shop</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px; font-family: Arial, sans-serif; color: #222; background: #fafafa; }
        .cart-container, .payment-section { width: min(1100px, 100%); margin: 0 auto; padding: 20px; background: #fff; border: 1px solid #ddd; }
        .payment-section { border-top: 0; }
        .cart-header, .cart-item { display: grid; grid-template-columns: 70px 100px 1fr 110px 90px 120px; gap: 12px; align-items: center; }
        .cart-header { border-bottom: 1px solid #ddd; padding-bottom: 10px; font-weight: bold; }
        .cart-item { padding: 12px 0; border-bottom: 1px solid #ddd; }
        .cart-item img { max-width: 90px; max-height: 70px; }
        .cart-item p { margin: 0 0 5px; }
        .qty input { width: 60px; padding: 6px; }
        .actions { margin-top: 16px; }
        button, .checkout-link { background: #111; color: #fff; border: 0; padding: 10px 16px; cursor: pointer; text-decoration: none; display: inline-block; }
        .checkout-link { background: #c00; float: right; }
        .payment-options { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 14px; }
        .payment-option { border: 1px solid #ccc; padding: 10px 14px; font-weight: bold; }
        .empty { padding: 25px 0; }
        @media (max-width: 760px) { body { padding: 10px; } .cart-header { display: none; } .cart-item { grid-template-columns: 40px 70px 1fr; } .cart-item .price, .cart-item .qty, .cart-item .total { grid-column: 3; } .checkout-link { float: none; margin-top: 10px; } }
    </style>
</head>
<body>
    <main class="cart-container">
        <h1>Shopping Cart</h1>
        <?php if (!$cartItems): ?>
            <p class="empty">Your cart is empty. <a href="index.php">Continue shopping</a>.</p>
        <?php else: ?>
            <div class="cart-header"><div>Remove</div><div>Image</div><div>Product</div><div>Price</div><div>Qty</div><div>Total</div></div>
            <form method="post">
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <div><input type="checkbox" name="remove[<?php echo $safe($item['id']); ?>]" value="1"></div>
                        <div><img src="<?php echo $safe($item['image']); ?>" alt="<?php echo $safe($item['name']); ?>"></div>
                        <div><p><strong><?php echo $safe($item['name']); ?></strong></p><p><?php echo $safe($item['description']); ?></p></div>
                        <div>$<?php echo number_format($item['price'], 2); ?></div>
                        <div class="qty"><input type="number" min="0" name="quantity[<?php echo $safe($item['id']); ?>]" value="<?php echo $item['quantity']; ?>"></div>
                        <div>$<?php echo number_format($item['line_total'], 2); ?></div>
                    </div>
                <?php endforeach; ?>
                <div class="actions"><button type="submit" name="action" value="update">UPDATE CART</button> <button type="submit" name="action" value="remove">REMOVE SELECTED</button></div>
            </form>
            <h2>Total: $<?php echo number_format($total, 2); ?></h2>
            <form action="billing.php" method="get">
                <input type="hidden" name="amount" value="<?php echo number_format($total, 2, '.', ''); ?>">
                <input type="hidden" name="items" value="<?php echo $safe(implode(', ', $itemNames)); ?>">
                <button class="checkout-link" type="submit">CHECKOUT NOW &raquo;</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
