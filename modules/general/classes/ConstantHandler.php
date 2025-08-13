<?php
include_once dirname(__FILE__) . '/../../../includes/config/Config.php';
include_once dirname(__FILE__) . '/../dao/GeneralReadDao.php';
include_once dirname(__FILE__) . '/../dao/GeneralWriteDao.php';
include_once dirname(__FILE__) . '/../models/Constant.php';

class ConstantHandler{
	private $cread;
	private $cwrite;

	public function __construct(){
		$this->cread = new GeneralReadDao();
		$this->cwrite = new GeneralWriteDao();
	}

	public function createAndUpdate($key,$value){
		$object = new Constant();
		try{
			$object = $this->getObject($key);
			if($object->getValue('id')!=0){
				$this->updateObject($object,$key,$value);
			}
			else{
				$object = $this->createObject($key,$value);
			}
			$object = $this->getObject($key);
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function createObject($key,$value){
		$object = new Constant();
		try{
			$object->setValue("key",$key);
			$object->setValue("val1",$value);
			$object = $this->cwrite->createObject($object);
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function updateObject($object,$key,$value){
		$changes = array();
		try{
			$changes['val1']=$value;
			if(count($changes)>0){
				$this->cwrite->updateObject($object,$changes);
			}
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function getObject($key){
		$constant = new Constant();
		$constant = $this->cread->getObject($constant,array('key'=>$key));
		return $constant;
	}

}