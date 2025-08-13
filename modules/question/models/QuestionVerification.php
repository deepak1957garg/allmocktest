<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class QuestionVerification extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'question_verification';
		$this->primary_key = 'question_id';

		$this->datamodel['question_id'] = "0";
		$this->datamodel['is_topic_verified'] = "0";
		$this->datamodel['is_answer_verified'] = "0";
		$this->datamodel['is_solution_verified'] = "0";
	}
}
