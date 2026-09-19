<?php
$products = array(
        array(
                'id' => 'bronton',
                'name' => 'Bronton',
                'price' => 3000.00,
                'image' => 'assets/img/bronton.jpg',
                'description' => 'Comfortable electric bike for everyday city riding.'
        ),
        array(
                'id' => 'e-bmx',
                'name' => 'E-BMX',
                'price' => 2000.00,
                'image' => 'assets/img/dummyimg.jpg',
                'description' => 'Agile electric BMX with responsive handling.'
        ),
        array(
                'id' => 'f-65',
                'name' => 'F-65',
                'price' => 700.00,
                'image' => 'assets/img/f65.jpg',
                'description' => 'Lightweight electric bike for short urban trips.'
        )
);

function find_product($products, $productId) {
        foreach ($products as $product) {
                if ($product['id'] === $productId) {
                        return $product;
                }
        }
        return null;
}
?>