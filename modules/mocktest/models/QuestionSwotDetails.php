<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';
include_once dirname(__FILE__) . '/../data/SwotMessages.php';

class QuestionSwotDetails extends DataModel{
	private $speeds = array('EASY'=>60,'MEDIUM'=>100,'HARD'=>140);
	private $speeds_name = array('EASY'=>'Easy','MEDIUM'=>'Moderate','HARD'=>'Tough','none'=>'',''=>'');
	private $swot_name = array('T'=>'Threat','W'=>'Weakness','O'=>'Opportunity','S'=>'Strength',''=>'');


	public function __construct(){
		$this->datamodel['no'] = "0";
		$this->datamodel['qid'] = "0";
		$this->datamodel['answer'] = "";
		$this->datamodel['answer_option'] = "";
		$this->datamodel['marks'] = "0";
		$this->datamodel['difficulty'] = "";
		$this->datamodel['speed'] = "none";
		$this->datamodel['speed_msg'] = "";
		$this->datamodel['time_taken'] = "";
		$this->datamodel['is_correct'] = "0";
		$this->datamodel['status'] = "";

		$this->datamodel['question_text'] = "";
		$this->datamodel['question_type'] = "MCQ";
		$this->datamodel['correct_answer'] = "";
		$this->datamodel['group_id'] = "";
		$this->datamodel['group_type'] = "MCQ";
		$this->datamodel['paragraph'] = "";
		$this->datamodel['correct_answer_option'] = "0";
		$this->datamodel['pic'] = "";
		$this->datamodel['solution'] = "";
		$this->datamodel['solution_pic'] = "";
		$this->datamodel['options'] = array();

		$this->datamodel['topic'] = "";
		$this->datamodel['subject'] = "";

		$this->datamodel['swot'] = "";
		$this->datamodel['swot_name'] = "";
		$this->datamodel['swot_msg'] = "";

	}

	public function getInfo(){
		if($this->datamodel['speed']=="slow"){
			$this->datamodel['speed_msg'] = "You took " . $this->datamodel['time_taken'] . " seconds — this question should’ve been done in " . $this->speeds[$this->datamodel['difficulty']] . " seconds.";
		}
		else if($this->datamodel['speed']=="fast"){
			$this->datamodel['speed_msg'] = "You took " . $this->datamodel['time_taken'] . " seconds--this usually takes " . $this->speeds[$this->datamodel['difficulty']] . " seconds.";
		}
		$this->datamodel['difficulty'] = $this->speeds_name[$this->datamodel['difficulty']];
		$this->datamodel['swot_name'] = $this->swot_name[$this->datamodel['swot']];

		if($this->datamodel['speed']=='none' || $this->datamodel['speed']==''){
			$this->datamodel['speed'] = "";
			$this->datamodel['marks'] = "0";
		}

		$this->datamodel['speed'] = ucfirst($this->datamodel['speed']);
		if($this->datamodel['marks']>0){
			$this->datamodel['marks'] = "+" . $this->datamodel['marks'];
		}

		$this->datamodel['swot_msg'] = $this->getSWOTMessage();
		$this->datamodel['paragraph'] = nl2br($this->datamodel['paragraph']);
		$this->datamodel['question_text'] = nl2br($this->datamodel['question_text']);
		$this->datamodel['solution'] = nl2br($this->datamodel['solution']);

		if($this->datamodel['pic']!=""){
			$this->datamodel['pic'] = "https://static.thingsapp.co/catmocktest/" . $this->datamodel['pic'];
		}

		if($this->datamodel['solution_pic']!=""){
			$this->datamodel['solution_pic'] = "https://static.thingsapp.co/catmocktest/" . $this->datamodel['solution_pic'];
		}
		return $this->getObject();
	}

	private function getSWOTMessage(){
		$message = "";
		if($this->datamodel['is_correct']=="1"){
			if($this->datamodel['speed']=='fast' || $this->datamodel['speed']=='Fast'){
				$message = SwotMessages::$RIGHT_FAST;
			}
			else {
				$message = SwotMessages::$RIGHT_SLOW;
			}
		}
		else{
			if($this->datamodel['status']=="answered" || $this->datamodel['status']=="review_answer"){
				if($this->datamodel['speed']=='fast' || $this->datamodel['speed']=='Fast'){
					$message = SwotMessages::$WRONG_FAST;
				}
				else {
					$message = SwotMessages::$WRONG_SLOW;
				}
			}
			else if($this->datamodel['status']=="skipped" || $this->datamodel['status']=="review"){
				if($this->datamodel['speed']=='fast' || $this->datamodel['speed']=='Fast'){
					$message = SwotMessages::$SKIPPED_FAST;
				}
				else {
					$message = SwotMessages::$SKIPPED_SLOW;
				}
			}
			else{
				$message = SwotMessages::$NOT_VISITED;
			}
		}
		return $message;
	}
}
