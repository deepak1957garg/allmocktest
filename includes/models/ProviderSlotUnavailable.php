<?php 
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class ProviderSlotUnavailable extends DataModel{

	public function __construct(){
		$this->table_name = 'slot_unavailable';
		$this->primary_key = 'id';

		$this->datamodel['id']=0;
		$this->datamodel['puid']=0;
		$this->datamodel['date']=""; 
		$this->datamodel['slot']="";        //9:30 pm equals = 21:30, in min
	}
}