<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class MockTest extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'test';
		$this->primary_key = 'test_id';

		$this->datamodel['test_id'] = "0";
		$this->datamodel['test_name'] = "0";
		$this->datamodel['questions'] = "0";
		$this->datamodel['total_ques'] = "0";
		$this->datamodel['total_time'] = "0";
	}
}
