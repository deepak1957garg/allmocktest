<?php
include_once dirname(__FILE__) . '/../includes/config/Config.php';
include_once dirname(__FILE__) . '/../includes/models/ApiResponse.php';
include_once dirname(__FILE__) . '/../includes/classes/ApiRequestReader.php';
include_once dirname(__FILE__) . '/../modules/mocktest/classes/TestAnalyst.php';
include_once dirname(__FILE__) . '/../modules/mocktest/classes/MockTestManager.php';
header('Cache-Control: max-age=0');
header('Content-Type: application/json');

$result = array();
$result['success'] = "true";
$result['error'] = "";
$result['response'] = array();


$apiResponse = new ApiResponse();
$apiRequestReader = new ApiRequestReader();

$entityBody = file_get_contents('php://input');
$data = json_decode($entityBody,TRUE);
$postdata = isset($_REQUEST['data']) ? json_decode($_REQUEST['data'],true) : $data;

$params['uid'] = (isset($_REQUEST['uid']) && $_REQUEST['uid']!="") ? $_REQUEST['uid'] : (isset($postdata['uid']) ? $postdata['uid'] : "0");
$params['test_id'] = isset($_REQUEST['test_id']) ? $_REQUEST['test_id'] : (isset($postdata['test_id']) ? $postdata['test_id'] : "0");
if($params['test_id']=="0"){
	$params['test_id'] = isset($_REQUEST['tst']) ? $_REQUEST['tst'] : (isset($postdata['tst']) ? $postdata['tst'] : "0");
}
$params['qno'] = isset($_REQUEST['qno']) ? $_REQUEST['qno'] : (isset($postdata['qno']) ? $postdata['qno'] : "0");
$params['subject'] = isset($_REQUEST['sub']) ? $_REQUEST['sub'] : (isset($postdata['sub']) ? $postdata['sub'] : "");
$params['topic_id'] = isset($_REQUEST['top']) ? $_REQUEST['top'] : (isset($postdata['top']) ? $postdata['top'] : "0");
$params['difficulty'] = isset($_REQUEST['diff']) ? $_REQUEST['diff'] : (isset($postdata['diff']) ? $postdata['diff'] : "");

if($params['uid']!=0){
	$analyst = new TestAnalyst();
	$manager = new MockTestManager();
	$qlist = $analyst->getQuestionsList($params['uid'],$params['test_id'],$params['subject'],$params['difficulty'],$params['topic_id']);
	//$stats = $manager->getTestStats($params['test_id']);
	if($params['test_id']!=0){
		$stats = $manager->getTestStats($params['test_id']);
	}
	else if($params['topic_id']!=0){
		$stats = $analyst->getTopicStats($params['topic_id'],$params['difficulty'],$qlist);
	}
	else{
		$stats = $analyst->getSubjectStats($params['subject'],$params['difficulty'],$qlist);
	}

	$result['response']['stats']=$stats;
	$result['response']['qlist']=$qlist;
}
else{
	$result['success'] = false;
	$result['error'] = 'data insufficient';
}
print_r(json_encode($result));
?>