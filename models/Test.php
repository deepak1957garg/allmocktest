<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class Test extends DataModel{

	public function __construct(){
		$this->table_name = 'test';
		$this->primary_key = 'test_id';

		$this->datamodel['test_id'] = "0";
		$this->datamodel['test_name'] = "";
		$this->datamodel['questions'] = "";
		$this->datamodel['total_ques'] = "20";
		$this->datamodel['total_time'] = "3600";
	}
}
