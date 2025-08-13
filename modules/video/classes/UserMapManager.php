<?php
include_once dirname(__FILE__) . '/../../../includes/config/Config.php';
include_once dirname(__FILE__) . '/../dao/UserMapReadDao.php';
include_once dirname(__FILE__) . '/../dao/UserMapWriteDao.php';
include_once dirname(__FILE__) . '/../cache/UserMapCachingDao.php';
include_once dirname(__FILE__) . '/../models/UserMap.php';
include_once dirname(__FILE__) . '/../models/Memes.php';
include_once dirname(__FILE__) . '/../../general/classes/Utils.php';

class UserMapManager{
	private $cread;
	private $cwrite;
	private $ccache;

	public function __construct(){
		$this->cread = new UserMapReadDao();
		$this->cwrite = new UserMapWriteDao();
		$this->ccache = new UserMapCachingDao();
	}

	public function createAndUpdate($params){
		$object = new UserMap();
		$error = "";
		try{
			if(!isset($params['uid']))	$params['uid']=0;
			if(isset($params['cc']) && $params['cc']!='' && $params['mobile']!='')	$params['mobile'] = $params['cc'] . $params['mobile'];
			$object = $this->getUserMap($params['uid'],$params['fb_id'],$params['mobile']);
			if($object->getValue('tuid')==0){
				$object = $this->createObject($params);
			}
			else{
				$object = $this->updateObject($object,$params);
			}
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function getUserMap($uid=0,$fb_id='',$mobile=''){
		$object = new UserMap();
		if($uid!=0){
			$object = $this->cread->getObject($object,array('uid'=>$uid));
		}
		else if($mobile!=''){
			$object = $this->cread->getObject($object,array('mobile'=>$mobile));
		}
		else if($fb_id!=''){
			$object = $this->cread->getObject($object,array('fb_id'=>$fb_id));
		}
		return $object;
	}

	public function createObject($params){
		$object = new UserMap();
		try{
			$object->setValue('uid',Utils::generateUID());
			$object->setValue('mobile',$params['mobile']);
			$object->setValue('fb_id',$params['fb_id']);
			$object->setValue('tuid',Utils::generateUID());
			$object = $this->cwrite->createObject($object,true);
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function updateObject($object,$params){
		$changes = array();
		try{
			if(isset($params['mobile'])  && $params['mobile']!='' && $object->getValue('mobile')!='' && $params['mobile']!=$object->getValue('mobile'))	$changes['mobile']=$params['mobile'];
			if(isset($params['fb_id'])  && $params['fb_id']!='' && $object->getValue('fb_id')!='' && $params['fb_id']!=$object->getValue('fb_id'))	$changes['fb_id']=$params['fb_id'];

			if(count($changes)>0){
				$this->cwrite->updateObject($object,$changes);
			}
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function getUsersFromMobileNos($mobile_nos){
		$object = new UserMap();
		$arr = $this->cread->getListByIds($object,$mobile_nos);
		return $arr;
	}

	public function getMemes(){
		$names = array("Harirahan","Shruti","Dev","Vishakha","Ramya","Deodhar","Sunil","Anupama","Raheel","Riya");
		$list = array();
		$object = new Memes();
		$arr = $this->cread->getMemes();
		$i=0;
		foreach($arr as $obj){
			$object1 = new Memes();
			$object1->setValue('id',$obj['id']);
			$object1->setUrl($obj['img']);
			$name = $names[($i%10)];
			$object1->setValue('name',$name);
			array_push($list,$object1->getObject());
			$i++;
		}
		return $list;
	}	

}