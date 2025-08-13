<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class QuestionFetchedSolutions extends DataModel{

	public function __construct(){
		$this->table_name = 'question_fetched_solutions';
		$this->primary_key = 'id';

		$this->datamodel['id'] = "0";
		$this->datamodel['question_id'] = "0";
		$this->datamodel['solution'] = "";
		$this->datamodel['answer'] = "";
		$this->datamodel['algo'] = "";
		$this->datamodel['solution_source'] = "";
		$this->datamodel['algo_source'] = "";
		$this->datamodel['is_correct'] = "0";
	}
}
