<?php 
//Page from paypal to verify buyer and seller
// Include configuration file 
include_once 'config.php'; 
 
// STEP 1: read POST data
// Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
// Instead, read raw POST data from the input stream.
$raw_post_data = file_get_contents('php://input');
$myPost = array();
parse_str($raw_post_data, $myPost);
// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
$req = 'cmd=_notify-validate';
foreach ($myPost as $key => $value) {
  $value = urlencode($value);
  $req .= "&$key=$value";
}

// Step 2: POST IPN data back to PayPal to validate
$paypalURL = PAYPAL_URL; 
$ch = curl_init($paypalURL); 
if ($ch == FALSE) { 
    return FALSE; 
} 
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
// In wamp-like environments that do not come bundled with root authority certificates,
// please download 'cacert.pem' from "https://curl.haxx.se/docs/caextract.html" and set
// the directory path of the certificate as shown below:
// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
if ( !($res = curl_exec($ch)) ) {
  // error_log("Got " . curl_error($ch) . " when processing IPN data");
  curl_close($ch);
  exit;
}
curl_close($ch);


// inspect IPN validation result and act accordingly
if (strcmp (trim($res), "VERIFIED") == 0) {
      // The IPN is verified, process it:
      // check whether the payment_status is Completed
      // check that txn_id has not been previously processed
      // check that receiver_email is your Primary PayPal email
      // check that payment_amount/payment_currency are correct
      // process the notification
      // assign posted variables to local variables
      $paymentStatus = isset($myPost['payment_status']) ? $myPost['payment_status'] : '';
      $receiverEmail = isset($myPost['receiver_email']) ? $myPost['receiver_email'] : '';
      $paymentCurrency = isset($myPost['mc_currency']) ? $myPost['mc_currency'] : '';
      if ($paymentStatus === 'Completed' && strcasecmp($receiverEmail, PAYPAL_ID) === 0 && $paymentCurrency === PAYPAL_CURRENCY) {
        $logEntry = date('c') . ' VERIFIED ' . ($myPost['txn_id'] ?? '') . ' ' . ($myPost['mc_gross'] ?? '') . ' ' . ($myPost['item_name'] ?? '') . PHP_EOL;
        file_put_contents(__DIR__ . '/payments.log', $logEntry, FILE_APPEND | LOCK_EX);
      }
    } else if (strcmp ($res, "INVALID") == 0) {
      // IPN invalid, log for manual investigation
      error_log('Invalid PayPal IPN received.');
    }
?>