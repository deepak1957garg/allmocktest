<?php
include_once dirname(__FILE__) . '/../modules/educard/classes/EduCardManager.php';
header('Cache-Control: max-age=0');
header('Content-Type: application/json');

$entityBody = file_get_contents('php://input');
$data = json_decode($entityBody,TRUE);

$result = array();
$result['success'] = "true";
$result['error'] = "";
$result['response'] = array();

$postdata = isset($_REQUEST['data']) ? json_decode($_REQUEST['data'],true) : $data;

$params = array();
$params['id'] = isset($_REQUEST['id']) ? $_REQUEST['id'] : (isset($postdata['id']) ? $postdata['id'] : '0');
$params['card_id'] = isset($_REQUEST['card_id']) ? $_REQUEST['card_id'] : (isset($postdata['card_id']) ? $postdata['card_id'] : '0');
$params['uid'] = (isset($_REQUEST['uid']) && $_REQUEST['uid']!="") ? $_REQUEST['uid'] : (isset($postdata['uid']) ? $postdata['uid'] : "0");
$params['action'] = isset($_REQUEST['action']) ? $_REQUEST['action'] : (isset($postdata['action']) ? $postdata['action'] : '0');

if($params['uid']!="0" && $params['id']!="0"){
    $manager = new EduCardManager();
    $manager->saveResponse($params['id'],$params['action']);
    $result['success'] = "true";
}
else{
    $result['success'] = "false";
    $result['error'] = "insufficient data";
}
print_r(json_encode($result));
?>