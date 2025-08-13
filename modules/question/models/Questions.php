<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class Questions extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'questions';
		$this->primary_key = 'question_id';

		$this->datamodel['question_id'] = "0";
		$this->datamodel['question_text'] = "";
		$this->datamodel['question_type'] = "MCQ";
		$this->datamodel['correct_answer'] = "";
		$this->datamodel['correct_answer_option'] = "0";
		$this->datamodel['group_id'] = "0";
	}
}
