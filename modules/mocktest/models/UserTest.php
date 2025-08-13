<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class UserTest extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'user_test';
		$this->primary_key = 'id';

		$this->datamodel['id'] = "0";
		$this->datamodel['order_id'] = "0";
		$this->datamodel['exam_id'] = "0";
		$this->datamodel['uid'] = "0";
		$this->datamodel['section1_id'] = "0";
		$this->datamodel['section1_time'] = "";
		$this->datamodel['section1_marks'] = "";
		$this->datamodel['is_section1_completed'] = "0";
		$this->datamodel['section2_id'] = "0";
		$this->datamodel['section2_time'] = "";
		$this->datamodel['section2_marks'] = "";
		$this->datamodel['is_section2_completed'] = "0";
		$this->datamodel['section3_id'] = "0";
		$this->datamodel['section3_time'] = "";
		$this->datamodel['section3_marks'] = "";
		$this->datamodel['is_section3_completed'] = "0";
		$this->datamodel['total_marks'] = "0";
		$this->datamodel['total_time'] = "0";
		$this->datamodel['is_started'] = "0";
		$this->datamodel['is_completed'] = "0";
		$this->datamodel['started_on'] = "0000-00-00";
		$this->datamodel['completed_on'] = "0000-00-00";
	}
}
