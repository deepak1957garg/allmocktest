<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class Otp extends DataModel{

	public function __construct(){
		$this->table_name = 'user_otps';
		$this->primary_key = 'id';

		$this->datamodel['id']=0;
		$this->datamodel['otp']="";
		$this->datamodel['mobile']="";
		$this->datamodel['cc']="";
	}

}