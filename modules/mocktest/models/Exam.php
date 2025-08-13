<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class Exam extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'exam';
		$this->primary_key = 'exam_id';

		$this->datamodel['exam_id'] = "0";
		$this->datamodel['test_name'] = "";
		$this->datamodel['template_id'] = "0";
		$this->datamodel['uid'] = "0";
	}
}
