<?php 
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class BookedSlot extends DataModel{

	public function __construct(){
		$this->db_name = 'sgf';
		$this->table_name = 'sgf_provider_booked_slot';
		$this->primary_key = 'id';

		$this->datamodel['id']=0;
		$this->datamodel['aid']=0;
		$this->datamodel['puid']=0;
		$this->datamodel['slot']='';
		$this->datamodel['booking_date']='0000-00-00';
	}

}