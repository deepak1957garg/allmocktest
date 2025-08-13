<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class Constant extends DataModel{

	public function __construct(){
		$this->table_name = 'constants';
		$this->primary_key = 'id';

		$this->datamodel['id']=0;
		$this->datamodel['key']='';
		$this->datamodel['value']='';
	}

}