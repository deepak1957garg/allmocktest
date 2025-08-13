<?php
require_once __DIR__ . '/includes/session.php';

// Verify session from cookie if needed
if (empty($_SESSION['shopify']) && !empty($_COOKIE['shopify_app_session'])) {
    session_id($_COOKIE['shopify_app_session']);
    session_start();
}

// Check installation status
if (empty($_SESSION['shopify']['installed'])) {
    header("Location: /auth/install.php?shop=" . ($_GET['shop'] ?? ''));
    exit();
}

$shop = $_SESSION['shopify']['shop'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>My App</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Required Shopify embedded app tags -->
    <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
    <script type="text/javascript">
        ShopifyApp.init({
            apiKey: '<?php echo $_ENV['SHOPIFY_API_KEY']; ?>',
            shopOrigin: 'https://<?php echo $shop; ?>'
        });
        
        ShopifyApp.ready(function() {
            ShopifyApp.Bar.initialize({
                title: "My PHP App",
                buttons: {
                    secondary: [{
                        label: "Settings",
                        loading: false,
                        callback: function() {
                            // Handle settings click
                        }
                    }]
                }
            });
        });
    </script>
</head>
<body>
    <!-- Your admin content will be loaded here -->
    <iframe 
        src="/templates/admin.php?shop=<?php echo $shop; ?>&host=<?php echo $_GET['host']; ?>" 
        style="width:100%; height:100%; border:none;"
        id="admin-iframe">
    </iframe>
</body>
</html>