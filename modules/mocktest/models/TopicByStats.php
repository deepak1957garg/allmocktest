<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class TopicByStats extends DataModel{

	public function __construct(){
		$this->datamodel['id'] = "0";
		$this->datamodel['uid'] = "0";
		$this->datamodel['topic_id'] = "0";
		$this->datamodel['topic'] = "";
		$this->datamodel['subject_id'] = "0";
		$this->datamodel['subject'] = "";
		$this->datamodel['difficulty'] = "";
		$this->datamodel['num_test'] = "0";
		$this->datamodel['total_questions'] = "0";
		$this->datamodel['total_time'] = "0";
		$this->datamodel['max_marks'] = "0";
		$this->datamodel['marks'] = "0";
		$this->datamodel['question_attempted'] = "0";
		$this->datamodel['time_taken'] = "0";
		$this->datamodel['avg_time'] = "0";
		$this->datamodel['correct_marks'] = "0";
		$this->datamodel['correct_question_attempted'] = "0";
		$this->datamodel['correct_time_taken'] = "0";
		$this->datamodel['correct_avg_time'] = "0";
		$this->datamodel['wrong_marks'] = "0";
		$this->datamodel['wrong_question_attempted'] = "0";
		$this->datamodel['wrong_time_taken'] = "0";
		$this->datamodel['wrong_avg_time'] = "0";
		$this->datamodel['swot_marks'] = "0";
		$this->datamodel['swot_avg'] = "0";
		$this->datamodel['swot_type'] = "";
	}
}
