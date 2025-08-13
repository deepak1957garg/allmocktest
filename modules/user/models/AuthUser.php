<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class AuthUser extends DataModel{

	public function __construct(){
		$this->table_name = 'user_auth';
		$this->primary_key = 'id';

		$this->datamodel['id']=0;
		$this->datamodel['email']="";
		$this->datamodel['uid']="";
	}

}