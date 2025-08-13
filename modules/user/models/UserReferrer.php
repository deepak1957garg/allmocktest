<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class UserReferrer extends DataModel{

	public function __construct(){
		$this->table_name = 'user_referrer';
		$this->primary_key = 'id';

		$this->datamodel['id']=0;
		$this->datamodel['uid']=0;
		$this->datamodel['source']='';
		$this->datamodel['campaign']='';
		$this->datamodel['medium']='';
		$this->datamodel['ref']='';
		$this->datamodel['iid']='';
		$this->datamodel['host']='';
	}

}