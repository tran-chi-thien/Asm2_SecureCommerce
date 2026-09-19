<?php
//global variable configuration
/*
* PayPal configuration
*/
// PayPal configuration
define('PAYPAL_ID', 'sb-18mta52908222@business.example.com'); //seller email
define('PAYPAL_SANDBOX', TRUE); //TRUE or FALSE
//redirect page
define('PAYPAL_RETURN_URL', 'http://localhost/Tut5/success.php'); 
define('PAYPAL_CANCEL_URL', 'http://localhost/Tut5/cancel.php'); 
define('PAYPAL_NOTIFY_URL', 'http://127.0.0.1/Tut5/ipn.php');
//define currency
define('PAYPAL_CURRENCY', 'AUD');

// Change not required
define('PAYPAL_URL', (PAYPAL_SANDBOX == true)? "https://www.sandbox.paypal.com/cgi-bin/webscr": "https://www.paypal.com/cgi-bin/webscr");

/*
* PayPal REST API (Checkout SDK) configuration
* Used for the in-page Visa / MasterCard card payment button on billing.php,
* which charges the card through PayPal without redirecting off-site (like Stripe).
* Get these from a REST API app at https://developer.paypal.com/dashboard/applications
*/
define('PAYPAL_CLIENT_ID', 'AURtAm2zAGYwrig9EsaAzLEJjAcT4ZGUB89wBTIL9tkYtZNELwcoToxY_3kLSjR-uz8GI9svhPEqBZBj');
define('PAYPAL_CLIENT_SECRET', 'EEwhEmI8UQmQu0ERLjSFiWRkN79333d893aQa6EAplYllsBGmjHnatROwYUMxPf-AQ12WUCvGkPa1MOp');

// Change not required
define('PAYPAL_API_BASE', (PAYPAL_SANDBOX == true) ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com');
?>