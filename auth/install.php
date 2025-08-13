<?php
include_once dirname(__FILE__) . '/../includes/config/Config.php';

// auth/install.php
$shop = $_GET['shop'];
$api_key = Config::$SHOPIFY_API_KEY;
$scopes = Config::$SHOPIFY_APP_SCOPES;
$redirect_uri = Config::$SERVER_URL . '/auth/callback.php';

$install_url = "https://{$shop}/admin/oauth/authorize?client_id={$api_key}&scope={$scopes}&redirect_uri={$redirect_uri}";
header("Location: $install_url");
exit;

// require_once __DIR__ . '/../includes/session.php';
// require_once __DIR__ . '/../includes/shopify.php';
// require_once __DIR__ . '/../includes/functions.php';

// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
// $dotenv->load();

// $shopify = new Shopify();

// // Debug output
// error_log("Install request: " . print_r($_GET, true));

// $_SESSION = [];
// session_destroy();


// if (!isset($_GET['shop'])) {
//     die("Shop parameter missing");
// }

// // Verify HMAC if present
// if (isset($_GET['hmac'])) {
//     if (!verifyShopifyRequest()) {
//         die('Invalid HMAC signature');
//     }
// }

// // Start fresh session
// session_start();

// $shopify = new Shopify();
// $installUrl = $shopify->installUrl($_GET['shop']);

// error_log("Redirecting to: " . $installUrl);
// header("Location: " . $installUrl);
// exit();
?>