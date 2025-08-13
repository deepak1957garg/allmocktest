<?php
include_once dirname(__FILE__) . '/../includes/config/Config.php';

$customerId = $_GET['customer_id'] ?? null;
$productId = $_GET['product_id'] ?? null;
$route = $_GET['route'] ?? "";
if($route=="mock"){
  include "exam.php";
  exit();
}
else if ($route=='get_questions'){
  include_once dirname(__FILE__) . "/../api/get_questions.php";
  exit();
}
else if ($route=='save_response'){
  include_once dirname(__FILE__) . "/../api/save_response.php";
  exit();
}
else if ($route=='start_test'){
  include_once dirname(__FILE__) . "/../api/start_test.php";
  exit();
}
else if ($route=='complete_test'){
  include_once dirname(__FILE__) . "/../api/complete_test.php";
  exit();
}
else if ($route=='complete_section'){
  include_once dirname(__FILE__) . "/../api/complete_section.php";
  exit();
}
else if ($route=='swot'){
  include_once dirname(__FILE__) . "/../api/swot-info.php";
  exit();
}
else if ($route=='swot-info'){
  include_once dirname(__FILE__) . "/../api/swot.php";
  exit();
}
else if ($route=='qlist'){
  include_once dirname(__FILE__) . "/../api/qlist.php";
  exit();
}
else if ($route=='cards'){
  include_once dirname(__FILE__) . "/../api/cards.php";
  exit();
}
else if ($route=='save_card_response'){
  include_once dirname(__FILE__) . "/../api/save_card_response.php";
  exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Mock Test</title></head>
<body>
<div><?php echo $customerId; ?></div>
<div><?php echo $productId; ?></div>
<h2>Mock Test for Product <?= htmlspecialchars($productId) ?></h2>
<div id="test-ui">[Render Test Here]</div>
<script src="<?php echo Config::$SERVER_URL; ?>/assets/mocktest.js"></script>
<script>
  loadMockTest(<?= json_encode($customerId) ?>, <?= json_encode($productId) ?>);
</script>
</body>
</html>