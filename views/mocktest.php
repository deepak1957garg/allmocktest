<?php
$customerId = $_GET['customer_id'] ?? null;
$productId = $_GET['product_id'] ?? null;
?>
<!DOCTYPE html>
<html>
<head><title>Mock Test</title></head>
<body>
<div><?php echo $customerId; ?></div>
<div><?php echo $productId; ?></div>
<h2>Mock Test for Product <?= htmlspecialchars($productId) ?></h2>
<div id="test-ui">[Render Test Here]</div>

</body>
</html>