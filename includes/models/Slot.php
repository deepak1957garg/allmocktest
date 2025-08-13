<?php 
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class Slot extends DataModel{

	public function __construct(){
		$this->datamodel['date']="";
		$this->datamodel['timing']="";
		$this->datamodel['dayname']="";
		$this->datamodel['month']="";
		$this->datamodel['year']="";
		$this->datamodel['day']="";
	}
}
?>