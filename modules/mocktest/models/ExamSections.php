<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class ExamSections extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'exam_sections';
		$this->primary_key = 'sec_id';

		$this->datamodel['sec_id'] = "0";
		$this->datamodel['exam_id'] = "0";
		$this->datamodel['section_id'] = "0";
		$this->datamodel['questions'] = "";
	}
}
