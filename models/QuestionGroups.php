<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class QuestionGroups extends DataModel{

	public function __construct(){
		$this->table_name = 'question_groups';
		$this->primary_key = 'group_id';

		$this->datamodel['group_id'] = "0";
		$this->datamodel['group_type'] = "para";
		$this->datamodel['paragraph'] = "";
		$this->datamodel['pic'] = "";
		$this->datamodel['is_cdn'] = "0";
	}
}
