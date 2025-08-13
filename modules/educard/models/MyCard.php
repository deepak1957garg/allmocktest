<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class MyCard extends DataModel{
	private $subject_name = array(1=>'QA',2=>'VARC',3=>'DILR',0=>'NONE');

	public function __construct(){
		$this->datamodel['id'] = "0";
		$this->datamodel['card_id'] = "0";
		$this->datamodel['repetition_date'] = date("Y-m-d 00:00:00",strtotime('now'));
		$this->datamodel['swot_factor'] = "0";
		$this->datamodel['importance_factor'] = "1";
		$this->datamodel['type_factor'] = "1";
		$this->datamodel['priority_factor'] = "1";
		$this->datamodel['subject_id'] = "0";
		$this->datamodel['subject'] = "";
		$this->datamodel['topic_id'] = "0";
		$this->datamodel['topic'] = "";
		$this->datamodel['type'] = "";
		$this->datamodel['front'] = "";
		$this->datamodel['back'] = "";
	}

	public function getInfo(){
		unset($this->datamodel['repetition_date']);
		unset($this->datamodel['swot_factor']);
		unset($this->datamodel['importance_factor']);
		unset($this->datamodel['type_factor']);

		$this->datamodel['subject'] = $this->subject_name[$this->datamodel['subject_id']];
		$this->datamodel['front'] = nl2br($this->datamodel['front']);
		$this->datamodel['back'] = nl2br($this->datamodel['back']);
		return $this->getObject();
	}
}