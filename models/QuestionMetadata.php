<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class QuestionMetadata extends DataModel{

	public function __construct(){
		$this->table_name = 'question_metadata';
		$this->primary_key = 'question_id';

		$this->datamodel['question_id'] = "";
		$this->datamodel['course_id'] = "0";
		$this->datamodel['exam_id'] = "";
		$this->datamodel['subject_id'] = "";
		$this->datamodel['topic_id'] = "";
		$this->datamodel['difficulty'] = "NONE";
		$this->datamodel['difficulty_algo'] = "NONE";
		$this->datamodel['difficulty_by_stats'] = "NONE";
		$this->datamodel['difficulty_manual'] = "NONE";
		$this->datamodel['pattern'] = "0";
		$this->datamodel['source'] = "";
		$this->datamodel['year'] = "";
		$this->datamodel['slot'] = "0";
		$this->datamodel['weightage'] = "0";
	}
}
