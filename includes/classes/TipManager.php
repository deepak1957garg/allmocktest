<?php
include_once dirname(__FILE__) . '/../dao/VideoReadDao.php';
include_once dirname(__FILE__) . '/../dao/VideoWriteDao.php';
include_once dirname(__FILE__) . '/../../modules/notification/classes/NotificationManager.php';
include_once dirname(__FILE__) . '/../../modules/notification/classes/UserNotificationManager.php';

class TipManager{
	private $vreadobj;
	private $vwriteobj;
	private $notifManager;
	private $userNotifManager;
	private $users = array();

	public function __construct(){
		$this->vreadobj = new VideoReadDao();
		$this->vwriteobj = new VideoWriteDao();
		$this->notifManager = new NotificationManager();
		$this->userNotifManager = new UserNotificationManager();
	}

	public function addEvent($uid,$vid,$event,$value){
		$arr = $this->vreadobj->getEvent($vid,$uid,$event);
		if(count($arr)>0){
			$this->vwriteobj->editEvent($vid,$uid,$event,$value);
		}
		else{
			$this->vwriteobj->addEvent($vid,$uid,$event,$value);
		}
	}


	public function addNotification($event,$uid,$uid2){
		if($event=="booking"){
			$params = array();
			$params['uid']=$uid2;
			$params['template']="meeting";
			$params['page']="profile";
			$params['iid']=$uid;

			$info=$this->getNotificationInfo($uid);
			$params['info']=json_encode($info);
			
			$notification = $this->notifManager->createObject($params);
			$this->saveUserNotification($notification,$video);
			$params['template']="endorsed_others";
			$notification = $this->notifManager->createObject($params);
			$this->saveUserNotification($notification,$video);
		}
		else if($event=="joined"){
			$params = array();
			$params['uid']=$uid;
			$params['template']="joined";
			$params['page']="profile";
			$params['iid']=$uid;

			$info = $this->getNotificationInfo($uid);
			$params['info']=json_encode($info);
			$notification = $this->notifManager->createObject($params);
			$this->saveUserNotification($notification,$video);
		}
	}

	public function addNotifChat($uid,$message){
			$params = array();
			$params['uid']=$uid;
			$params['template']="chat";
			$params['page']="all";
			$params['is_processed']=1;
			$params['iid']=$uid;
			$info = array();
			$info['message'] = $message;
			$info['uid'] = $uid;
			$params['info']=json_encode($info);
			$notification = $this->notifManager->createObject($params);
			$this->saveUserNotification($notification,array());
	}	

	private function saveUserNotification($notification,$video=array()){
		if($notification->getValue('template')=="meeting"){
			$params['uid']=$video['uid'];
			$params['nid']=$notification->getValue('nid');
			$this->userNotifManager->createObject($params);
		}
		else if($notification->getValue('template')=="joined"){
			$contacts = $this->contactsHandler->getSignedUpContacts($notification->getValue('uid'));
			if(count($contacts)>0){
				$this->vwriteobj->addUserNotifications($notification->getValue('nid'),$contacts);
			}
			//$params['uid']=$video['uid'];
			//$params['nid']=$notification->getValue('nid');
			//$this->userNotifManager->createObject($params);
		}
		else if($notification->getValue('template')=="chat"){
			$params['uid']=$notification->getValue('uid');
			$params['nid']=$notification->getValue('nid');
			$params['is_sent']=1;
			$params['is_seen']=1;
			$this->userNotifManager->createObject($params);
		}
	}

	private function getNotificationInfo($uid){
		$info = array();
		$user = array();
		if(!isset($this->users[$uid])){
			$user = $this->vreadobj->getUser($uid);
			$this->users[$uid] = $user;
		}
		else{
			$user = $this->users[$uid];
		}
		return $user;
	}

	public function updateRemoveStatus($vid,$reason){
		$this->vwriteobj->updateRemoveStatus($vid,$reason);
	}		

}
?>