<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class ClusterStats extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'cluster_stats';
		$this->primary_key = 'id';

		$this->datamodel['id'] = "0";
		$this->datamodel['topic_id'] = "0";
		$this->datamodel['difficulty'] = 'NONE';
		$this->datamodel['cluster_id'] = "0";
		$this->datamodel['num_ques'] = "0";
	}
}