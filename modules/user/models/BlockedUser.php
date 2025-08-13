<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class BlockedUser extends DataModel{

	public function __construct(){
		$this->table_name = 'user_block';
		$this->primary_key = 'id';

		$this->datamodel['id']=0;	
		$this->datamodel['uid']=0;
		$this->datamodel['blocked_uid']=0;
	}

}