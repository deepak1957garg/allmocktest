<?php
include_once dirname(__FILE__) . '/../modules/notification/classes/NotificationManager.php';
include_once dirname(__FILE__) . '/../modules/notification/classes/NotificationSender.php';

$nManager = new NotificationManager();
$nSender = new NotificationSender();

while(1){
	$notifs = $nManager->getUnProcessedNotifications();
	//print_r($notifs);
	if(count($notifs)==0){
		break;
	}
	else{
		foreach($notifs as $notif){
			$nSender->sentNotification($notif);
		}
	}
}
?>