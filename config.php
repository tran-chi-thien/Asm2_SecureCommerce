<?php
//global variable configuration
/*
* PayPal configuration
*/
// PayPal configuration
define('PAYPAL_ID', 'sb-18mta52908222@business.example.com'); //seller email
define('PAYPAL_SANDBOX', TRUE); //TRUE or FALSE
//redirect page
define('PAYPAL_RETURN_URL', 'http://127.0.0.1/Q1_Asm2/success.php');
define('PAYPAL_CANCEL_URL', 'http://127.0.0.1/Q1_Asm2/cancel.php');
define('PAYPAL_NOTIFY_URL', 'http://127.0.0.1/Q1_Asm2/ipn.php');
//define currency
define('PAYPAL_CURRENCY', 'AUD');

define('PAYPAL_URL', (PAYPAL_SANDBOX == true)? "https://www.sandbox.paypal.com/cgi-bin/webscr": "https://www.paypal.com/cgi-bin/webscr");

//PayPal REST API

define('PAYPAL_CLIENT_ID', 'AURtAm2zAGYwrig9EsaAzLEJjAcT4ZGUB89wBTIL9tkYtZNELwcoToxY_3kLSjR-uz8GI9svhPEqBZBj');
define('PAYPAL_CLIENT_SECRET', 'EEwhEmI8UQmQu0ERLjSFiWRkN79333d893aQa6EAplYllsBGmjHnatROwYUMxPf-AQ12WUCvGkPa1MOp');


define('PAYPAL_API_BASE', (PAYPAL_SANDBOX == true) ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com');
?>