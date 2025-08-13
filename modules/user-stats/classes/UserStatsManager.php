<?php
include_once dirname(__FILE__) . '/../dao/UserStatsReadDao.php';
include_once dirname(__FILE__) . '/../dao/UserStatsWriteDao.php';
include_once dirname(__FILE__) . '/../models/UserStats.php';

class UserStatsManager{
	private $cread;
	private $cwrite;

	public function __construct(){
		$this->cread = new UserStatsReadDao();
		$this->cwrite = new UserStatsWriteDao();
	}

	public function createAndUpdate($uid,$event,$status=1){
		$object = new User();
		$error = "";
		try{
			$object = $this->getObject($uid,$event);
			if($object->getValue('id')==0){
				$object = $this->createObject($uid,$event,$status);
			}
			else{
				$this->updateObject($object,$status);
				$object = $this->getObject($uid,$event);
			}
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function getObject($uid,$event){
		$object = new UserStats();
		$object = $this->cread->getObject($object,array('uid'=>$uid,'event'=>$event));
	}

	private function createObject($uid,$event,$status){
		$object = new UserStats();
		$object->setValue('uid',$uid);
		$object->setValue('event',$event);
		$object->setValue('num_count',$status);
		$object = $this->cwrite->createObject($object,true);
		return $object;
	}

	private function updateObject($object,$status=1){
		$changes = array();
		try{
			$num_count = $object->getValue('num_count') + $status;

			$changes['num_count']=$num_count;
			if(count($changes)){
				$this->cwrite->updateObject($object,$changes);
			}
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function getObjects($uid){
		$list = array();
		$object = new UserStats();
		$arr = $this->cread->getList($object,array('uid'=>$uid));
		foreach($arr as $content){
			$object = new UserStats();
			$object->setObject($content);
			array_push($list,$object);
		}
		return $list;
	}

	public function getList($uid){
		$list = array();
		$objects = $this->getObjects($uid);
		foreach($arr as $object){
			array_push($list,$object->getObject());
		}
		return $list;
	}

	public function getStats($uid){
		$stats = array();
		$stats['endorse']=0;
		$stats['buy']=0;
		$stats['upload']=0;
		$stats['messages']=0;
		$stats['unbox']=0;
		$list = $this->cread->getUserStats($uid);
		foreach($list as $object){
			if($object['event']=='endorse' && $object['data']=="1"){
				$stats['endorse']=$object['cnt'];
			}
			else if($object['event']=='buy'){
				$stats['buy']=$object['cnt'];
			}
		}
		$stats['upload'] = $this->cread->getUserVideoCountByType($uid,1);
		$stats['unbox'] = $this->cread->getUserVideoCountByType($uid,2);
		$stats['messages'] = $this->cread->getUserMessageCount($uid);
		return $stats;
	}

	public function getUnseenMessageCount($uid){
		$count = $this->cread->getUserMessageCount($uid);
		return $count;
	}

}