<?php 
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class VideoStats extends DataModel{

	public function __construct(){
		$this->datamodel['vid']=0;
		$this->datamodel['points']=0;
		$this->datamodel['num_tips']=0;
		$this->datamodel['points_paid']=0;
		$this->datamodel['num_tips_paid']=0;
		$this->datamodel['num_user_tips']=0;
		$this->datamodel['first_tip']=0;
		$this->datamodel['last_tip']=0;
		$this->datamodel['top_tip']=0;
		$this->datamodel['first_tip_amount']=0;
		$this->datamodel['last_tip_amount']=0;
		$this->datamodel['top_tip_amount']=0;
		$this->datamodel['created_on']='';
		$this->datamodel['updated_on']='';
	}
}
?>