<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// In a real app, you would verify the request comes from a Shopify store you've installed on
// For this example, we'll skip that verification

$input = json_decode(file_get_contents('php://input'), true);

// Static data response
$response = [
    'status' => 'success',
    'message' => 'Hello from PHP server!',
    'value' => 42,
    'timestamp' => date('Y-m-d H:i:s'),
    'shop' => $input['shop'] ?? 'unknown'
];

echo json_encode($response);
exit();
?>