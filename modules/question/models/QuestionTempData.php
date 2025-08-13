<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class QuestionTempData extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'temptable';
		$this->primary_key = 'id';

		$this->datamodel['id'] = "0";
		$this->datamodel['year'] = "";
		$this->datamodel['slot'] = "";
		$this->datamodel['question'] = "";
		$this->datamodel['options'] = "";
		$this->datamodel['algorithm'] = "";
		$this->datamodel['topic'] = "";
		$this->datamodel['sub_topic'] = "";
		$this->datamodel['difficulty'] = "";
		$this->datamodel['generated_solution'] = "";
		$this->datamodel['predicted_answer'] = "";
		$this->datamodel['cluster'] = "";
		$this->datamodel['uuid'] = "";
		$this->datamodel['passage'] = "";
		$this->datamodel['predicted_answer'] = "";
		$this->datamodel['image_url'] = "";
	}
}
