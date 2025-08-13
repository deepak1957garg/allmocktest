<?php
function verifyShopifyRequest() {
    if (!isset($_GET['hmac']) || !isset($_GET['shop'])) {
        return false;
    }
    
    $shop = $_GET['shop'];
    $hmac = $_GET['hmac'];
    
    $params = $_GET;
    unset($params['hmac']);
    
    ksort($params);
    $computedHmac = hash_hmac('sha256', http_build_query($params), $_ENV['SHOPIFY_API_SECRET']);
    
    return hash_equals($hmac, $computedHmac);
}

function getShopifyClient($shop, $accessToken) {
    return new GuzzleHttp\Client([
        'base_uri' => "https://{$shop}/admin/api/2023-01/",
        'headers' => [
            'X-Shopify-Access-Token' => $accessToken,
            'Content-Type' => 'application/json',
        ]
    ]);
}

function isAppInstalled() {
    session_start();
    return isset($_SESSION['access_token']) && isset($_SESSION['shop']);
}

function verifyAdminAccess() {
    if (!isAppInstalled()) {
        header("Location: /?shop=" . ($_GET['shop'] ?? ''));
        exit();
    }
}
?>