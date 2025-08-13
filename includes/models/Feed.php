<?php
include_once dirname(__FILE__) . '/../common/Constants.php';
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class Feed extends DataModel{

	public function __construct(){
		$this->datamodel['uid']=0;
		$this->datamodel['path']="";
		$this->datamodel['pic']="";
		$this->datamodel['first']="";
		$this->datamodel['webp']="";
		$this->datamodel['upic']="";

		$this->datamodel['name']="";
		$this->datamodel['cafes']="";
		$this->datamodel['availability']="";
		$this->datamodel['uname']="";
	}

	public function getInfo(){
		if($this->datamodel['path']!="")	$this->datamodel['path'] = Constants::$VIDEO_CDN_PATH . $this->datamodel['path'];
		if($this->datamodel['pic']!="")		$this->datamodel['pic'] = Constants::$VIDEO_CDN_PATH . $this->datamodel['pic'];
		if($this->datamodel['first']!="")		$this->datamodel['first'] = Constants::$VIDEO_CDN_PATH . $this->datamodel['first'];
		if($this->datamodel['webp']!="")	$this->datamodel['webp'] = Constants::$VIDEO_CDN_PATH . $this->datamodel['webp'];
		if($this->datamodel['upic']!="")	$this->datamodel['upic'] = Constants::$VIDEO_CDN_PATH . $this->datamodel['upic'];
		return $this->getObject();
	}

}
?>