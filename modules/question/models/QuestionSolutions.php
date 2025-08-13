<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class QuestionSolutions extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'question_solutions';
		$this->primary_key = 'id';

		$this->datamodel['id'] = "0";
		$this->datamodel['question_id'] = "0";
		$this->datamodel['solution'] = "";
		$this->datamodel['answer'] = "";
		$this->datamodel['predicted_answer'] = "";
		$this->datamodel['algo'] = "";
		$this->datamodel['pic'] = "";
		$this->datamodel['solution_source'] = "";
		$this->datamodel['algo_source'] = "";
	}
}
