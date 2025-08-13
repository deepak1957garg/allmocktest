<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class QuestionTopics extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'question_topics';
		$this->primary_key = 'topic_id';

		$this->datamodel['topic_id'] = "0";
		$this->datamodel['topic_name'] = "";
		$this->datamodel['subject'] = "";
		$this->datamodel['parent_topic_id'] = "0";
		$this->datamodel['description'] = "";
	}
}
