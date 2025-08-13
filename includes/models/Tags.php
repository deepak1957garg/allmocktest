<?php 
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class Tag extends DataModel{

	public function __construct(){
		$this->datamodel['tid']=0;
		$this->datamodel['uid']=0;
		$this->datamodel['tname']='';
		$this->datamodel['turl']='';
		$this->datamodel['is_active']=0;
		$this->datamodel['created_on']='';
		$this->datamodel['updated_on']='';
	}

}
?>