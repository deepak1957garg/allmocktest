<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class TestStats extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'test_stats';
		$this->primary_key = 'test_id';

		$this->datamodel['test_id'] = "0";
		$this->datamodel['order_id'] = "0";
		$this->datamodel['exam_id'] = "0";
		$this->datamodel['uid'] = "0";
		$this->datamodel['template_id'] = "0";
		$this->datamodel['template_name'] = "0";
		$this->datamodel['test_name'] = "";
		$this->datamodel['total_questions'] = "0";
		$this->datamodel['total_time'] = "0";
		$this->datamodel['total_section'] = "0";
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
		$this->datamodel['swot_type'] = "";
		$this->datamodel['exam_on'] = "";
	}
}
