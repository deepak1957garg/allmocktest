<?php
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class Orders extends DataModel{

	public function __construct(){
		$this->table_name = 'orders';
		$this->primary_key = 'id';

		$this->datamodel['id'] = "0";
		$this->datamodel['app_id'] = "0";
		$this->datamodel['admin_graphql_api_id'] = "";
		$this->datamodel['contact_email'] = "";
		$this->datamodel['currency'] = "";
		$this->datamodel['created_at'] = "";
		$this->datamodel['subtotal_price'] = "";
		$this->datamodel['email'] = "";
		$this->datamodel['order_status_url'] = "";
		$this->datamodel['token'] = "";
		$this->datamodel['customer_id'] = "";
		$this->datamodel['customer_first_name'] = "";
		$this->datamodel['customer_last_name'] = "";
		$this->datamodel['customer_email'] = "";
		$this->datamodel['product_name'] = "";
		$this->datamodel['product_id'] = "0";
		$this->datamodel['variant_id'] = "0";
		$this->datamodel['variant_title'] = "";
		$this->datamodel['title'] = "";
	}
}
