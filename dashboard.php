<?php
include_once dirname(__FILE__) . '/includes/config/Config.php';

// dashboard.php
$shop = $_GET['shop'];
?>
<!DOCTYPE html>
<html>
<head>
  <script src="https://cdn.shopify.com/shopifycloud/app-bridge.js"></script>
</head>
<body>
  <h2>Welcome CAT 2025 Mock Test App</h2>

  <!-- <div id="test-app">
    <a href="/assets/start-test.html?shop=<?php echo $shop; ?>">Start Test</a>
  </div> -->

  <script>
    const AppBridge = window['app-bridge'];
    const createApp = AppBridge.default;
    const app = createApp({
      apiKey: '<?php echo Config::$SHOPIFY_API_KEY; ?>',
      shopOrigin: '<?php echo $shop; ?>',
      host: new URLSearchParams(window.location.search).get("host"),
      forceRedirect: true,
    });
  </script>
</body>
</html>