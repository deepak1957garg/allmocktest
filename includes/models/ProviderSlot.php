<?php 
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class ProviderSlot extends DataModel{

	public function __construct(){
		$this->table_name = 'provider_slots';
		$this->primary_key = 'id';

		$this->datamodel['id']=0;
		$this->datamodel['puid']=0;         //provider id   
		$this->datamodel['day']=0;          //1 - mon,2 - Tues,3 - wed
		$this->datamodel['slot']="";        //9:30 pm equals = 21:30, in min
		$this->datamodel['module']=1;
		$this->datamodel['num_allowed']=1; 
	}
}