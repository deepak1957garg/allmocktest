<?php 
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class Category extends DataModel{

	public function __construct(){
		$this->datamodel['cid']=0;
		$this->datamodel['cname']='';
		$this->datamodel['curl']='';
		$this->datamodel['cdesc']='';
		$this->datamodel['is_active']=0;
		$this->datamodel['corder']='';
		$this->datamodel['created_on']='';
		$this->datamodel['updated_on']='';
	}

}
?>