<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class ApiResponse extends DataModel{

	public function __construct(){
		$this->datamodel['success'] = false;
		$this->datamodel['error'] = "";
		$this->datamodel['data'] = new stdClass();
	}

	public function addData($key,$values){
		if(!is_array($this->datamodel['data']))	$this->datamodel['data'] = array();
		$this->datamodel['data'][$key] = $values;
	}

}