<?php
session_start();
require_once __DIR__ . '/data.php';
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$cartCount = array_sum($cart);
$safe = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alice's Electronic Bike Shop</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php"><strong>ALICE'S</strong> ELECTRONIC BIKE Shop</a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="chart.php"><i class="fa fa-shopping-cart"></i> Cart (<?php echo $cartCount; ?>)</a></li>
                <li><a href="#">Login</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <ol class="breadcrumb"><li class="active">Electric Bikes</li></ol>
                <div class="row">
                    <div class="btn-group alg-right-pad">
                        <a class="btn btn-default" href="index.php"><strong><?php echo count($products); ?></strong> items</a>
                    </div>
                </div>
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 text-center col-sm-6 col-xs-6">
                            <div class="thumbnail product-box">
                                <img src="<?php echo $safe($product['image']); ?>" alt="<?php echo $safe($product['name']); ?>">
                                <div class="caption">
                                    <h3><?php echo $safe($product['name']); ?></h3>
                                    <p><?php echo $safe($product['description']); ?></p>
                                    <p>Price: <strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
                                    <form action="chart.php" method="post">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="product_id" value="<?php echo $safe($product['id']); ?>">
                                        <button type="submit" class="btn btn-success">ADD TO CART</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <!--Footer -->
    <div class="col-md-12 footer-box">


        

            <div class="col-md-4">
                <strong>Our Location</strong>
                <hr>
                <p>
                     Swanston St, Melbourne,<br />
                                    VIC 3000, Australia<br />
                    Call: +61-000-000-000<br>
                    Email: info@alicebikeshop.com<br>
                </p>

                2020 www.alicebikeeshop.com | All Right Reserved
            </div>
          
        </div>
        <hr>
    </div>
    <div class="col-md-12 end-box">&copy; 2020 | All Rights Reserved | Alice's Electronic Bike Shop</div>
</body>
</html>
