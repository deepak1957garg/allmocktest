<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';
include_once dirname(__FILE__) . '/../../../includes/common/Constants.php';

class BasicUserInfo extends DataModel{

	public function __construct(){
		$this->table_name = 'users';
		$this->primary_key = 'uid';

		$this->datamodel['uid']=0;
		$this->datamodel['name']='';
		$this->datamodel['uname']='';
		$this->datamodel['pic']='';
		$this->datamodel['upic']='';
		$this->datamodel['thumb']='';
		$this->datamodel['isCdn']='';
	}

	public function getInfo(){
		if($this->datamodel['isCdn']==0){
			$this->datamodel['upic'] = Constants::$VIDEO_NON_CDN_PATH . $this->datamodel['upic'] . "?alt=media";
		}
		else if($this->datamodel['thumb']!=''){
			$this->datamodel['upic'] = Constants::$VIDEO_CDN_PATH . $this->datamodel['thumb'];
		}
		else if($this->datamodel['upic']!=''){ 	
			$this->datamodel['upic'] = Constants::$VIDEO_CDN_PATH . $this->datamodel['upic'];
		}
		if($this->datamodel['upic']!=""){
			$this->datamodel['pic'] = $this->datamodel['upic'];
		}
		unset($this->datamodel['upic']);
		unset($this->datamodel['thumb']);
		unset($this->datamodel['isCdn']);
		return $this->getObject();
	}

}