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
$params['uid'] = (isset($_REQUEST['uid']) && $_REQUEST['uid']!="") ? $_REQUEST['uid'] : (isset($postdata['uid']) ? $postdata['uid'] : "0");
$params['qid'] = isset($_REQUEST['question']) ? $_REQUEST['question'] : (isset($postdata['question']) ? $postdata['question'] : '0');
$params['answer'] = (isset($_REQUEST['answer']) && $_REQUEST['answer']!="") ? $_REQUEST['answer'] : (isset($postdata['answer']) ? $postdata['answer'] : "");
$params['answer_option'] = (isset($_REQUEST['option']) && $_REQUEST['option']!="") ? $_REQUEST['option'] : (isset($postdata['option']) ? $postdata['option'] : "0");
$params['status'] = (isset($_REQUEST['status']) && $_REQUEST['status']!="") ? $_REQUEST['status'] : (isset($postdata['status']) ? $postdata['status'] : "");
$params['time'] = isset($_REQUEST['time']) ? $_REQUEST['time'] : (isset($postdata['time']) ? $postdata['time'] : '0');
$params['qno'] = isset($_REQUEST['qno']) ? $_REQUEST['qno'] : (isset($postdata['qno']) ? $postdata['qno'] : '0');

//print_r($params);

if($params['qid']!="0" && $params['uid']!="0" && $params['test_id']!="0"){
    $manager = new MockTestManager();
    $manager->saveResponse($params);
    $result['success'] = "true";
}
else{
    $result['success'] = "false";
    $result['error'] = "insufficient data";
}
print_r(json_encode($result));
?>