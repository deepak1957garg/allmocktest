<?php 
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class ProviderUnavailability extends DataModel{

	public function __construct(){
		// $this->table_name = 'programs';
		// $this->primary_key = 'pid';

		$this->datamodel['id']=0;   //provider id   
		$this->datamodel['pr_id']=0;   //provider id   
		$this->datamodel['calender_date']='0000-00-00';
		$this->datamodel['slots_unavailable']={"2100","2200"};
		$this->datamodel['isfullDay_unavailable']=1;
		$this->datamodel['unavailable_reason']="";
		$this->datamodel['isactive']=1;
		$this->datamodel['created_on']='0000-00-00';
		$this->datamodel['updated_on']='0000-00-00';
	}
}