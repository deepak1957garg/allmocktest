<?php
include_once dirname(__FILE__) . '/../includes/config/Config.php';

$api_key = Config::$SHOPIFY_API_KEY;
$shared_secret = Config::$SHOPIFY_API_SECRET; //'c2475bd1c7ba2a2fd89925f699713fe1';

$shop = $_GET['shop'];
$code = $_GET['code'];
$hmac = $_GET['hmac'];
$params = $_GET;

// Step 1: HMAC Validation
function validateHMAC($params, $shared_secret, $hmac) {
    unset($params['hmac'], $params['signature']);
    ksort($params);
    $query = http_build_query($params);
    $calculated_hmac = hash_hmac('sha256', $query, $shared_secret);
    return hash_equals($hmac, $calculated_hmac);
}

if (!validateHMAC($params, $shared_secret, $hmac)) {
    die("Invalid HMAC verification.");
}

// Step 2: Exchange code for access token using cURL
$access_token_url = "https://$shop/admin/oauth/access_token";

$data = [
    "client_id" => $api_key,
    "client_secret" => $shared_secret,
    "code" => $code
];

//print_r($data);
//print_r($access_token_url);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $access_token_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_status !== 200) {
    echo "Failed to get access token. Status: $http_status<br>";
    echo "Response: " . htmlspecialchars($response);
    exit;
}

// Step 3: Decode response
$response_data = json_decode($response, true);
$access_token = $response_data['access_token'] ?? null;

if (!$access_token) {
    echo "Access token not found in response.";
    exit;
}

// Step 4: Store access token in DB
require_once '../includes/db.php';
$conn = getDbConnection();
$stmt = $conn->prepare("INSERT INTO users (shop, email) VALUES (?, '') ON DUPLICATE KEY UPDATE shop = shop");
$stmt->bind_param("s", $shop);
$stmt->execute();

$stmt = $conn->prepare("UPDATE users SET access_token = ? WHERE shop = ?");
$stmt->bind_param("ss", $access_token, $shop);
$stmt->execute();

// After app installation, register these webhooks
registerWebhook($shop, $access_token);
function registerWebhook($shop, $access_token) {
    $webhookUrl = 'https://' . $shop . '/admin/api/2024-01/webhooks.json';
    $data = [
        "webhook" => [
            "topic" => "orders/paid",
            "address" => "https://mocktest.thingsapp.co/webhooks/order_paid.php",
            "format" => "json"
        ]
    ];


    //error_log(print_r($webhookUrl,1));
    //error_log(print_r($data,1));

    $ch = curl_init($webhookUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "X-Shopify-Access-Token: $access_token"
    ]);



    $response = curl_exec($ch);
   // error_log(print_r($response,1));
    curl_close($ch);

    $webhookUrl = 'https://' . $shop . '/admin/api/2024-01/webhooks.json';
    $data = [
        "webhook" => [
            "topic" => "orders/fulfilled",
            "address" => "https://mocktest.thingsapp.co/webhooks/order_fulfilled.php",
            "format" => "json"
        ]
    ];


    //error_log(print_r($webhookUrl,1));
    //error_log(print_r($data,1));

    $ch = curl_init($webhookUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "X-Shopify-Access-Token: $access_token"
    ]);



    $response = curl_exec($ch);
    //error_log(print_r($response,1));
    curl_close($ch);
}

// Step 5: Redirect via App Bridge to dashboard
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.shopify.com/shopifycloud/app-bridge.js"></script>
</head>
<body>
<script>
    const AppBridge = window['app-bridge'];
    const createApp = AppBridge.default;
    const actions = AppBridge.actions;

    const app = createApp({
        apiKey: '<?= $api_key ?>',
        shopOrigin: 'https://<?= $shop ?>',
        forceRedirect: true,
    });

    const Redirect = actions.Redirect;
    const redirect = Redirect.create(app);
    redirect.dispatch(Redirect.Action.APP, '/dashboard.php?shop=<?= $shop ?>');
</script>
</body>
</html>