<?php
include_once dirname(__FILE__) . '/../includes/config/Config.php';
include_once dirname(__FILE__) . '/../includes/models/ApiResponse.php';
include_once dirname(__FILE__) . '/../includes/classes/ApiRequestReader.php';
include_once dirname(__FILE__) . '/../modules/user/classes/UserInfoManager.php';
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

if($params['uid']!=0){
	$uinfomanager = new UserInfoManager();
	$manager = new MockTestManager();
    $obj = $manager->getIncompleteNockTestsStatus($params['uid']);

    $livetests = $manager->getAllIncompletedTestList($params['uid']);
    $status_arr = array();

    $list = $manager->getUserCompletedTestList($params['uid']);
    $subjects = $manager->getSubjectLevelSwot($params['uid']);
    $topics = $manager->getTopicLevelSwot($params['uid']);

    $status = "buy";
   	$status_arr['action'] = '/products/cat-mock-test-2025';
    $status_arr['name'] = 'Mock Test';
    if($obj['id']!="0"){
    	if($obj['is_started']=="0"){
    		$status = "test";
    		$status_arr['status'] = 'test';
    		$status_arr['action'] = '/pages/mocktest?tst=' . $obj['id'] .'&ref=my-dashboard';
    		$status_arr['name'] = $obj['template'];
    	}
    	else if($obj['is_completed']=="0"){
    		$status = "resume";
    		$status_arr['status'] = 'resume';
    		$status_arr['action'] = '/pages/mocktest?tst=' . $obj['id'] .'&ref=my-dashboard';
    		$status_arr['name'] = $obj['template'];
    	}
    }
    if(count($list) == 0){
	    $template = $manager->getRecommendedTest($params['uid']);
    	$status_arr['status'] = 'buy';
    	$status_arr['action'] = '/products/cat-mock-test-2025?variant=' . $template['shopify_variant_id'];
    	$status_arr['name'] = $template['template_name'];
    }

	$result['response']['statuses']=$status_arr;
	$result['response']['status']=$livetests;
	$result['response']['topics_stats']=$topics;
	$result['response']['subjects']=$subjects;
	$result['response']['tests']=$list;
}
else{
	$result['success'] = false;
	$result['error'] = 'data insufficient';
}
print_r(json_encode($result));
?>