<?php
// include_once dirname(__FILE__) . '/../includes/config/Config.php';
// include_once dirname(__FILE__) . '/../includes/models/ApiResponse.php';
// include_once dirname(__FILE__) . '/../includes/classes/ApiRequestReader.php';
// include_once dirname(__FILE__) . '/../modules/user/classes/UserInfoManager.php';
// include_once dirname(__FILE__) . '/../modules/mocktest/classes/MockTestManager.php';
// header('Cache-Control: max-age=0');
// header('Content-Type: application/json');

// $result = array();
// $result['success'] = "true";
// $result['error'] = "";
// $result['response'] = array();


// $apiResponse = new ApiResponse();
// $apiRequestReader = new ApiRequestReader();

// $entityBody = file_get_contents('php://input');
// $data = json_decode($entityBody,TRUE);
// $postdata = isset($_REQUEST['data']) ? json_decode($_REQUEST['data'],true) : $data;

// $params['uid'] = (isset($_REQUEST['uid']) && $_REQUEST['uid']!="") ? $_REQUEST['uid'] : (isset($postdata['uid']) ? $postdata['uid'] : "0");

// if($params['uid']!=0){
// 	$uinfomanager = new UserInfoManager();
// 	$manager = new MockTestManager();
//     $obj = $manager->getIncompleteNockTests($params['uid']);

//     //$list = $manager->getUserCompletedTestList($params['uid']);

//     $status = "buy";
//     if($obj->getValue('id')!="0"){
//     	if($obj->getValue('is_started')=="0"){
//     		$status = "test";
//     	}
//     	else if($obj->getValue('is_completed')=="0"){
//     		$status = "resume";
//     	}
//     }
//     $test_arr = $manager->getLastCompleteNockTests($params['uid']);

// 	$result['response']['status']=$status;
// 	$result['response']['tests']=$test_arr;
// }
// else{
// 	$result['success'] = false;
// 	$result['error'] = 'data insufficient';
// }
// print_r(json_encode($result));
?>