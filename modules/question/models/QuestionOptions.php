<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class QuestionOptions extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'question_options';
		$this->primary_key = 'option_id';

		$this->datamodel['option_id'] = "0";
		$this->datamodel['question_id'] = "0";
		$this->datamodel['option_text'] = "";
		$this->datamodel['option_number'] = "0";
		$this->datamodel['is_correct'] = "0";
	}
}
