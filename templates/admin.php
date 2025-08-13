<?php
require_once __DIR__ . '/../includes/session.php';

// Restore session if passed
if (!empty($_GET['session'])) {
    session_id($_GET['session']);
    session_start();
}

// Verify session
if (empty($_SESSION['shopify']['installed'])) {
    die('<script>top.window.location.href = "/auth/install.php?shop=' . ($_GET['shop'] ?? '') . '"</script>');
}

$shop = "deepakgarg-test.myshopify.com/";//$_SESSION['shopify_store'];
$host = $_GET['host'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>My App Admin</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f9fafb;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My PHP App Dashboard</h1>
        
        <!-- Your admin content here -->
        <div id="app-content">
            <p>Welcome to your app admin panel!</p>
            
            <h2>Liquid Snippet</h2>
            <p>Add this to your theme:</p>
            <textarea style="width:100%; height:60px;" readonly>
{% render 'my-php-app-snippet' %}
            </textarea>
        </div>
    </div>
    
    <!-- Shopify embedded app script -->
    <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
    <script>
        ShopifyApp.init({
            apiKey: '<?php echo $_ENV['SHOPIFY_API_KEY']; ?>',
            shopOrigin: 'https://<?php echo $shop; ?>'
        });
        
        // Tell Shopify we're embedded
        ShopifyApp.ready(function() {
            ShopifyApp.Bar.loadingOff();
        });
    </script>
</body>
</html>