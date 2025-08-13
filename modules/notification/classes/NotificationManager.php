<?php
include_once dirname(__FILE__) . '/../dao/NotificationReadDao.php';
include_once dirname(__FILE__) . '/../dao/NotificationWriteDao.php';
include_once dirname(__FILE__) . '/../models/Notification.php';
include_once dirname(__FILE__) . '/../models/UserNotification.php';
include_once dirname(__FILE__) . '/../models/AppNotification.php';
include_once dirname(__FILE__) . '/../classes/NotificationTemplates.php';
include_once dirname(__FILE__) . '/../classes/UserNotificationManager.php';

class NotificationManager{
	private $cread;
	private $cwrite;
	private $userNotifManager;

	public function __construct(){
		$this->cread = new NotificationReadDao();
		$this->cwrite = new NotificationWriteDao();
		$this->userNotifManager = new UserNotificationManager();
	}

	public function createAndUpdate($params){
		$error = "";
		$object = new Notification();
		try{
			$object = $this->getNotification($params['nid']);
			if($object->getValue('nid')==0){
				$object = $this->createObject($params);
			}
			else{
				$object = $this->updateObject($object,$params);
			}
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function getNotification($nid=0){
		$object = new Notification();
		if($nid!=0){
			$object = $this->cread->getObject($object,array('nid'=>$nid));
		}
		return $object;
	}

	public function createObject($params){
		$object = new Notification();
		try{
			$keys = $object->getKeys();
			foreach($keys as $key){
				if(isset($params[$key])){
					$object->setValue($key,$params[$key]);
				}
			}
			$object = $this->cwrite->createObject($object);
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function updateObject($object,$params){
		$changes = array();
		try{
			$keys = $object->getKeys();
			foreach($keys as $key){
				if(isset($params[$key]) && $params[$key]!="" && $params[$key]!=$object->getValue($key)){
					$changes[$key]=$params[$key];
				}
			}

			if(count($changes)>0){
				$this->cwrite->updateObject($object,$changes);
			}
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function getUnProcessedNotifications(){
		$list = array();
		$object = new Notification();
		$arr = $this->cread->getList($object,array('is_processed'=>0));
		foreach ($arr as $values) {
			$object = new Notification();
			$object->setObject($values);
			array_push($list,$object);
		}
		return $list;
	}

	public function changeNotificationStatus($uid,$status=1){
		$this->cwrite->setUserNotificationIsSeen($uid,1);
	}

	public function getUserNotificationStatus($uid){
		//$uid = "172931255382118841";
		$arr = $this->getNotificationList($uid);
		$appNotification = new AppNotification();
		if(count($arr)>0){
			$appNotification->setObject($arr[0]);
		}
		return $appNotification->getObject();
	}

	public function getNotificationList($uid){
		$arr = array();
		$list = $this->cread->getNotificationList($uid);
		if(count($list)<10){
			$list2 = $this->cread->getSeenNotificationList($uid);
			$list = array_merge($list,$list2);
			$list = array_values($list);
			$list = array_slice($list,0,10);
		}
		usort($list, function ($item1, $item2) {
		    return $item1['created_on'] <=> $item2['created_on'];
		});
		$list = array_reverse($list);

		list($bookings,$users) = $this->getNotificationUsersAndBookings($list);
		//print_r($bookings);
		//print_r($bookings);
		foreach ($list as $obj) {
			$object = new AppNotification();
			$object->setValue('id',$obj['id']);
			$object->setValue('nid',$obj['nid']);
			$object->setValue('template',$obj['template']);
			$object->setValue('page',$obj['page']);
			$object->setValue('iid',$obj['iid']);

			$info = json_decode($obj['info'],true);
			if($obj['page']=="booking"){
				$text = NotificationTemplates::$TEMPLATES[$obj['template']]['text'];
				if($info['seller']!=$uid){
					if(isset($users[$info['seller']])){
						$object->setValue('uid',$info['seller']);
						if($users[$info['seller']]['isCdn']==0){
							$object->setValue('pic',Constants::$VIDEO_NON_CDN_PATH . $users[$info['seller']]['upic'] . '?alt=media');
						}
						else if($users[$info['seller']]['thumb']!=''){
							$object->setValue('pic',Constants::$VIDEO_CDN_PATH . $users[$info['seller']]['thumb']);
						}
						else if($users[$info['seller']]['upic']!=''){
							$object->setValue('pic',Constants::$VIDEO_CDN_PATH . $users[$info['seller']]['upic']);
						}
						//$object->setValue('pic',Constants::$VIDEO_CDN_PATH . $users[$info['seller']]['upic']);
						if(isset($users[$info['seller']]['webp']))	$object->setValue('webp',Constants::$VIDEO_CDN_PATH . $users[$info['seller']]['webp']);
						$object->setValue('name',trim($users[$info['seller']]['name']));
					}
				}
				else{
					if(isset($users[$info['customer']])){
						$object->setValue('uid',$info['customer']);
						if($users[$info['customer']]['isCdn']==0){
							$object->setValue('pic',Constants::$VIDEO_NON_CDN_PATH . $users[$info['customer']]['upic'] . '?alt=media');
						}
						else if($users[$info['customer']]['thumb']!=''){
							$object->setValue('pic',Constants::$VIDEO_CDN_PATH . $users[$info['customer']]['thumb']);
						}
						else if($users[$info['customer']]['upic']!=''){
							$object->setValue('pic',Constants::$VIDEO_CDN_PATH . $users[$info['customer']]['upic']);
						}
						//$object->setValue('pic',Constants::$VIDEO_CDN_PATH . $users[$info['customer']]['upic']);
						if(isset($users[$info['customer']]['webp']))	$object->setValue('webp',Constants::$VIDEO_CDN_PATH . $users[$info['customer']]['webp']);
						$object->setValue('name',trim($users[$info['customer']]['name']));
					}
				}
				$text = NotificationTemplates::$TEMPLATES[$obj['template']]['text'];
				if(isset($info['suggestedAvailability']))	$text = str_replace("{availability}",date("jS M, h A",strtotime($info['suggestedAvailability'])), $text);
				if(isset($info['suggestedLocations']))	$text = str_replace("{place}",ucwords(strtolower($info['suggestedLocations'])), $text);
				
				$text = str_replace("{name}",$object->getValue('name'), $text);
				if(isset($info['sellerName']))	$text = str_replace("{sellerName}",ucwords(strtolower($info['sellerName'])), $text);
				if(isset($info['customerName']))	$text = str_replace("{customerName}",ucwords(strtolower($info['customerName'])), $text);
				if($obj['template']=="meeting_code" || $obj['template']=="meeting_code2"){
					if($info['seller']!=$uid){
						$text = str_replace("{code}",$info['sellerCode'], $text);
					}
					else{
						$text = str_replace("{code}",$info['customerCode'], $text);
					}
				}
				$object->setValue('text',$text);
			}
			// else if($obj['template']=="chat"){
			// 	//print_r($info);
			// 	$object->setValue('uid',$users[$info['uid']]['uid']);
			// 	$object->setValue('name',$users[$info['uid']]['name']);
			// 	$object->setValue('text',$info['message']);
			// 	//$info['title'] = $info['message'];
			// }
			// else{
			// 	if(isset($info['uid'])){
			// 		if(isset($users[$info['uid']])){
			// 			$object->setValue('uid',$users[$info['uid']]['uid']);
			// 			$object->setValue('name',$users[$info['uid']]['name']);
			// 			if($users[$info['uid']]['pic']!="")	$object->setValue('pic',$this->getPic($users[$info['uid']]['pic']));
			// 		}
			// 		else{
			// 			$object->setValue('uid',$info['uid']);
			// 			$object->setValue('name',$info['title']);
			// 			$object->setValue('pic',Constants::$VIDEO_NON_CDN_PATH . $info['pic']."?alt=media");
			// 			if($obj['template']=="ready_for_sell" || $obj['template']=="ready_for_unbox"){
			// 				//$object->setValue('pic',Constants::$VIDEO_CDN_PATH . $info['pic']);
			// 			}
			// 			else{
			// 				if($info['pic']!="")	$object->setValue('pic',$this->getPic($info['pic']));
			// 			}
			// 		}
			// 	}
			// }

			// if($obj['page']=="item"){
			// 	if(isset($videos[$obj['iid']])){
			// 		$object->setValue('vid',$videos[$obj['iid']]['vid']);
			// 		$object->setValue('vname',$videos[$obj['iid']]['vname']);
			// 		$object->setValue('vpic',Constants::$VIDEO_CDN_PATH . $videos[$obj['iid']]['thumb']);
			// 		$object->setValue('gif',Constants::$VIDEO_CDN_PATH . $videos[$obj['iid']]['gif']);
			// 		$object->setValue('curr',$videos[$obj['iid']]['curr']);
			// 		if($videos[$obj['iid']]['sale_amount']!=0){
			// 			$object->setValue('amount',$videos[$obj['iid']]['sale_amount']);
			// 		}
			// 		else{
			// 			$object->setValue('amount',$info['amount']);
			// 		}
			// 	}
			// 	else{
			// 		$object->setValue('vid',$obj['iid']);
			// 		$object->setValue('vname',"");
			// 		$object->setValue('vpic',Constants::$VIDEO_CDN_PATH . $info['pic']);
			// 		$object->setValue('curr',"₹");
			// 	}
			// }

			if(isset($info['is_seen'])) $object->setValue('is_seen',$obj['is_seen']);
			if(isset($info['is_seen'])) $object->setValue('isseen',$obj['is_seen']);
			$object->setValue('time',strtotime($obj['created_on']));
			
			array_push($arr,$object->getObject());
		}
		return $arr;
	}

	private function getNotificationUsersAndBookings($list){
		$users = array();
		$bookings = array();
		$uids = array();
		$bids = array();
		foreach ($list as $obj) {
			if($obj['page']=="booking"){
				array_push($bids,$obj['iid']);
				$info = json_decode($obj['info'],true);
				$bookings[$obj['iid']] = $info;
				if(isset($info['provider'])) array_push($uids,$info['provider']);
				if(isset($info['taker'])) array_push($uids,$info['taker']);
				if(isset($info['seller'])) array_push($uids,$info['seller']);
				if(isset($info['customer'])) array_push($uids,$info['customer']);
			}
		}

		if(count($uids)>0){
			$uids = array_values(array_unique($uids));
			$users = $this->cread->getUsers($uids);
		}
		return array($bookings,$users);
	}

	private function getPic($pic){
		if($pic!=""){
			if(!strpos($pic,"googleusercontent.com")){
				$pic = Constants::$VIDEO_NON_CDN_PATH . $pic ."?alt=media";
			}
		}
		return $pic;
	}

	public function startBookingNotification($booking){
		$params = array();
		$params['uid']=$booking->getValue('customer');
		$params['template']="ask_meeting";
		$params['page']="booking";
		$params['iid']=$booking->getValue('id');

		$info=$booking->getObject();
		$params['info']=json_encode($info);

		$notification = $this->createObject($params);
		
		$params['nid']=$notification->getValue('nid');
		$params['uid']=$booking->getValue('seller');
		$this->userNotifManager->createObject($params);

		$params['template']="meet_confirm_wait";
		$params['uid']=$booking->getValue('customer');
		$notification = $this->createObject($params);
		
		$params['nid']=$notification->getValue('nid');
		$params['uid']=$booking->getValue('customer');
		$this->userNotifManager->createObject($params);
	}

	public function reminderBookingNotification($booking){
			$params = array();
			$params['uid']=$booking->getValue('customer');
			$params['template']="reminder";
			$params['page']="booking";
			$params['iid']=$booking->getValue('id');

			$info=$booking->getObject();
			$params['info']=json_encode($info);

			$notification = $this->createObject($params);
			
			$params['nid']=$notification->getValue('nid');
			$params['uid']=$booking->getValue('seller');
			$this->userNotifManager->createObject($params);

			$params['template']="reminder2";
			$params['uid']=$booking->getValue('customer');
			$notification = $this->createObject($params);
			
			$params['nid']=$notification->getValue('nid');
			$params['uid']=$booking->getValue('customer');
			$this->userNotifManager->createObject($params);
	}

	public function addBookingNotification($booking,$status){
		if($status == 0){
			$params = array();
			$params['uid']=$booking->getValue('customer');
			$params['template']="ask_meeting";
			$params['page']="booking";
			$params['iid']=$booking->getValue('id');

			$info=$booking->getObject();
			$params['info']=json_encode($info);

			$notification = $this->createObject($params);
			
			$params['nid']=$notification->getValue('nid');
			$params['uid']=$booking->getValue('seller');
			$this->userNotifManager->createObject($params);

			$params['template']="meet_confirm_wait";
			$params['uid']=$booking->getValue('customer');
			$notification = $this->createObject($params);
			
			$params['nid']=$notification->getValue('nid');
			$params['uid']=$booking->getValue('customer');
			$this->userNotifManager->createObject($params);
		}
		else if($status == 2){
			$params = array();
			$params['uid']=$booking->getValue('seller');
			$params['template']="declined";
			$params['page']="booking";
			$params['iid']=$booking->getValue('id');

			$info=$booking->getObject();
			$params['info']=json_encode($info);

			$notification = $this->createObject($params);
			
			$params['nid']=$notification->getValue('nid');
			$params['uid']=$booking->getValue('customer');
			$this->userNotifManager->createObject($params);
		}
		else if($status == 3){
			$params = array();
			$params['uid']=$booking->getValue('seller');
			$params['template']="expired";
			$params['page']="booking";
			$params['iid']=$booking->getValue('id');

			$info=$booking->getObject();
			$params['info']=json_encode($info);

			$notification = $this->createObject($params);
			
			$params['nid']=$notification->getValue('nid');
			$params['uid']=$booking->getValue('customer');
			$this->userNotifManager->createObject($params);
		}
		else if($status == 1){
			$params = array();
			$params['uid']=$booking->getValue('seller');
			$params['template']="confirmed";
			$params['page']="booking";
			$params['iid']=$booking->getValue('id');

			$info=$booking->getObject();
			$params['info']=json_encode($info);

			$notification = $this->createObject($params);
			
			$params['nid']=$notification->getValue('nid');
			$params['uid']=$booking->getValue('customer');
			$this->userNotifManager->createObject($params);

			$params['template']="meeting";
			$params['uid']=$booking->getValue('seller');
			$notification = $this->createObject($params);

			$params['nid']=$notification->getValue('nid');
			$params['uid']=$booking->getValue('customer');
			$this->userNotifManager->createObject($params);
		}
		else if($status == 4){
			$params = array();
			$params['uid']=$booking->getValue('seller');
			$params['template']="meeting_code";
			$params['page']="booking";
			$params['iid']=$booking->getValue('id');

			$info=$booking->getObject();
			$params['info']=json_encode($info);

			$notification = $this->createObject($params);
			
			$params['nid']=$notification->getValue('nid');
			$params['uid']=$booking->getValue('customer');
			$this->userNotifManager->createObject($params);

			$params['template']="meeting_code2";
			$params['uid']=$booking->getValue('customer');
			$notification = $this->createObject($params);

			$params['nid']=$notification->getValue('nid');
			$params['uid']=$booking->getValue('seller');
			$this->userNotifManager->createObject($params);
		}
		else if($status == 7){
			$params = array();
			$params['uid']=$booking->getValue('seller');
			$params['template']="completed";
			$params['page']="booking";
			$params['iid']=$booking->getValue('id');

			$info=$booking->getObject();
			$params['info']=json_encode($info);

			$notification = $this->createObject($params);
			
			$params['nid']=$notification->getValue('nid');
			$params['uid']=$booking->getValue('customer');
			$this->userNotifManager->createObject($params);

			$params['template']="completed2";
			$params['uid']=$booking->getValue('customer');
			$notification = $this->createObject($params);

			$params['nid']=$notification->getValue('nid');
			$params['uid']=$booking->getValue('seller');
			$this->userNotifManager->createObject($params);

		}
	}


	// public function addBookingNotification($booking){
	// 	$params = array();
	// 	$params['uid']=$booking->getValue('taker');
	// 	$params['template']="meeting";
	// 	$params['page']="booking";
	// 	$params['iid']=$booking->getValue('id');

	// 	$info=$booking->getObject();
	// 	$params['info']=json_encode($info);

	// 	$this->createBookingUserNotif($params,$booking->getValue('seller'),$booking->getValue('customer'));

	// 	$params['template']="meeting_code";
	// 	$this->createBookingUserNotif($params,$booking->getValue('seller'),$booking->getValue('customer'));

	// 	$params['template']="ask_code";
	// 	$this->createBookingUserNotif($params,$booking->getValue('seller'),$booking->getValue('customer'));
	// }

	private function createBookingUserNotif($params,$provider,$taker){
		$notification = $this->createObject($params);
		$params['nid']=$notification->getValue('nid');

		$params['uid']=$provider;
		$this->userNotifManager->createObject($params);

		$params['uid']=$taker;
		$this->userNotifManager->createObject($params);
	}

	// private function getNotificationInfo($uid){
	// 	$info = array();
	// 	$user = array();
	// 	if(!isset($this->users[$uid])){
	// 		$user = $this->cread->getUserInfo($uid);
	// 		$this->users[$uid] = $user;
	// 	}
	// 	else{
	// 		$user = $this->users[$uid];
	// 	}
	// 	return $user;
	// }

}