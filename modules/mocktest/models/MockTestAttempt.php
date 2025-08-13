<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class MockTestAttempt extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'test_attempt';
		$this->primary_key = 'id';

		$this->datamodel['id'] = "0";
		$this->datamodel['test_id'] = "0";
		$this->datamodel['question_id'] = "0";
		$this->datamodel['question_no'] = "0";
		$this->datamodel['user_id'] = "0";
		$this->datamodel['answer'] = "";
		$this->datamodel['answer_option'] = "";
		$this->datamodel['time_taken'] = "0";
		$this->datamodel['is_attemped'] = "0";
		$this->datamodel['is_correct'] = "0";
		$this->datamodel['mark_revisit'] = "0";
		$this->datamodel['status'] = "";
		$this->datamodel['marks'] = "0";
		$this->datamodel['difficulty'] = "";
		$this->datamodel['swot'] = "0";
	}
}
