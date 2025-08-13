<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class AttributionParams extends DataModel{

	public function __construct(){
		$this->datamodel['host']='';
		$this->datamodel['iid']='';
		$this->datamodel['utm_source']='';
		$this->datamodel['utm_campaign']='';
		$this->datamodel['utm_medium']='';
		$this->datamodel['ref']='';
		$this->datamodel['refm']='';
		$this->datamodel['uname']='';
		$this->datamodel['refname']='';
	}

}