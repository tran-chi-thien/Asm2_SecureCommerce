<?php
// Minimal client for the PayPal REST API (Orders v2), used to charge
// Visa / MasterCard directly through PayPal's hosted card fields.
require_once __DIR__ . '/config.php';

function paypal_get_access_token() {
    $ch = curl_init(PAYPAL_API_BASE . '/v1/oauth2/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ':' . PAYPAL_CLIENT_SECRET);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Accept-Language: en_US'));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException('Unable to reach PayPal: ' . $error);
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $data = json_decode($response, true);
    if ($status !== 200 || !isset($data['access_token'])) {
        throw new RuntimeException('PayPal authentication failed.');
    }
    return $data['access_token'];
}

function paypal_api_request($method, $path, $accessToken, $body = null) {
    $ch = curl_init(PAYPAL_API_BASE . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken,
    ));
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException('PayPal request failed: ' . $error);
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $data = json_decode($response, true);
    if ($status < 200 || $status >= 300) {
        $message = isset($data['message']) ? $data['message'] : ('PayPal request failed with status ' . $status . '.');
        throw new RuntimeException($message);
    }
    return $data;
}

function paypal_create_order($amount, $currency) {
    $accessToken = paypal_get_access_token();
    $body = array(
        'intent' => 'CAPTURE',
        'purchase_units' => array(
            array(
                'amount' => array(
                    'currency_code' => $currency,
                    'value' => $amount,
                ),
            ),
        ),
    );
    return paypal_api_request('POST', '/v2/checkout/orders', $accessToken, $body);
}

function paypal_capture_order($orderId) {
    $accessToken = paypal_get_access_token();
    return paypal_api_request('POST', '/v2/checkout/orders/' . rawurlencode($orderId) . '/capture', $accessToken, new stdClass());
}
?>
