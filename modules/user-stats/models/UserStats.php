<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class UserStats extends DataModel{

	public function __construct(){
		$this->table_name = 'user_stats1';
		$this->primary_key = 'id';

		$this->datamodel['id']=0;
		$this->datamodel['uid']=0;
		$this->datamodel['event']="";
		$this->datamodel['num_count']=0;
	}

}