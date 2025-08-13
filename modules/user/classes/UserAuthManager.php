<?php
$dirname = str_replace("/modules/user/classes", "",dirname(__FILE__));
require_once $dirname . '/vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;

include_once dirname(__FILE__) . '/../dao/UserReadDao.php';
include_once dirname(__FILE__) . '/../dao/UserWriteDao.php';
include_once dirname(__FILE__) . '/../../general/classes/Utils.php';
include_once dirname(__FILE__) . '/../models/AuthUser.php';
include_once dirname(__FILE__) . '/../../notification/classes/EmailNotifications.php';

class UserAuthManager{
	private $cread;
	private $cwrite;
	private $factory;
	private $things_config;
	public function __construct(){
		$dirname = str_replace("/modules/user/classes", "",dirname(__FILE__));
		$this->factory = (new Factory)->withServiceAccount($dirname . "/includes/config/things-app-d3310-firebase-adminsdk-lo8as-22523efa70.json");

		$things_config = [
		    'keyFilePath' => $dirname . "/includes/config/things-app-d3310-firebase-adminsdk-lo8as-22523efa70.json",
		    'projectId' => 'things-app-d3310',
		];

		$this->cread = new UserReadDao();
		$this->cwrite = new UserWriteDao();
	}

	public function createAuthToken($params){
		$arr = array();
		$error = "";
		try{
			$auth = $this->factory->createAuth();
			$uid = $this->getAndSaveAuthUid($params);

			$customToken = $auth->createCustomToken($uid);
			$customTokenString = $customToken->toString();
			$arr['uid'] = $uid;
			$arr['token'] = $customTokenString;
			//print_r($arr);
			if($customTokenString!=""){
				$nmanager = new EmailNotifications();
				$nmanager->sendVerifyEmailMail($uid,$params['email']);
			}
		}
		catch(Exception $ex){ 

		}
		return $arr;
	}

	public function getAndSaveAuthUid($params){
		$object = $this->getAuthObject($params);
		if($object->getValue('uid')==""){
			$object = $this->createAuthObject($params);
		}
		return $object->getValue('uid');
	}

	public function getAuthObject($params){
		$object = new AuthUser();
		$object = $this->cread->getObject($object,array('email'=>$params['email']));
		return $object;
	}

	public function createAuthObject($params){
		$object = new AuthUser();
		$object->setObject($params);
		$object->setValue('uid',Utils::generateUID());
		$object = $this->cwrite->createObject($object,false);
		$params['uid']=$object->getValue('uid');
		//$object = $this->getAuthObject($params);
		return $object;
	}

}
?>