<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class QuestionClusterMapping extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'question_cluster_mapping';
		$this->primary_key = 'mapping_id';

		$this->datamodel['mapping_id'] = "0";
		$this->datamodel['question_id'] = "0";
		$this->datamodel['topic_id'] = "0";
		$this->datamodel['cluster_id'] = "0";
	}
}
