<?php 
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class Tip extends DataModel{

	public function __construct(){
		$this->datamodel['id']=0;
		$this->datamodel['vid']=0;
		$this->datamodel['uid']=0;
		$this->datamodel['points']=0;
		$this->datamodel['is_paid']=0;
		$this->datamodel['paid_on']='';
		$this->datamodel['pay_id']='';
		$this->datamodel['tip_uid']=0;
		$this->datamodel['is_active']=1;
		$this->datamodel['created_on']='';
		$this->datamodel['updated_on']='';
	}

}
?>