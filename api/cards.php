<?php
include_once dirname(__FILE__) . '/../includes/config/Config.php';
include_once dirname(__FILE__) . '/../includes/models/ApiResponse.php';
include_once dirname(__FILE__) . '/../includes/classes/ApiRequestReader.php';
include_once dirname(__FILE__) . '/../modules/educard/classes/EduCardManager.php';
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
$params['topic_id'] = isset($_REQUEST['top']) ? $_REQUEST['top'] : (isset($postdata['top']) ? $postdata['top'] : "0");

if($params['uid']!=0){
	$manager = new EduCardManager();
	list($list,$total) = $manager->getUserCards($params['uid']);

	$result['response']['total']=$total;
	$result['response']['list']=$list;
}
else{
	$result['success'] = false;
	$result['error'] = 'data insufficient';
}
print_r(json_encode($result));
?>