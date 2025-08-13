<?php
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class HVideoInfo extends DataModel{

	public function __construct(){
		$this->datamodel['vid']=0;
		$this->datamodel['vname']='';
		// $this->datamodel['vactors']='';
		// $this->datamodel['vmessage']='';    // owner message
		$this->datamodel['vurl']='';
		$this->datamodel['vdesc']='';
		// $this->datamodel['vdesc']='';
		// $this->datamodel['vgenre']='';
		$this->datamodel['isold']=0;
		$this->datamodel['issold']=0;
		$this->datamodel['itype']=1;
		$this->datamodel['sale_amount']=0;
		$this->datamodel['created_on']='';
		$this->datamodel['updated_on']='';
	}

	public function getObjectPartially(){
		$obj = array();
		$obj['vname'] = $this->datamodel['vname'];
		//$obj['vactors'] = $this->datamodel['vactors'];
		//$obj['vmessage'] = $this->datamodel['vmessage'];
		$obj['vurl'] = $this->datamodel['vurl'];
		$obj['vdesc'] = $this->datamodel['vdesc'];
		$obj['sale_amount'] = $this->datamodel['sale_amount'];
		$obj['isold'] = $this->datamodel['isold'];
		$obj['issold'] = $this->datamodel['issold'];
		return $obj;
	}

}
?>