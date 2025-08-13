<?php
include_once dirname(__FILE__) . '/../common/Constants.php';
include_once dirname(__FILE__) . '/../common/CookieManager.php';
include_once dirname(__FILE__) . '/../dao/UserReadDao.php';

class CredsVerification{
	
	function __construct(){ }


	public static function checkAdminUserVerification(){
		$uid = CookieManager::getAdminUid();
		if($uid!==0){
			$uid=self::isAdmin($uid);
		}
		return $uid;
	}

	public static function setAdminUser($uid){
		CookieManager::setAdminUid($uid);
	}

	public static function isAdmin($uid){
		$uread = new UserReadDao();
		$admin_arr = $uread->getAdminUsers();
		if(!isset($admin_arr[$uid]))	$uid=0;
		return $uid;
	}

}
?>