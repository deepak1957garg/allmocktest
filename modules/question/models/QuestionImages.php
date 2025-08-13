<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class QuestionImages extends DataModel{

	public function __construct(){
		$this->table_name = 'question_images';
		$this->primary_key = 'pic_id';

		$this->datamodel['pic_id'] = "0";
		$this->datamodel['question_id'] = "0";
		$this->datamodel['pic_url'] = "";
	}
}
