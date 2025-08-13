<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class ExamTemplate extends DataModel{

	public function __construct(){
		$this->db_name = 'question_bank';
		$this->table_name = 'exam_template';
		$this->primary_key = 'template_id';

		$this->datamodel['template_id'] = "";
		$this->datamodel['template_name'] = "";
		$this->datamodel['num_questions'] = "20";
		$this->datamodel['num_time'] = "3600";
		$this->datamodel['num_section'] = "1";
		$this->datamodel['naming_convention'] = "";
		$this->datamodel['shopify_product_id'] = "0";
		$this->datamodel['shopify_variant_id'] = "0";
	}
}
