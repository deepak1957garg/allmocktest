<?php
include_once dirname(__FILE__) . '/../config/Config.php';
include_once dirname(__FILE__) . '/../common/Constants.php';
class CookieManager{

	public static function getAdminUid(){
		$uid=0;
		if(!empty($_COOKIE[Constants::$COOKIE_AUTH]))	$uid=$_COOKIE[Constants::$COOKIE_AUTH];
		return $uid;
	}

	public static function setAdminUid($uid){
		setcookie(Constants::$COOKIE_AUTH,$uid,(time()+(86400*365)),'/',Config::$COOKIE_DOMAIN_PATH);
	}

}
?>