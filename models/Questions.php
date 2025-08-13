<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class Questions extends DataModel{

	public function __construct(){
		$this->table_name = 'questions';
		$this->primary_key = 'question_id';

		$this->datamodel['question_id'] = "0";
		$this->datamodel['question_text'] = "";
		$this->datamodel['question_type'] = "MCQ";
		$this->datamodel['correct_answer'] = "";
		$this->datamodel['group_id'] = "0";
	}
}
