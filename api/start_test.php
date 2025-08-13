<?php
include_once dirname(__FILE__) . '/../modules/mocktest/classes/MockTestManager.php';
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
$params['test_id'] = isset($_REQUEST['test_id']) ? $_REQUEST['test_id'] : (isset($postdata['test_id']) ? $postdata['test_id'] : '0');
$params['uid'] = (isset($_REQUEST['uid']) && $_REQUEST['uid']!="") ? $_REQUEST['uid'] : (isset($postdata['uid']) ? $postdata['uid'] : '0');


//print_r($params);
if($params['uid']!='0' && $params['test_id']!='0'){
    $manager = new MockTestManager();
    $manager->startTest($params['test_id']);
    $result['success'] = "true";
}
else{
    $result['success'] = "false";
    $result['error'] = "insufficient data";
}
print_r(json_encode($result));
?>