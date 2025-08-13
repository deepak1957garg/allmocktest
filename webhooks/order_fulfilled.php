<?php
include_once dirname(__FILE__) . '/../includes/config/Config.php';
include_once dirname(__FILE__) . '/../modules/mocktest/classes/MockTestManager.php';
function verifyWebhook($data, $hmac_header) {
    $calculated_hmac = base64_encode(hash_hmac('sha256', $data, Config::$SHOPIFY_API_SECRET, true));
    return hash_equals($hmac_header, $calculated_hmac);
}

$data = file_get_contents('php://input');
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];

// error_log("inside order_fulfilled");
// error_log(print_r($data,1));
// error_log(print_r($hmac_header,1));

if (!verifyWebhook($data, $hmac_header)) {
    http_response_code(401);
    die('Invalid webhook signature');
}
//error_log("inside order_fulfilled 2");
$mockTestManager = new MockTestManager();
$order = json_decode($data, true);

error_log("order_fulfilled " . print_r($order,1));

$mockTestManager->saveOrder($order);
?>