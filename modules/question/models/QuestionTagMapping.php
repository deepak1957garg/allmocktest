<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class QuestionTagMapping extends DataModel{

	public function __construct(){
		$this->table_name = 'question_tag_mapping';
		$this->primary_key = 'mapping_id';

		$this->datamodel['mapping_id'] = "0";
		$this->datamodel['question_id'] = "0";
		$this->datamodel['tag_id'] = "0";
	}
}
