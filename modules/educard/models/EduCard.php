<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class EduCard extends DataModel{

	public function __construct(){
		$this->db_name = 'cat_cards';
		$this->table_name = 'cards';
		$this->primary_key = 'card_id';

		$this->datamodel['card_id'] = "0";
		$this->datamodel['subject_id'] = "0";
		$this->datamodel['topic_id'] = "0";
		$this->datamodel['type'] = "";
		$this->datamodel['front'] = "";
		$this->datamodel['back'] = "";
	}
}
