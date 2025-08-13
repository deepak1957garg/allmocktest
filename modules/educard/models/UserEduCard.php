<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class UserEduCard extends DataModel{

	public function __construct(){
		$this->db_name = 'cat_cards';
		$this->table_name = 'user_cards';
		$this->primary_key = 'id';

		$this->datamodel['id'] = "0";
		$this->datamodel['uid'] = "0";
		$this->datamodel['card_id'] = "0";
		$this->datamodel['ease_factor'] = "2.5";
		$this->datamodel['repetition'] = "0";
		$this->datamodel['repetition_date'] = date("Y-m-d 00:00:00",strtotime('now'));
		$this->datamodel['show_interval'] = "0";
		$this->datamodel['is_seen'] = "0";
		$this->datamodel['last_chosen_option'] = "0";
		$this->datamodel['repetition_reason'] = "0";
		$this->datamodel['swot'] = "";
		$this->datamodel['swot_factor'] = "0";
		$this->datamodel['importance_factor'] = "1";
		$this->datamodel['type_factor'] = "1";
	}
}