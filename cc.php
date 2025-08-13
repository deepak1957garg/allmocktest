<?php
include_once dirname(__FILE__) . '/modules/educard/classes/EduCardCreator.php';


$qprocess = new EduCardCreator();
	
// $data = file_get_contents(dirname(__FILE__) . '/uploads/varc_cards.json');
// $qprocess->getTopics('VARC');
$data = file_get_contents(dirname(__FILE__) . '/uploads/qa_cards.json');
$qprocess->getTopics('QA');
// $data = file_get_contents(dirname(__FILE__) . '/uploads/dilr_cards.json');
// $qprocess->getTopics('DILR');
//$data = file_get_contents(dirname(__FILE__) . '/uploads/qa.json');
//$data = file_get_contents(dirname(__FILE__) . '/uploads/varc.json');

//$qprocess = new QuestionProcessing('DILR');
$i=0;
if($data != ""){
	echo "hello";
	//print_r($data);
	//echo json_validate($data);
	$arr = json_decode($data,true);
	//print_r($arr);
	echo json_last_error_msg();
	//print_r($arr);
	echo count($arr);
	foreach($arr as $obj){
		$i++;
		$qprocess->saveData($obj);
		//if($i>2)	break;
	}
	//print_r($arr[0]);
	
}
else{
	echo "test";
}
?>