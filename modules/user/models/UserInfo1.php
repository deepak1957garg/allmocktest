<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class UserInfo1 extends DataModel{

	public function __construct(){
		$this->table_name = 'users_info';
		$this->primary_key = 'uid';

		$this->datamodel['uid']=0;
		$this->datamodel['info']='';
	}

}