<?php
include_once dirname(__FILE__) . '/../modules/mocktest/classes/MockTestManager.php';
include_once dirname(__FILE__) . '/../modules/educard/classes/EduCardManager.php';

$manager = new MockTestManager();
$ecmanager = new EduCardManager();
$uids = $manager->getAllUsers();
foreach($uids as $uid){
	$manager->saveSubjectDifficultySwotAnalysis($uid);
	$ecmanager->getAndCreateUserCards($uid);
}
?>