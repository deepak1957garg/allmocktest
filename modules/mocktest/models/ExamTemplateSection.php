<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class ExamTemplateSection extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'exam_template_section';
		$this->primary_key = 'section_id';

		$this->datamodel['section_id'] = "";
		$this->datamodel['template_id'] = "1";
		$this->datamodel['section_name'] = "";
		$this->datamodel['num_questions'] = "20";
		$this->datamodel['num_time'] = "3600";
		$this->datamodel['section_order'] = "1";
	}
}
