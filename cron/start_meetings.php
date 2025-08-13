<?php
include_once dirname(__FILE__) . '/../modules/user/classes/UserInfoManager.php';
include_once dirname(__FILE__) . '/../modules/notification/classes/NotificationManager.php';
include_once dirname(__FILE__) . '/../modules/user/models/Booking.php';

$notifManager = new NotificationManager();
$uinfomanager = new UserInfoManager();

while(1){
	$bookings = $uinfomanager->getBookingsToBeStarted();
	//print_r($bookings);
	if(count($bookings)==0){
		break;
	}
	else{
		foreach($bookings as $booking){
			$uinfomanager->updateBookingStatus($booking['id'],$booking['seller'],4);
			$booking['status'] = 4;
			$booking_obj = new Booking();
			$booking_obj->setObject($booking);
			$notifManager->addBookingNotification($booking_obj,$booking['status']);
		}
	}
	break;
}
?>