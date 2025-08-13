<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class TestSummary extends DataModel{

	public function __construct(){
		$this->datamodel['test_id'] = "0";
		$this->datamodel['test_name'] = "";
		$this->datamodel['template_name'] = "";
		$this->datamodel['total_questions'] = "0";
		$this->datamodel['total_time'] = "0";
		$this->datamodel['time_taken'] = "0";
		$this->datamodel['exam_on'] = "";
		$this->datamodel['marks'] = "0";
		$this->datamodel['max_marks'] = "0";
		$this->datamodel['sections'] = array();
		$this->datamodel['questions'] = array();
	}

	public function getInfo(){
		$arr = array();
		$arr = $this->getObject();

		if($arr['exam_on']!=""){
			$arr['exam_on'] = date("d M Y, H:i A",(strtotime($arr['exam_on'])+19800));	
		}
		return $arr;
	}

}