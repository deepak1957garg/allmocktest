<?php 
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class UserVideoStats extends DataModel{

	public function __construct(){
		$this->datamodel['id']=0;
		$this->datamodel['uid']=0;
		$this->datamodel['vid']=0;
		$this->datamodel['points_spent']=0;
		$this->datamodel['num_tips_spent']=0;
		$this->datamodel['points_earned']=0;
		$this->datamodel['num_tips_earned']=0;
		$this->datamodel['created_on']='';
		$this->datamodel['updated_on']='';
	}

}
?>