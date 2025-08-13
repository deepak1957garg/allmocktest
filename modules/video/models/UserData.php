<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class UserData extends DataModel{

	public function __construct(){
		$this->table_name = 'users_data';
		$this->primary_key = 'tuid';

		$this->datamodel['tuid']=0;
		$this->datamodel['info']='';
	}

}