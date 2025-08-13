<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class TestData extends DataModel{

	public function __construct(){
		$this->datamodel['test_id'] = "0";
		$this->datamodel['test_name'] = "";
		$this->datamodel['template_name'] = "";
		$this->datamodel['total_questions'] = "0";
		$this->datamodel['total_time'] = "0";
		$this->datamodel['exam_on'] = "";
		$this->datamodel['marks'] = "0";
		$this->datamodel['sections'] = array();
		$this->datamodel['questions'] = array();


		$this->datamodel['total_section'] = "0";
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
		$this->datamodel['swot_type'] = "";
	}
}


Your score:

98/ 220 - VARC: 20/ 60, DILR: 32/ 78, QA: 46/ 46

Time taken:

109 Minutes

Speed:

Correct Answers: Fast

Wrong Answers: Slow