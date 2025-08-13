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
$params['section_no'] = (isset($_REQUEST['section_no']) && $_REQUEST['section_no']!="") ? $_REQUEST['section_no'] : (isset($postdata['section_no']) ? $postdata['section_no'] : '-1');
$params['section_id'] = (isset($_REQUEST['section_id']) && $_REQUEST['section_id']!="") ? $_REQUEST['section_id'] : (isset($postdata['section_id']) ? $postdata['section_id'] : '0');
$params['time'] = isset($_REQUEST['time']) ? $_REQUEST['time'] : (isset($postdata['time']) ? $postdata['time'] : '0');

//print_r($params);
if($params['uid']!='0' && $params['test_id']!='0'){
    $manager = new MockTestManager();
    $manager->endSection($params['test_id'],$params['section_no'],$params['section_id'],$params['time']);
    $result['success'] = "true";
}
else{
    $result['success'] = "false";
    $result['error'] = "insufficient data";
}
print_r(json_encode($result));
?>