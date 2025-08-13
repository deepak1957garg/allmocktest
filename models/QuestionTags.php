<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class QuestionTags extends DataModel{

	public function __construct(){
		$this->table_name = 'question_tags';
		$this->primary_key = 'tag_id';

		$this->datamodel['tag_id'] = "0";
		$this->datamodel['tag_name'] = "";
	}
}
