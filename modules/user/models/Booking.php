<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class Booking extends DataModel{

	public function __construct(){
		$this->table_name = 'bookings';
		$this->primary_key = 'id';

		$this->datamodel['id']=0;
		$this->datamodel['seller']=0;
		$this->datamodel['customer']=0;
		$this->datamodel['availability']="";
		$this->datamodel['locations']="";
		$this->datamodel['suggestedAvailability']="";
		$this->datamodel['suggestedLocations']="";
		$this->datamodel['sellerCode']="";
		$this->datamodel['customerCode']="";
		$this->datamodel['status']=0;         // 0 - start, 1 - confirm, 2 - declined, 3 - expired, 4 - started, 5 - meeting code by provider, 6 - meeting code by taker, 7 - meeting code by both(complete)
	}

}