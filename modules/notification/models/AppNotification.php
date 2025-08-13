<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class AppNotification extends DataModel{

	public function __construct(){
		$this->datamodel['id']="0";
		$this->datamodel['nid']="0";
		$this->datamodel['template']="";
		$this->datamodel['page']="";
		$this->datamodel['iid']="";
		$this->datamodel['text']="";

		$this->datamodel['uid']="0";
		$this->datamodel['pic']="";
		//$this->datamodel['vpic']="";
		$this->datamodel['webp']="";

		$this->datamodel['name']="";
		//$this->datamodel['vid']="0";
		//$this->datamodel['vname']="";
		//$this->datamodel['amount']="0";
		//$this->datamodel['curr']="";
		$this->datamodel['title']='';

		$this->datamodel['is_seen']="0";
		$this->datamodel['isseen']="0";
		$this->datamodel['time']=0;
	}

}