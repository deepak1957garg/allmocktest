<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class User extends DataModel{

	public function __construct(){
		$this->table_name = 'users';
		$this->primary_key = 'uid';

		$this->datamodel['uid']=0;
		$this->datamodel['name']='';
		$this->datamodel['uname']='';
		$this->datamodel['fbid']='';
		$this->datamodel['email']='';
		$this->datamodel['pic']='';
		$this->datamodel['upic']='';
		$this->datamodel['thumb']='';
		$this->datamodel['isCdn']=0;
		$this->datamodel['vid']=0;
		$this->datamodel['cafes']='';
		$this->datamodel['availability']='';
		$this->datamodel['num_meetings']=0;
	}

	public function getInfo(){
		$user = array();
		$user = $this->getObject();

		if($user['upic']!=""){
			if($user['isCdn']==0){
				$user['upic'] = Constants::$VIDEO_NON_CDN_PATH . $user['upic'] . "?alt=media";
			}
			else if($user['thumb']!=''){
				$user['upic'] = Constants::$VIDEO_CDN_PATH . $user['thumb'];
			}
			else{ 	
				$user['upic'] = Constants::$VIDEO_CDN_PATH . $user['upic'];
			}
			$user['pic'] = $user['upic'];	
		}
		else if(!strpos($user['pic'],"googleusercontent.com")){
			$user['pic'] = Constants::$VIDEO_NON_CDN_PATH . $user['pic']."?alt=media";
		}
		unset($user['email']);
		unset($user['isCdn']);
		unset($user['thumb']);
		return $user;
	}

}