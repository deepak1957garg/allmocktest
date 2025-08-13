<?php
include_once dirname(__FILE__) . '/../../../includes/config/Config.php';
include_once dirname(__FILE__) . '/../dao/UserMapReadDao.php';
include_once dirname(__FILE__) . '/../dao/UserMapWriteDao.php';
include_once dirname(__FILE__) . '/../cache/UserMapCachingDao.php';
include_once dirname(__FILE__) . '/../models/UserData.php';
include_once dirname(__FILE__) . '/../models/UserInfo.php';
include_once dirname(__FILE__) . '/../models/UserMap.php';
include_once dirname(__FILE__) . '/../../general/classes/Utils.php';

class UserDataManager{
	private $cread;
	private $cwrite;
	private $ccache;

	public function __construct(){
		$this->cread = new UserMapReadDao();
		$this->cwrite = new UserMapWriteDao();
		$this->ccache = new UserMapCachingDao();
	}

	public function createAndUpdate($params){
		$object = new UserData();
		$error = "";
		try{
			$object = $this->cread->getObject($object,array('tuid'=>$params['tuid']));
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

	public function createObject($params){
		$object = new UserData();
		try{
			$info_object = new UserInfo();
			$info_object->setObject($params);
			$object->setValue('info',$info_object->getObject($info_object->RES_FORMAT_JSON));
			$object->setValue('tuid',$params['tuid']);
			$object = $this->cwrite->createObject($object,true);
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function updateObject($object,$params){
		$changes = array();
		try{
			$info = json_decode($object->getValue('info'),true);
			$info = $this->updateInfoObject($info,$params);

			$changes['info'] = json_encode($info);
			if(count($changes)>0){
				$this->cwrite->updateObject($object,$changes);
			}
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function updateInfoObject($info,$params){
		$object = new UserInfo();
		try{
			$object->setObject($info);
			foreach($info as $key=>$val){
				if(isset($params[$key]) && $params[$key]!='' && $val!=$params[$key]){
					$object->setValue($key,$params[$key]);
				}
			}

			$info = $object->getObject();
		}
		catch(Exception $ex){ }
		return $info;
	}	

	public function getUserData($data){
		$info = new UserInfo();
		//print_r($data);
		$info->setObject($data);

		$mobile = '';
		if(isset($data['mobile']))	$mobile = $data['mobile'];
		else if(isset($data['phone']))	$mobile = $data['phone'];
		$mobile = str_replace("+","",$mobile);
		$info->setValue('mobile',$mobile);
		if(isset($data['uId'])) $info->setValue('fb_id',$data['uId']);

		print_r($info->getObject());

		$object = new UserMap();
		$object = $this->cread->getObject($object,array('mobile'=>$mobile));

		if($object->getValue('tuid')==0){
			$object->setValue('uid',Utils::generateUID());
			$object->setValue('mobile',$info->getValue('mobile'));
			$object->setValue('fb_id',$info->getValue('fb_id'));
			$object->setValue('tuid',Utils::generateUID());
			$object = $this->cwrite->createObject($object,true);
		}
		else{
		}

		$params = $info->getObject();
		$params['tuid']=$object->getValue('tuid');
		$this->createAndUpdate($params);


	}	

}