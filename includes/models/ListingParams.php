<?php
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class ListingParams extends DataModel{

	public function __construct(){
		$this->datamodel['type']="";
		$this->datamodel['val']="";
		$this->datamodel['vid']=0;
		$this->datamodel['uid']='';
	}

}
?>