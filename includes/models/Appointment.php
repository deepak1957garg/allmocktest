<?php 
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class Appointment extends DataModel{

	public function __construct(){
		$this->db_name = 'sgf';
		$this->table_name = 'sgf_appointments';
		$this->primary_key = 'aid';

		$this->datamodel['booking_date']='0000-00-00';
		$this->datamodel['aid']=0;
		$this->datamodel['puid']=0;
		$this->datamodel['uid']=0;
		$this->datamodel['slot']='';
		$this->datamodel['booking_status']=1;  //1 - confirmed,2 - cancelled, 0 - unbooked/default,3 - resheduled, 4 - booked, 5 - payment failed
		$this->datamodel['module']=1;
	}

	public function addNewAidField(){
		$this->datamodel['new_aid']=0;
	}

}