<?php
include_once dirname(__FILE__) . '/../dao/NotificationReadDao.php';
include_once dirname(__FILE__) . '/../dao/NotificationWriteDao.php';
include_once dirname(__FILE__) . '/../models/Notification.php';
include_once dirname(__FILE__) . '/../models/UserNotification.php';

class UserNotificationManager{
	private $cread;
	private $cwrite;

	public function __construct(){
		$this->cread = new NotificationReadDao();
		$this->cwrite = new NotificationWriteDao();
	}

	public function createAndUpdate($params){
		$error = "";
		$object = new UserNotification();
		try{
			$object = $this->getObject($params['uid'],$params['nid']);
			if($object->getValue('id')==0){
				$object = $this->createObject($params);
			}
			else{
				$object = $this->updateObject($object,$params);
				$object = $this->getObject($params['uid'],$params['uid']);
			}
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function getObject($uid,$nid){
		$object = new UserNotification();
		if($uid!=0 && $nid=0){
			$object = $this->cread->getObject($object,array('uid'=>$uid,'nid'=>$nid));
		}
		return $object;
	}

	public function createObject($params){
		$object = new UserNotification();
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
			foreach($params as $key=>$value){
				if(isset($keys[$key]) && $params[$key]!="" && $params[$key]!=$object->getValue($key)){
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

	public function getUnProcessedNotification(){
		$list = array();
		$object = new UserNotification();
		$arr = $this->cread->getList($object,array('is_sent'=>0));
		foreach ($arr as $values) {
			$object = new UserNotification();
			$object->setObject($values);
			array_push($list,$object);
		}
		return $list;
	}

}