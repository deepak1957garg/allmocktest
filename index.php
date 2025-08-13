<?php
// index.php

// Load environment variables (optional, else use constants)
require_once __DIR__ . '/includes/db.php'; // your MySQL connection file
// include shopify_helper.php if needed

// Check if shop parameter is set
if (!isset($_GET['shop'])) {
    echo "Missing 'shop' parameter.";
    exit;
}

$shop = $_GET['shop'];

//print_r($_REQUEST);

// Lookup shop in database to see if access_token exists
$conn = getDbConnection(); // defined in db.php

$stmt = $conn->prepare("SELECT access_token FROM users WHERE shop = ?");
$stmt->bind_param("s", $shop);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // First time install → Redirect to OAuth install
    header("Location: /auth/install.php?shop=" . urlencode($shop));
    exit;
} else {
    // App already installed → Redirect to dashboard
    header("Location: /dashboard.php?shop=" . urlencode($shop));
    exit;
}

// require_once __DIR__ . '/vendor/autoload.php';

// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
// $dotenv->load();

// session_start();

// // Simple router
// $request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// $query = $_GET;

// // Check if app is installed (in production, you'd check your database)
// $isInstalled = isset($_SESSION['access_token']) && isset($_SESSION['shop']);

// switch ($request) {
//     case '/':
//         if ($isInstalled) {
//             // App is installed, redirect to admin
//             header("Location: /templates/admin.php?shop=" . $_SESSION['shop']);
//         } else {
//             // Show installation page
//             echo "<h1>Welcome to My Shopify App</h1>";
//             echo "<p><a href='/auth/install.php?shop=" . ($_GET['shop'] ?? 'your-store.myshopify.com') . "'>Install App</a></p>";
//         }
//         break;
        
//     case '/auth/install':
//     case '/auth/install.php':
//         require __DIR__ . '/auth/install.php';
//         break;
        
//     case '/auth/callback':
//     case '/auth/callback.php':
//         require __DIR__ . '/auth/callback.php';
//         break;
        
//     case '/admin':
//     case '/templates/admin.php':
//         if ($isInstalled) {
//             require __DIR__ . '/templates/admin.php';
//         } else {
//             header("Location: /?shop=" . ($_GET['shop'] ?? ''));
//         }
//         break;
        
//     default:
//         http_response_code(404);
//         echo '404 Not Found';
//         break;
// }