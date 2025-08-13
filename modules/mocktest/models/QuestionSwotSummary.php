<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class QuestionSwotSummary extends DataModel{

	public function __construct(){
		$this->datamodel['no'] = "0";
		$this->datamodel['qid'] = "0";
		$this->datamodel['type'] = "MCQ";
		$this->datamodel['time_taken'] = "0";
		$this->datamodel['status'] = "";
		$this->datamodel['marks'] = "0";
		$this->datamodel['difficulty'] = "";
		$this->datamodel['speed'] = "";
		$this->datamodel['swot'] = "";
		$this->datamodel['color'] = "";
	}

	//assumptions that question distribution : 60% medium, 30% hard, 10% easy(40,20,6 questions out of 66)
	//total time = 120min = 120*60 secs
	//lets medium difficult question time = x
	//  40x+20(1.4x)+6(0.6x) = 120*60
	// medium - 100sec, hard - 140 sec, easy - 60 secs
	public function getInfo(){
		if($this->datamodel['difficulty']=="EASY"){
			$this->datamodel['difficulty'] = "Easy";
		}
		else if($this->datamodel['difficulty']=="MEDIUM"){
			$this->datamodel['difficulty'] = "Moderate";
		}
		else if($this->datamodel['difficulty']=="HARD"){
			$this->datamodel['difficulty'] = "Tough";
		}
		if($this->datamodel['speed']=='none'){
			$this->datamodel['speed'] = "N.A.";
			$this->datamodel['marks'] = "0";
		}
		$this->datamodel['speed'] = ucfirst($this->datamodel['speed']);
		if($this->datamodel['marks']>0){
			$this->datamodel['marks'] = "+" . $this->datamodel['marks'];
		}
		return $this->getObject();
	}
}
