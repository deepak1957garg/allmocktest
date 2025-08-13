<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class UserInfo2 extends DataModel{

	public function __construct(){
		$this->datamodel['uid']="0";
		$this->datamodel['name']='';
		$this->datamodel['uname']='';
		$this->datamodel['fbid']='';
		$this->datamodel['email']='';
		$this->datamodel['pic']='';
		$this->datamodel['upic']='';
		$this->datamodel['cafes']='';
		$this->datamodel['availability']='';
		$this->datamodel['vid']="0";
		$this->datamodel['path']="";
		$this->datamodel['thumb']="";
		$this->datamodel['gif']="";
		$this->datamodel['webp']="";
		$this->datamodel['iscdn']="0";
		$this->datamodel['etotal']="0";
		$this->datamodel['epaid']="0";
		$this->datamodel['eremaining']="0";
		$this->datamodel['paypalid']="";
		$this->datamodel['lat']="";
		$this->datamodel['long']="";
	}

	public function getInfo(){
		$user = array();
		$user = $this->getObject();

		if($user['path']!=""){
			if($user['iscdn']==0){
				$user['path'] = Constants::$VIDEO_NON_CDN_PATH . $user['path'] . "?alt=media";
			}
			else{
				$user['path'] = Constants::$VIDEO_CDN_PATH . $user['path'];
			}
		}
		if($user['thumb']!=""){
			if($user['iscdn']==0){
				$user['thumb'] = Constants::$VIDEO_NON_CDN_PATH . $user['thumb'] . "?alt=media";
			}
			else{
				$user['thumb'] = Constants::$VIDEO_CDN_PATH . $user['thumb'];
			}
		}
		if($user['gif']!=""){
			if($user['iscdn']==0){
				$user['gif'] = Constants::$VIDEO_NON_CDN_PATH . $user['gif'] . "?alt=media";
			}
			else{
				$user['gif'] = Constants::$VIDEO_CDN_PATH . $user['gif'];
			}
		}
		if($user['webp']!=""){
			if($user['iscdn']==0){
				$user['webp'] = Constants::$VIDEO_NON_CDN_PATH . $user['webp'] . "?alt=media";
			}
			else{
				$user['webp'] = Constants::$VIDEO_CDN_PATH . $user['webp'];
			}	
		}
		$user['vid'].="";
		$user['uid'].="";			
		//unset($user['email']);
		//unset($user['iscdn']);
		return $user;
	}

}