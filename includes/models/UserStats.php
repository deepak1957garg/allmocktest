<?php 
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class UserStats extends DataModel{

	public function __construct(){
		$this->datamodel['uid']=0;
		$this->datamodel['points']=0;
		$this->datamodel['num_tips']=0;
		$this->datamodel['points_paid']=0;
		$this->datamodel['num_tips_paid']=0;
		$this->datamodel['free_tips']=0;
		$this->datamodel['points_earned']=0;
		$this->datamodel['num_tips_earned']=0;
		$this->datamodel['created_on']='';
		$this->datamodel['updated_on']='';
	}

}
?>