<?php 
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class ProviderSlotFees extends DataModel{

	public function __construct(){
		$this->table_name = 'provider_slot_fees';
		$this->primary_key = 'id';

		$this->datamodel['id']=0;
		$this->datamodel['puid']=0;
		$this->datamodel['slot_duration']=0;
		$this->datamodel['fees']=0;
		$this->datamodel['module']=2;
	}

}