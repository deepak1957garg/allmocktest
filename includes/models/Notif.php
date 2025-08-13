<?php 
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class Notif extends DataModel{

	public function __construct(){
		$this->datamodel['id']=0;
		$this->datamodel['vid']=0;
		$this->datamodel['uid']=0;
		$this->datamodel['ntype']="";
		$this->datamodel['is_sent']=0;
		$this->datamodel['req']='';
		$this->datamodel['res']='';
		$this->datamodel['tip_uid']=0;
		$this->datamodel['created_on']='';
		$this->datamodel['updated_on']='';
	}

}
?>